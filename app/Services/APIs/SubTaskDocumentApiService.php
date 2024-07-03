<?php

namespace App\Services\APIs;

use App\Models\SubTask;
use App\Models\SubTaskDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SubTaskDocumentApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To add sub task document service
     */
    public function addSubTaskDocumentService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'sub_task_xid' => 'required',
                'doc_name' => 'required',
                'file' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $sub_task_xid = SubTask::where('id', $request->sub_task_xid)->first();
            if (!$sub_task_xid) {
                return jsonResponseWithErrorMessageApi(__('subtask_xid not found'), 422);
            }

            $file = $request->file('file');
            $filePath = saveSingleImageWithoutCrop($file, 'sub_task_documents');

            $subtaskDocument = SubTaskDocument::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'sub_task_xid' => $sub_task_xid->id,
                    'iam_principal_xid' => $iamprincipal_id,
                    'doc_name' => $request->doc_name,
                    'file' => $filePath,
                ]
            );
            DB::commit();
            $responseData['subtaskDocument'] = $subtaskDocument;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add sub task document service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To fetch sub task document listing service
     */
    public function fetchSubTaskDocumentService()
    {
        try {
            $data = SubTaskDocument::select('id', 'sub_task_xid', 'iam_principal_xid', 'doc_name', 'file')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('sub task document data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            foreach ($data as $k => $val) {
                $data[$k]['file'] = ListingImageUrl('sub_task_documents', $val['file']);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch sub task document service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To delete sub task document service
     */
    public function deleteSubTaskDocumentService($request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $data = SubTaskDocument::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }
            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete sub Task Document failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
