<?php

namespace App\Services\APIs;

use App\Models\IamPrincipal;
use App\Models\IamRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Hash;

class ProfileDetailsApiService
{
    /**
     * Created By : Vedant Chavan
     * Created At : 03 July 2024
     * Use : To add profile details Service
     */
    public function addProfileDetailService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();

            $profilePhoto = $request->file('profile_photo');
            $profilePath = saveSingleImageWithoutCrop($profilePhoto, 'profile_photos');

            $profileData = IamPrincipal::updateOrCreate(
                ['id' => $iamprincipal_id],
                ['full_name' => $request->full_name,
                 'username' => $request->username,
                 'date_of_birth' => $request->date_of_birth,
                 'gender' => $request->gender,
                 'address_line1' => $request->location,
                 'profile_photo' => $profilePath,
                 'is_profile_updated'=>1
                ]
            );
            DB::commit();
            $responseData['profile'] = $profileData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add profile details service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To fetch role master listing service
     */
    public function fetchRoleService()
    {
        try {
            $data = IamRole::select('id', 'role_name')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('role master data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch role master service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To update profile details service
     */
    public function updateProfileService($iamprincipal_id, $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
                'password' => 'required',
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $data = IamPrincipal::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            $data->update([
                'email_address' => $request->email_address,
                'password_hash' => Hash::make($request->password),
            ]);
            DB::commit();
            $responseData['profile'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('update profile details service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To delete profile service
     */
    public function deleteProfileService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            if ($request->id != $iamprincipal_id) {
                return jsonResponseWithErrorMessageApi(__('auth.unauthorized_action'), 403);
            }

            $data = IamPrincipal::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete profile data failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
