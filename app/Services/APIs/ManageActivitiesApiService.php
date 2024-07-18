<?php

namespace App\Services\APIs;

use App\Models\ManageActivities;
use App\Models\SpaceFolderListTaskLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\ManageActivity;
use App\Models\IamPrincipalManageAcivitiesLink;

class ManageActivitiesApiService
{
    /**
     * Created By : Vedant Chavan
     * Created At : 04 July 2024
     * Use : To get Activities Service
     */

    public function getActivitiesService(){
        try {
            DB::beginTransaction();
            $activities = ManageActivity::where('is_active',1)->get();
            DB::commit();
            $responseData['activities'] = $activities;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add manage activities service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Vedant Chavan
     * Created At : 04 July 2024
     * Use : Store Activities to the user Service
     */

    public function storeActivitiesService(Request $request,$iamprincipal_id){
        try {
            DB::beginTransaction();

                $activities_id = json_decode($request->input('activities_id'), true);

                foreach($activities_id as $value){
                    IamPrincipalManageAcivitiesLink::create([
                        'iam_principal_xid' => $iamprincipal_id,
                        'manage_activity_xid' => $value
                    ]);
                }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add manage activities service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    public function addActivitiesService(Request $request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'space_folder_list_task_link_xid' => 'required|numeric',
                'title' => 'required',
                'date_time' => 'required|date_format:Y-m-d',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 403);
            }

            // $space_folder_list_task_link_xid = SpaceFolderListTaskLink::where('id', $request->space_folder_list_task_link_xid)->first();
            $space_folder_list_task_link_xid = false;
            if (!$space_folder_list_task_link_xid) {
                return jsonResponseWithErrorMessageApi(__('space_xid not found'), 422);
            }

            // $activities = ManageActivities::Create([
            //     'iam_principal_xid' => $iamprincipal_id,
            //     'space_folder_list_task_link_xid' => $space_folder_list_task_link_xid->id,
            //     'title' => $request->title,
            //     'date_time' => $request->date_time,
            // ]);
            DB::commit();
            // $responseData['activities'] = $activities;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add manage activities service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To fetch activities listing service
     */
    public function fetchActivitiesService()
    {
        try {
            $data = ManageActivities::select('id', 'space_folder_list_task_link_xid', 'iam_principal_xid', 'title', 'date_time')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('manage activities data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('manage activities service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
