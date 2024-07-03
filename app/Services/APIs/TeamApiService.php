<?php

namespace App\Services\APIs;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TeamApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To add team service
     */
    public function addTeamService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $team = Team::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'iam_principal_xid' => $iamprincipal_id,
                    'name' => $request->name,
                ]
            );
            DB::commit();
            $responseData['team'] = $team;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add team service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To fetch team listing service
     */
    public function fetchTeamService()
    {
        try {
            $data = Team::select('id', 'iam_principal_xid', 'name')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('team data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch team service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To delete team service
     */
    public function deleteTeamService($request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            $data = Team::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }
            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete team service failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
