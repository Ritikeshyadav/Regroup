<?php

namespace App\Services\APIs;

use App\Models\Space;
use App\Models\SpaceFolderLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpaceFolderApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 26 March 2024
     * Use : To show space folder list
     */
    public function fetchSpaceFolderService()
    {
        try {
            $spaceFolderData = SpaceFolderLink::select('id', 'folder_name')->get();
            if ($spaceFolderData == null) {
                Log::info('Space folder data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 200);
            }
            $responseData['result'] = $spaceFolderData;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 26 March 2024
     * Use : To add space folder
     */
    public function addSpaceFolderService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'folder_name' => 'required',
                'space_xid' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $space_xid = Space::where('id', $request->space_xid)->first();
            if (!$space_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 422);
            }

            $createFolder = SpaceFolderLink::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'folder_name' => $request->folder_name,
                    'space_xid' => $space_xid->id,
                ]
            );
            DB::commit();
            $responseData['folder'] = $createFolder;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add space folder service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 26 March 2024
     * Use : To delete space folder
     */
    public function deleteSpaceFolderService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $spaceFolder = SpaceFolderLink::find($request->id);
            if (!$spaceFolder) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }
            $spaceFolder->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $spaceFolder, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
