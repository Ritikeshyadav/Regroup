<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\AuthApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
class AuthApiController extends Controller
{

    //constructor of auth service
    protected $AuthApiService;
    public function __construct(AuthApiService $AuthApiService)
    {
        $this->AuthApiService = $AuthApiService;
    }

    /**
     * Created By : Vedant Chavan
     * Created at : 01 July 2024
     * Use : To send otp
     */
    public function sendOtp(Request $request)
    {

        try {
            $validator = $this->validateRegistrationForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Registration form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->sendOtpService($request);
        } catch (Exception $e) {
            Log::error('Registration form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Vedant Chavan
     * Created at : 01 July 2024
     * Use : check otp and register
     */
    public function verifykOtp(Request $request)
    {
        try {
            $validator = $this->validateRegistrationForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Registration form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->verifyOtpService($request);
        } catch (Exception $e) {
            Log::error('Registration form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created at : 14 March 2024
     * Use : To store registration form data into table
     */
    public function registrationForm(Request $request)
    {
        try {
            $validator = $this->validateRegistrationForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Registration form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->registrationFormService($request);
        } catch (Exception $e) {
            Log::error('Registration form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Vedant Chavan
     * Created at : 03 July 2024
     * Use : To validate registration form data
     */
    public function validateRegistrationForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'email_address' => 'required|email|max:50|unique:iam_principal',
                'password' => ['required', 'string', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            ],
            [
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and minimum 8 and maximum 20 characters long.'
            ]
        );
    }

    /**
     * Created By : Vedant Chavan
     * Created at : 03 July 2024
     * Use : To validate User Profile Data
     */
    public function validateUserDetails(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'username' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
                'profile_photo' => 'required',
                'location' => 'required',

            ],
        );
    }

    /**
     * Created By : Chandan Yadav
     * Created at : 14 March 2024
     * Use : To store login form data into table
     */
    public function login(Request $request)
    {
        try {
            $validator = $this->validateLoginForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Login form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->loginFormService($request);
        } catch (Exception $e) {
            Log::error('Login form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 14 March 2024
     * Use : To validate login form data
     */
    public function validateLoginForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'email_address' => 'required|email|max:50',
                'password' => ['required', 'string', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            ],
        );
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 14 March 2024
     * Use : To forgot password send otp
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = $this->validateForgotPasswordForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Forgot password form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->forgotPasswordFormService($request);
        } catch (Exception $e) {
            Log::error('Forgot password form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 14 March 2024
     * Use : To validate forgot password form data
     */
    public function validateForgotPasswordForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'email_address' => 'required|exists:iam_principal,email_address',
            ],
        );
    }

    /**
     * Created By : Chandan Yadav
     * Created at : 15 March 2024
     * Use : To verify forgot password send otp
     */
    public function verifyOtpForgotPassword(Request $request)
    {
        try {
            $validator = $this->validateVerifyOtpForgotPasswordForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Verify Forgot password form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->verifyOtpForgotPasswordFormService($request);
        } catch (Exception $e) {
            Log::error('Verify Forgot password form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 15 March 2024
     * Use : To validate verify forgot password otp form data
     */
    public function validateVerifyOtpForgotPasswordForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'iam_principal_xid' => 'required|exists:iam_principal,id',
                'otp' => 'required',
            ],
        );
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 18 March 2024
     * Use : To verify resend send otp
     */
    public function resendOtp(Request $request)
    {
        try {
            $validator = $this->validateResendOtp($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Verify Resend Otp form validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->resendOtpFormService($request);
        } catch (Exception $e) {
            Log::error('Verify Resend Otp form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created at : 18 March 2024
     * Use : To validate verify resend otp form data
     */
    public function validateResendOtp(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'iam_principal_xid' => 'required|exists:iam_principal,id',
                'email_address' => 'required|exists:iam_principal,email_address',
                'otp_purpose' => 'required',
            ],
        );
    }



    /**
     * Created By : Hritik
     * Created at : 09 July 2024
     * Use : To get auth User data
     */
    public function getAuthUserDetails(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {


                $iamprincipal_id = $token['sub'];

                $request['iam_principal_xid'] = $iamprincipal_id;
                // return $this->AuthApiService->getAuthUserDataService($request);

                $response = $this->AuthApiService->getAuthUserDataService($request);
                return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $response, 200);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Hritik
     * Created at : 10 July 2024
     * Use : To Update User Account type
     */

    public function updateUserAccountType(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {

                $validator = $this->validateUpdateUserAccountTypeForm($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update User Account Form when login with Google/Apple form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $user_id = $token['sub'];
                $request['iam_principal_xid'] = $user_id;

                return $this->AuthApiService->updateUserAccountTypeService($request);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('Update User Account Form  function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    public function validateUpdateUserAccountTypeForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'principal_type_xid' => 'required|integer|exists:iam_principal_type,id' //1=Individual, //2= Business


            ],
        );
    }

 /**
     * Created By : Hritik
     * Created at : 09 July 2024
     * Use : To Reset Your Password
     */

    public function resetPassword(Request $request)
    {

        try {
            $validator = $this->validateResetPasswordForm($request);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Reset Password validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            return $this->AuthApiService->resetPasswordService($request);
        } catch (Exception $e) {
            Log::error('Reset Password form controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    public function validateResetPasswordForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'email_address' => 'required|email|max:50',
                'password' => ['required', 'string', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            ],
            [
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and minimum 8 and maximum 20 characters long.'
            ]
        );
    }
    


  




}




