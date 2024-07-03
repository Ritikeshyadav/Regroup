<?php

namespace App\Services\APIs;

use App\Models\PriorityMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class PriorityMasterService
{

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show task status list
     */
    public function taskPriority()
    {
        try {
            DB::beginTransaction();
            $taskPriorityData = PriorityMaster::select('id', 'title')->get();
            DB::commit();
            $responseData['user'] = $taskPriorityData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Task Priority function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add Priority  list
     */
    public function addPriority(Request $request)
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

            $existingPriority = PriorityMaster::where('title', $request->input('title'))->first();

            if ($existingPriority) {
                return jsonResponseWithErrorMessageApi('Priority with the same name already exists', 400);
            }

            $priorityData = PriorityMaster::create([
                'title' => $request->input('title'),
                'colors_xid' => $request->input('colors_xid')
            ]);
            // dd($priorityData);

            DB::commit();
            $responseData['user'] = $priorityData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add Priority function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To delete priority 
     */
    public function deletePriority(Request $request)
    {
        try {
            DB::beginTransaction();
            $priority = PriorityMaster::find($request->id);

            if (!$priority) {
                return jsonResponseWithErrorMessageApi(__('error.priority_not_found'), 404);
            }

            $priority->delete();
            DB::commit();

            $responseData['deleted_status_id'] = $request->id; // Provide feedback about the deleted priority ID
            return jsonResponseWithSuccessMessageApi(__('success.delete_data'), $responseData, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete priority function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
