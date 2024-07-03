<?php

namespace App\Services\APIs;

use App\Models\Colors;
use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class ColorsService
{
    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show colors  list
     */
    public function fetchColors()
    {
        try {
            DB::beginTransaction();

            $colorsData = Colors::select('id', 'name', 'image')->get();
            foreach ($colorsData as $k => $val) {
                $colorsData[$k]['image'] = ListingImageUrl('colors_image', $val['image']);
            }
            DB::commit();
            $responseData['user'] = $colorsData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('fetch colors function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add colors  list
     */
    public function addColors(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'required|image'
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->first(), 400);
            }

            $existingColor = Colors::where('name', $request->input('name'))->first();

            if ($existingColor) {
                return jsonResponseWithErrorMessageApi('Color with the same name already exists', 400);
            }

            $image = $request->file('image');

            $imagePath = saveSingleImageWithoutCrop($image, 'colors_image');

            $colorsData = Colors::create([
                'name' => $request->input('name'),
                'image' => $imagePath
            ]);

            DB::commit();
            $responseData['user'] = $colorsData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add colors function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
