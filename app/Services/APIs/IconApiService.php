<?php

namespace App\Services\APIs;

use App\Models\Colors;
use App\Models\IconMaster;
use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class IconApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To show icons  list
     */
    public function fetchIconsService()
    {
        try {
            $iconsData = IconMaster::select('id', 'name', 'image')->get();
            if ($iconsData == null) {
                Log::info('icon data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 200);
            }
            foreach ($iconsData as $k => $val) {
                $iconsData[$k]['image'] = ListingImageUrl('icons_image', $val['image']);
            }
            $responseData['result'] = $iconsData;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch colors function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }




    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To add icons  list
     */
    public function addIconsService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'required|image'
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $image = $request->file('image');
            $imagePath = saveSingleImageWithoutCrop($image, 'icons_image');
            $iconsData = IconMaster::create([
                'name' => $request->input('name'),
                'image' => $imagePath
            ]);

            DB::commit();
            $responseData['icon'] = $iconsData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add icons service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
