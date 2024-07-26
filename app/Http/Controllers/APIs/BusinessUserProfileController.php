<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\IamPrincipal;
use App\Models\IamRole;
use App\Models\IamPrincipalOtp;
use App\Services\APIs\BusinessProfileDetailsApiService;
use App\Services\APIs\AuthApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BusinessUserProfileController extends Controller
{
    protected $BusinessProfileDetailsApiService;
    public function __construct(BusinessProfileDetailsApiService $BusinessProfileDetailsApiService)
    {
        $this->BusinessProfileDetailsApiService = $BusinessProfileDetailsApiService;
    }

    /**
     * Created By : Hritik Yadav
     * Created at : 09 July 2024
     * Use : To Store Tell Us About Business Details
     */

    public function tellUsAboutYourBusiness(Request $request)
    {

        try {
            $token = readHeaderToken();
            if ($token) {

                $validator = $this->validateTellUsAboutFormOfBusinessUser($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Tell Us About YourSelf of business form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                return $this->BusinessProfileDetailsApiService->tellUsAboutYourBusinessFormService($request, $iamprincipal_id);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Hritik Yadav
     * Created at : 09 July 2024
     * Use : To Update Business Profile at step 1
     */
    public function updateBusinessProfile(Request $request)
    {

        try {
            $token = readHeaderToken();
            if ($token) {

                $validator = $this->vallidateUpdateBusinessProfileForm($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update Business Profile form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                $user_id = $token['sub'];
                $request['iam_principal_xid'] = $user_id;

                return $this->BusinessProfileDetailsApiService->updateBusinessProfileStepOneService($request);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : TO fetch bussiness profile
     */
    public function fetchBusinessProfile()
    {
        try {
            $token = readHeaderToken();
            // dd($token['sub']);
            return $token ? $this->BusinessProfileDetailsApiService->fetchBusinessProfileService($token['sub'],null) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('Fetch bussiness profile function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : TO update bussiness profile
     */
    public function updateBusinessProfileFunction(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $validator = validator::make($request->all(), [
                    'business_name' => 'required',
                    'business_username' => 'required',
                    'business_owner_name' => 'required',
                    'founded_on' => 'required',
                    'website_link' => 'required',
                    'business_location' => 'required',
                    'des' => 'required',
                    'business_profile' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
                ]);

                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update Business Profile validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                return $this->BusinessProfileDetailsApiService->updateBusinessProfile($token['sub'], $request);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('Update business profile function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : TO send mail otp for update password
     */
    public function updatePasswordSendMailOtp(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $validator = validator::make($request->all(), [
                    'current_password' => 'required',
                    'new_password' => 'required|required_with:confirm_password|same:confirm_password|different:current_password',
                ]);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update Business Profile validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }

                // getting user password for checking purpose
                $userData = IamPrincipal::where('id', $token['sub'])->first();

                // checking new password and old password
                if (!Hash::check($request->current_password, $userData->password_hash)) {
                    Log::error("Update Business Profile validation error: password not matched");
                    return jsonResponseWithErrorMessageApi('password not matched', 403);
                }

                return $this->BusinessProfileDetailsApiService->sendMailOtpForUpdatePasswordService($userData->email_address, $token['sub']);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);

        } catch (Exception $e) {
            Log::error('Send mail otp for update password function failed: ' . $e->getMessage());
            return jsonResponseWithSuccessMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function verifyUpdatePasswordOtp(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $validator = Validator::make($request->all(), [
                    'otp' => 'required',
                    'new_password' => 'required',
                ]);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update Business Profile validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $storedOtp = IamPrincipalOtp::where("principal_xid", $token['sub'])->first();
                if (!$storedOtp || carbon::now() > $storedOtp->valid_till || $storedOtp->otp_code != $request->otp || $storedOtp->is_used == 1) {
                    $validationErrors = !$storedOtp ? 'OTP not found!' : (carbon::now() > $storedOtp->valid_till ? 'OTP has been expired!' : ($storedOtp->otp_code != $request->otp ? 'OTP not matched!' : 'OTP is already used!'));
                    Log::error("Update Business Profile validation error: " . $validationErrors);
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                return $this->BusinessProfileDetailsApiService->verifyOtpForUpdatePasswordService($token['sub'], $request, $storedOtp);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('Verify otp for update password function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function validateTellUsAboutFormOfBusinessUser(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'business_name' => 'required',
                'business_owner_name' => 'required',
                'business_location' => 'required',
                'business_type_xid' => 'required'

            ],
        );
    }
    public function vallidateUpdateBusinessProfileForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'business_contact_number' => 'required|integer|digits:10',
                'business_email' => 'required|email|max:50',//regex:/^[\w\.\-]+@([\w\-]+\.)+[\w\-]{2,4}$/
                'business_handle' => 'required',
                'opening_hours' => 'required',
                'website_link' => 'required',
                'google_review_link' => 'required',
                'tags' => 'required',
                'business_logo' => 'required',
                'banner_image' => 'required',

            ],
        );
    }




}
