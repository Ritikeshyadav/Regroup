<?php

namespace App\Services\APIs;

use App\Models\SpaceFolderListTaskLink;
use App\Models\TaskDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TaskDocumentApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To add  task document service
     */
    public function addTaskDocumentService(Request $request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'space_folder_list_task_link_xid' => 'required|numeric',
                'doc_name' => 'required',
                'file' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $space_folder_list_task_link_xid = SpaceFolderListTaskLink::where('id', $request->space_folder_list_task_link_xid)->first();
            if (!$space_folder_list_task_link_xid) {
                return jsonResponseWithErrorMessageApi(__('space_folder_list_task_link_xid not found'), 422);
            }

            $file = $request->file('file');
            $filePath = saveSingleImageWithoutCrop($file, 'task_documents');

            $taskDocument = TaskDocument::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'space_folder_list_task_link_xid' => $space_folder_list_task_link_xid->id,
                    'iam_principal_xid' => $iamprincipal_id,
                    'doc_name' => $request->doc_name,
                    'file' => $filePath,
                ]
            );
            DB::commit();
            $responseData['taskDocument'] = $taskDocument;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add task document service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To fetch task document listing service
     */
    public function fetchTaskDocumentService()
    {
        try {
            $data = TaskDocument::select('id', 'space_folder_list_task_link_xid', 'iam_principal_xid', 'doc_name', 'file')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('task document data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            foreach ($data as $k => $val) {
                $data[$k]['file'] = ListingImageUrl('task_documents', $val['file']);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch task document service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To delete task document service
     */
    public function deleteTaskDocumentService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $data = TaskDocument::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }
            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Task Document failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
