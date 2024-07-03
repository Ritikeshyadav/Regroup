<?php

namespace App\Services\APIs;

use App\Models\ManageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class ManageProductService
{

    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To add product 
     */
    public function addProduct(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'product_name' => 'required',
                'product_description' => 'required',
                'image' => 'required|image',
                'product_monthly_price' => 'required|numeric',
                'product_yearly_price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->first(), 400);
            }

            $image = $request->file('image');

            $imagePath = saveSingleImageWithoutCrop($image, 'product_image');

            $productData = ManageProduct::create([
                'product_name' => $request->input('product_name'),
                'product_description' => $request->input('product_description'),
                'image' => $imagePath,
                'product_monthly_price' => $request->input('product_monthly_price'),
                'product_yearly_price' => $request->input('product_yearly_price'),

            ]);

            Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));

            $stripeProduct = \Stripe\Product::create([
                'name' => $productData->product_name,
                'description' => $productData->product_description,
                'type' => 'service',
            ]);

            $productData->stripe_product_id = $stripeProduct->id;
            $productData->save();

            $monthlyPrice = \Stripe\Price::create([
                'product' => $stripeProduct->id,
                'unit_amount' => $request->product_monthly_price * 100,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],
            ]);

            $yearlyPrice = \Stripe\Price::create([
                'product' => $stripeProduct->id,
                'unit_amount' => $request->product_yearly_price * 100,
                'currency' => 'usd',
                'recurring' => ['interval' => 'year'],
            ]);

            $productData->stripe_monthly_price_id = $monthlyPrice->id;
            $productData->stripe_yearly_price_id = $yearlyPrice->id;
            $productData->save();

            DB::commit();
            $responseData = $productData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add Product function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To fetch  product list
     */
    public function fetchProduct()
    {
        try {
            DB::beginTransaction();
            $productData = ManageProduct::select('id', 'product_name', 'product_description', 'image', 'product_monthly_price', 'product_yearly_price', 'stripe_product_id', 'stripe_monthly_price_id', 'stripe_yearly_price_id', 'is_popular')->get();
            foreach ($productData as $k => $val) {
                $productData[$k]['image'] = ListingImageUrl('product_image', $val['image']);
            }
            DB::commit();
            $responseData = $productData;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('fetch product function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }



    /**
     * Created By : Ravindra Gawade
     * Created At : 19 March 2024
     * Use : To edit product
     */
    public function editProduct(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = ManageProduct::find($request->id);

            if (!$product) {
                return jsonResponseWithErrorMessageApi(__('product.not_found'), 404);
            }

            $validator = Validator::make($request->all(), [
                'product_name' => 'required',
                'product_description' => 'required',
                'image' => 'image',
                'product_monthly_price' => 'required|numeric',
                'product_yearly_price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->first(), 400);
            }

            $updateData = [
                'product_name' => $request->input('product_name', $product->product_name),
                'product_description' => $request->input('product_description', $product->product_description),
                // 'product_monthly_price' => $request->input('product_monthly_price', $product->product_monthly_price),
                // 'product_yearly_price' => $request->input('product_yearly_price', $product->product_yearly_price),
            ];

            if ($request->hasFile('image')) {
                $updateData['image'] = saveSingleImageWithoutCrop($request->file('image'), 'product_image');
            }

            $product->update($updateData);

            $stripe = new \Stripe\StripeClient(config('constants.STRIPE_SECRET_KEY'));

            $stripe->products->update($product->stripe_product_id, [
                'name' => $product->product_name,
                'description' => $product->product_description,
            ]);

            $existProductItem = ManageProduct::where('id', $request->id)->first();

            // ========================================for monthly==============================================

            if ($existProductItem->product_monthly_price != $request->input('product_monthly_price')) {
                $productPrice1 = $stripe->prices->create([
                    'unit_amount' => $request->input('product_monthly_price') * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => 'month'],
                    'product' => $existProductItem->stripe_product_id,

                ]);

                $updateCreatedMarekt = ManageProduct::where('id', $request->id)->update([
                    'product_monthly_price' => $request->input('product_monthly_price'),
                    'stripe_monthly_price_id' => $productPrice1->id
                ]);
                $subscriptionList = $stripe->subscriptions->all(['price' => $product->stripe_monthly_price_id]);

                foreach ($subscriptionList as $subscription) {
                    $items = $stripe->subscriptionItems->all(['subscription' => $subscription->id]);
                    foreach ($items as $item) {
                        $stripe->subscriptionItems->delete($item->id);
                    }

                    $stripe->subscriptionItems->create([
                        'subscription' => $subscription->id,
                        'price' => $productPrice1->id,
                    ]);
                }

                $stripe->prices->update($product->stripe_monthly_price_id, ['active' => false]);
            }

            //======================================== for yearly=================================
            if ($existProductItem->product_yearly_price != $request->input('product_yearly_price')) {
                $productPrice2 = $stripe->prices->create([
                    'unit_amount' => $request->input('product_yearly_price') * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => 'year'],
                    'product' => $existProductItem->stripe_product_id, // with product ID

                ]);

                $updateCreatedMarekt = ManageProduct::where('id', $request->id)->update([
                    'product_yearly_price' => $request->input('product_yearly_price'),
                    'stripe_yearly_price_id' => $productPrice2->id
                ]);

                $subscriptionList = $stripe->subscriptions->all(['price' => $product->stripe_yearly_price_id]);


                foreach ($subscriptionList as $subscription) {
                    $items = $stripe->subscriptionItems->all(['subscription' => $subscription->id]);
                    foreach ($items as $item) {
                        $stripe->subscriptionItems->delete($item->id);
                    }

                    $stripe->subscriptionItems->create([
                        'subscription' => $subscription->id,
                        'price' => $productPrice2->id,
                    ]);
                }

                $stripe->prices->update($product->stripe_yearly_price_id, ['active' => false]);
            }
            DB::commit();
            $updatedProduct = ManageProduct::find($request->id);
            $responseData = $updatedProduct;
            return jsonResponseWithSuccessMessageApi(__('success.product updated successfully'), $responseData, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('edit Product function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To delete product
     */
    public function deleteProduct(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = ManageProduct::find($request->id);
            if (!$product) {
                return jsonResponseWithErrorMessageApi(__('Product not found'), 404);
            }

            Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));
            \Stripe\Price::update($product->stripe_monthly_price_id, ['active' => false]);
            \Stripe\Price::update($product->stripe_yearly_price_id, ['active' => false]);

            try {
                \Stripe\Product::update($product->stripe_product_id, ['active' => false]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                Log::error('Failed to delete product from Stripe: ' . $e->getMessage());
            }
            $product->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('Product deleted successfully'), [], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete product function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
