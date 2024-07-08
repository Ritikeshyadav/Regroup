<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\ProfileDetailsApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileDetailsApiController extends Controller
{
    protected $ProfileDetailsApiService;
    public function __construct(ProfileDetailsApiService $ProfileDetailsApiService)
    {
        $this->ProfileDetailsApiService = $ProfileDetailsApiService;
    }

    /**
     * Created By : Vedant Chavan
     * Created At : 03 July 2024
     * Use : To add profile details
     */
    public function addProfile(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                
                $validator = $this->validateUserDetails($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Registration form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->addProfileDetailService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To role master listing 
     */
    public function fetchRole(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->fetchRoleService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch role master function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To update profile  
     */
    public function updateProfile(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->updateProfileService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('update profile function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To delete profile  
     */
    public function deleteProfile(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->deleteProfileService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete profile controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    /**
     * Created By : Vedant Chavan
     * Created at : 03 July 2024
     * Use : To validate Profile User Data
     */

    public function validateUserDetails(Request $request){
        return Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'username' => 'required',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender' => 'required',
                'profile_photo' => 'required',
                'location' => 'required',
                
            ],
        );
    }

}
