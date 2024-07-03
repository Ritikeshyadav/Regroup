<?php

namespace App\Services\APIs;


use App\Models\PriorityMaster;
use App\Models\Space;
use App\Models\SpaceFolderListLink;
use App\Models\SpaceFolderListTaskLink;
use App\Models\SpaceListLink;
use App\Models\StatusMaster;
use App\Models\SubTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubTaskApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 02 April 2024
     * Use : To add sub task service
     */
    public function addSubTaskService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            // $validator = Validator::make($request->all(), [
            //     'space_folder_list_task_link_xid' => 'required',
            //     'iam_principal_xid' => 'required',
            //     'name' => 'required',
            //     'description' => 'required',
            //     'start_date' => 'required',
            //     'due_date' => 'required',
            //     'priority_master_xid' => 'required',
            //     'comment' => 'required',
            //     'status_master_xid' => 'required',

            // ]);

            // if ($validator->fails()) {
            //     return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            // }

            $space_folder_list_task_link_xid = SpaceFolderListTaskLink::where('id', $request->space_folder_list_task_link_xid)->first();
            if (!$space_folder_list_task_link_xid) {
                return jsonResponseWithErrorMessageApi(__('space_xid not found'), 422);
            }

            $status_master_xid = StatusMaster::where('id', $request->status_master_xid)->first();
            if (!$status_master_xid) {
                return jsonResponseWithErrorMessageApi(__('status_master_xid data_not_found'), 422);
            }

            $priority_master_xid = PriorityMaster::where('id', $request->priority_master_xid)->first();
            if (!$priority_master_xid) {
                return jsonResponseWithErrorMessageApi(__('priority_master_xid data_not_found'), 422);
            }


            $subTask = SubTask::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'space_folder_list_task_link_xid' => $space_folder_list_task_link_xid->id,
                    'iam_principal_xid' => $iamprincipal_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'due_date' => $request->due_date,
                    'priority_master_xid' => $priority_master_xid->id,
                    'comment' => $request->comment,
                    'status_master_xid' => $status_master_xid->id,
                    'is_group_assignee' => 1,
                ]
            );
            DB::commit();
            $responseData['subTask'] = $subTask;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add sub task service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 15 April 2024
     * Use : To fetch sub task listing service
     */
    public function fetchSubTaskService($iamprincipal_id, $request)
    {
        try {
            $query = SubTask::select('id', 'name', 'description', 'start_date', 'due_date', 'comment', 'status_master_xid', 'priority_master_xid')
                ->with([
                    'statusmaster' => function ($query) {
                        $query->select('id', 'title');
                    }
                ])
                ->with([
                    'priority' => function ($query) {
                        $query->select('id', 'title');
                    }
                ])
                ->where([['is_active', 1]]);

            $search = $request->input('search');
            if ($search) {
                $columns = 'name|description';
                fullSearchQuery($query, $search, $columns);
            }

            $sub_task_data = $query->get();
            if ($sub_task_data == null) {
                Log::info('Sub task data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $sub_task_data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch sub task function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 02 April 2024
     * Use : To delete sub task service
     */
    public function deleteSubTaskService($request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $data = SubTask::find($request->id);

            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            // Delete relationships
            $data->delete_space_folder_list_task_link()->delete();
            $data->delete_iam_principal()->delete();

            // Now delete the main entity
            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Sub Task failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
