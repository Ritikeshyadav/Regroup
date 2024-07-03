<?php

namespace App\Services\APIs;

use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class StatusMasterService
{

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show task status list
     */
    public function taskStatus()
    {
        try {
            DB::beginTransaction();
            $taskStatusData = StatusMaster::select('id', 'title')->get();
            DB::commit();
            $responseData['user'] = $taskStatusData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Task Status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add colors  list
     */
    public function addStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = validator::make($request->all(), [
                'title' => 'required',
                'colors_xid' => 'required'
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->first(), 400);
            }

            $existingStatus = StatusMaster::where('title', $request->input('title'))->first();

            if ($existingStatus) {
                return jsonResponseWithErrorMessageApi('Status with the same name already exists', 400);
            }

            $statusData = StatusMaster::create([
                'title' => $request->input('title'),
                'colors_xid' => $request->input('colors_xid')
            ]);

            DB::commit();
            $responseData['user'] = $statusData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To delete status 
     */
    public function deleteStatus(Request $request)
    {
        try {
            DB::beginTransaction();

            $status = StatusMaster::find($request->id);

            if (!$status) {
                return jsonResponseWithErrorMessageApi(__('error.status_not_found'), 404);
            }

            $status->delete();
            DB::commit();

            $responseData['deleted_status_id'] = $request->id; // Provide feedback about the deleted status ID
            return jsonResponseWithSuccessMessageApi(__('success.delete_data'), $responseData, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
