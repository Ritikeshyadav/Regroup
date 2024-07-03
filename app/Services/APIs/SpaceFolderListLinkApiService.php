<?php

namespace App\Services\APIs;

use App\Models\Colors;
use App\Models\PriorityMaster;
use App\Models\Space;
use App\Models\SpaceFolderLink;
use App\Models\SpaceFolderListLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpaceFolderListLinkApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To add space folder list link
     */
    public function addSpaceFolderListService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'space_xid' => 'required',
                'color_xid' => 'required',
                'space_folder_link_xid' => 'required',
                'priority_xid' => 'required',
                'list_name' => 'required',
                'description' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $space_xid = Space::where('id', $request->space_xid)->first();
            if (!$space_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 422);
            }

            $color_xid = Colors::where('id', $request->color_xid)->first();
            if (!$color_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 422);
            }

            $space_folder_link_xid = SpaceFolderLink::where('id', $request->space_folder_link_xid)->first();
            if (!$space_folder_link_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 422);
            }

            $priority_xid = PriorityMaster::where('id', $request->priority_xid)->first();
            if (!$priority_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 422);
            }

            $spaceFolderListLink = SpaceFolderListLink::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'space_xid' => $space_xid->id,
                    'color_xid' => $color_xid->id,
                    'space_folder_link_xid' => $space_folder_link_xid->id,
                    'priority_xid' => $priority_xid->id,
                    'list_name' => $request->list_name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]
            );
            DB::commit();
            $responseData['spaceFolderListLink'] = $spaceFolderListLink;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add space folder list link service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To show space folder list link
     */
    public function fetchSpaceFolderListLinkService()
    {
        try {
            $spaceFolderListLinkData = SpaceFolderListLink::select('id', 'list_name', 'description', 'start_date', 'end_date')->get();

            if ($spaceFolderListLinkData == null) {
                Log::info('Space folder list link data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 200);
            }
            $responseData['result'] = $spaceFolderListLinkData;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To delete space folder list link
     */
    public function deleteSpaceFolderListService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $spaceFolderListLink = SpaceFolderListLink::find($request->id);
            if (!$spaceFolderListLink) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }
            $spaceFolderListLink->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $spaceFolderListLink, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}