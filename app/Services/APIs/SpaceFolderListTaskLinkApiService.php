<?php

namespace App\Services\APIs;

use App\Events\SendNotification;
use App\Models\PriorityMaster;
use App\Models\Space;
use App\Models\SpaceFolderListLink;
use App\Models\SpaceFolderListTaskLink;
use App\Models\SpaceListLink;
use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpaceFolderListTaskLinkApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 01 April 2024
     * Use : To add space folder list task link
     */
    public function addSpaceFolderListTaskService(Request $request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            // $validator = Validator::make($request->all(), [
            //     'space_folder_list_link_xid' => 'required',
            //     'space_xid' => 'required',
            //     'space_list_link_xid' => 'required',
            //     'iam_principal_xid' => 'required',
            //     'name' => 'required',
            //     'description' => 'required',
            //     'start_date' => 'required',
            //     'due_date' => 'required',
            //     'number' => 'required',
            //     'cover_image' => 'required',
            //     'priority_master_xid' => 'required',
            //     'comment' => 'required',
            //     'status_master_xid' => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            // }

            $space_xid = Space::where('id', $request->space_xid)->first();
            if (!$space_xid) {
                return jsonResponseWithErrorMessageApi(__('space_xid not found'), 422);
            }

            $status_master_xid = StatusMaster::where('id', $request->status_master_xid)->first();
            if (!$status_master_xid) {
                return jsonResponseWithErrorMessageApi(__('status_master_xid data_not_found'), 422);
            }

            $space_list_link_xid = SpaceListLink::where('id', $request->space_list_link_xid)->first();
            if (!$space_list_link_xid) {
                return jsonResponseWithErrorMessageApi(__('space_list_link_xid data_not_found'), 422);
            }

            $priority_master_xid = PriorityMaster::where('id', $request->priority_master_xid)->first();
            if (!$priority_master_xid) {
                return jsonResponseWithErrorMessageApi(__('priority_master_xid data_not_found'), 422);
            }

            $spacefolderlistlink_xid = SpaceFolderListLink::where('id', $request->space_folder_list_link_xid)->first();
            if (!$spacefolderlistlink_xid) {
                return jsonResponseWithErrorMessageApi(__('spacefolderlistlink_xid data_not_found'), 422);
            }

            $randomString = Str::random(5);
            $uniqueId = "task#{$randomString}";

            $spaceFolderListTaskLink = SpaceFolderListTaskLink::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'space_folder_list_link_xid' => $spacefolderlistlink_xid->id,
                    'tasks_id' => $uniqueId,
                    'space_xid' => $space_xid->id,
                    'space_list_link_xid' => $space_list_link_xid->id,
                    'iam_principal_xid' => $iamprincipal_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'due_date' => $request->due_date,
                    'number' => $request->number,
                    'cover_image' => $request->cover_image,
                    'priority_master_xid' => $priority_master_xid->id,
                    'comment' => $request->comment,
                    'status_master_xid' => $status_master_xid->id,
                ]
            );

            $data = [
                'iamprincipal_xid' => $iamprincipal_id,
                'title' => 'New Task Added',
                'description' => 'New Task Added has been successfully listed.'
            ];
            event(new SendNotification($data));
            DB::commit();
            //response data
            Log::info('Task Added form data Created successfully');
            $responseData['spaceFolderListTaskLink'] = $spaceFolderListTaskLink;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add space folder list task link service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 12 April 2024
     * Use : To show space folder list link
     */
    public function fetchSpaceFolderListTaskLinkService($iamprincipal_id, $request)
    {
        try {

            $query = SpaceFolderListTaskLink::select('id', 'tasks_id', 'name', 'description', 'start_date', 'due_date', 'comment', 'status_master_xid', 'priority_master_xid')
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
                $columns = 'name';
                fullSearchQuery($query, $search, $columns);
            }

            $task_data = $query->get();
            if ($task_data == null) {
                Log::info('Task data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $task_data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 01 April 2024
     * Use : To delete space folder list link
     */
    public function deleteSpaceFolderListTaskService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $data = SpaceFolderListTaskLink::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            // Delete relationships
            $data->delete_space_folder_list_link()->delete();
            $data->delete_space()->delete();
            $data->delete_space_list_link()->delete();
            $data->delete_iam_principal()->delete();

            // Now delete the main entity
            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 11 April 2024
     * Use : To show listing of task based on their status
     */
    public function listingTaskBasedonStatusService($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $status_xid = $request->input('status');
            
            $tasks = SpaceFolderListTaskLink::when($status_xid, function ($query) use ($status_xid) {
                return $query->where('status_master_xid', $status_xid);
            })
                ->with([
                    'statusmaster' => function ($query) {
                        $query->select('id', 'title');
                    }
                ])->get();

            if ($tasks->isEmpty()) {
                Log::info('Tasks data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $tasks;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('listing of task based on status service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}