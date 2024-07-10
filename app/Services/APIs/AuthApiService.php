<?php

namespace App\Services\APIs;

use App\Mail\ForgotPasswordOtp;
use App\Mail\SendOtp;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalGroupLink;
use App\Models\IamPrincipalManageCommunityLink;
use App\Models\IamPrincipalManageGroupLink;
use App\Models\IamPrincipalOtp;
use App\Models\ManageCommunity;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiService
{

    /**
     * Created By : Vedant Chavan
     * Created at : 01 July 2024
     * Use : To send otp
     */
    public function sendOtpService($request)
    {
        try {
            DB::beginTransaction();
            // Check if the user already exists
            $email = $request->email_address;
            $existingUser = IamPrincipal::where('email_address', $email)->first();
            if ($existingUser) {
                return jsonResponseWithErrorMessageApi(__('auth.user_already_exist'), 403);
            }

            $otp = generateRandomOTP();
            // $otp = 1234;
            IamPrincipalOtp::updateOrCreate(
                ['email_id' => $request->email_address],
                [
                    'otp_code' => $otp,
                    'otp_purpose' => 'Register User',
                    'valid_till' => Carbon::now()->addMinutes(2),
                    'is_used' => 0,
                ]
            );

            $mailData['body'] = $otp;
            Mail::to($email)->send(new SendOtp($mailData));
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.otp_sent_successfully'), 200);
        } catch (Throwable $ex) {
            DB::rollBack();
            Log::error('Registration form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Vedant Chavan
     * Created at : 01 July 2024
     * Use : check otp and register
     */

    public function verifyOtpService($request)
    {
        try {
            DB::beginTransaction();
            $iamPrincipal = IamPrincipalOTP::where('email_id', $request->email_address)->first();

            // Check if OTP record exists for the user
            if (!$iamPrincipal) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_not_found'), 403);
            }

            // Check if the provided OTP matches the stored OTP
            if ($iamPrincipal->otp_code !== $request->otp) {
                return jsonResponseWithErrorMessageApi(__('auth.invalid_otp'), 403);
            }

            // Check if the OTP is still valid
            if (Carbon::now()->gt($iamPrincipal->valid_till)) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_expired'), 403);
            }

            // Check if the OTP has already been used
            if ($iamPrincipal->is_used === 1) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_used'), 403);
            }

            // Mark OTP as used
            $iamPrincipal->is_used = 1;
            $iamPrincipal->save();

            $user = IamPrincipal::create([
                'email_address' => $request->email_address,
                'password_hash' => Hash::make($request->password),
                'principal_type_xid' => $request->account_type,
                'principal_source_xid' => '2',
                'is_profile_updated' => 0
            ]);
            $token = generateToken($user);
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $token, 201);
        } catch (Throwable $ex) {
            DB::rollBack();
            Log::error('Registration form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 14 March 2024
     * Use : To create registration form store data service function
     */
    public function registrationFormService($request)
    {
        try {
            DB::beginTransaction();
            // Check if the user already exists
            $existingUser = IamPrincipal::where('email_address', $request->email_address)->first();
            if ($existingUser) {
                return jsonResponseWithErrorMessageApi(__('auth.user_already_exist'), 403);
            }

            $user = IamPrincipal::create([
                'email_address' => $request->email_address,
                'password_hash' => Hash::make($request->password),
                'principal_type_xid' => $request->account_type,
                'principal_source_xid' => '2',
            ]);

            DB::commit();
            $responseData['user'] = $user;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Throwable $ex) {
            DB::rollBack();
            Log::error('Registration form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 14 March 2024
     * Use : To create login form store data service function
     */
    public function loginFormService($request)
    {
        try {
            if ($request->email_address) {
                $user = IamPrincipal::where('email_address', $request->email_address)->first();
                if (!$user) {
                    return jsonResponseWithErrorMessageApi(__('auth.user_not_found'), 403);
                }
                if ($user && Hash::check($request->password, $user->password_hash)) {
                    $token = JWTAuth::fromUser($user);
                    $responseData['access-token'] = $token;
                    $responseData['id'] = $user['id'];
                    return jsonResponseWithSuccessMessageApi(__('auth.sign_in'), $responseData, 201);
                } else {
                    Log::error('User email address not found');
                    return jsonResponseWithErrorMessageApi(__('auth.failed'), 403);
                }
            }
        } catch (Throwable $ex) {
            Log::error('Login form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 14 March 2024
     * Use : To create forgot password form to send otp.
     */
    public function forgotPasswordFormService($request)
    {
        try {
            $email = $request->email_address;
            $user = IamPrincipal::where('email_address', $email)->first();
            if (!$user) {
                return jsonResponseWithErrorMessageApi(__('auth.check_email'), 403);
            }
            $otp = generateRandomOTP();
            IamPrincipalOtp::updateOrCreate(
                ['principal_xid' => $user->id],
                [
                    'otp_code' => $otp,
                    'otp_purpose' => 'forgot password',
                    'valid_till' => Carbon::now()->addMinutes(2),
                    'is_used' => 0,
                ]
            );

            $mailData['body'] = $otp;
            Mail::to($email)->send(new ForgotPasswordOtp($mailData));

            $response = [
                'iam_principal_xid' => $user->id,
                'email_address' => $user->email_address,
                'otp' => $otp
            ];
            return jsonResponseWithSuccessMessageApi(__('auth.otp_sent_successfully'), $response, 201);
        } catch (Throwable $ex) {
            Log::error('Forgot Password form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 15 March 2024
     * Use : To check verify forgot password form to send otp.
     */
    public function verifyOtpForgotPasswordFormService($request)
    {
        try {
            // Retrieve the user's OTP record
            $user = IamPrincipal::where('id', $request->iam_principal_xid)->first();
            $iamPrincipal = IamPrincipalOTP::where('principal_xid', $user->id)->first();

            // Check if OTP record exists for the user
            if (!$iamPrincipal) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_not_found'), 403);
            }

            // Check if the provided OTP matches the stored OTP
            if ($iamPrincipal->otp_code !== $request->otp) {
                return jsonResponseWithErrorMessageApi(__('auth.invalid_otp'), 403);
            }

            // Check if the OTP is still valid
            if (Carbon::now()->gt($iamPrincipal->valid_till)) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_expired'), 403);
            }

            // Check if the OTP has already been used
            if ($iamPrincipal->is_used === 1) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_used'), 403);
            }

            // Mark OTP as used
            $iamPrincipal->is_used = 1;
            $iamPrincipal->save();

            $response = [
                'iam_principal_xid' => $user->id
            ];
            return jsonResponseWithSuccessMessageApi(__('auth.otp_verified'), $response, 201);
        } catch (Throwable $ex) {
            Log::error('Verify Forgot Password Otp form service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 18 March 2024
     * Use : To check verify resend form to resend otp.
     */
    public function resendOtpFormService($request)
    {
        try {
            // Retrieve the user's OTP record
            $iamPrincipal = IamPrincipalOTP::where('principal_xid', $request->iam_principal_xid)->first();
            $email = $request->email_address;

            // Check if OTP record exists for the user
            if (!$iamPrincipal) {
                return jsonResponseWithErrorMessageApi(__('auth.otp_not_found'), 403);
            }

            // Calculate the allowed resend interval (2 minutes)
            $allowedResendInterval = Carbon::now()->subMinutes(2);

            // Check if the user can resend OTP only after a 2-minute interval
            if ($iamPrincipal->updated_at >= $allowedResendInterval) {
                return jsonResponseWithErrorMessageApi(__('auth.send_otp'), 403);
            }

            // Generate a new OTP for the user
            $otp = generateRandomOTP();

            // Update the OTP record with the new OTP and validity
            $iamPrincipal->otp_code = $otp;
            $iamPrincipal->otp_purpose = $request->otp_purpose;
            $iamPrincipal->valid_till = Carbon::now()->addMinutes(2);
            $iamPrincipal->is_used = 0;
            $iamPrincipal->save();

            $mailData['body'] = $otp;
            Mail::to($email)->send(new ForgotPasswordOtp($mailData));

            // For testing purposes, include the OTP in the response
            $response = [
                'otp' => $otp,
            ];
            return jsonResponseWithSuccessMessageApi(__('auth.otp_sent_successfully'), $response, 200);
        } catch (Exception $e) {
            Log::error('Resend Otp form service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Hritik D
     * Created At : 09 July 2024
     * Use : To get User Data 
     */

    public function getAuthUserDataService($request)
    {
        try {

            $data = IamPrincipal::select(
                'id',
                'google_id',
                'apple_id',
                'user_name',
                'full_name',
                'gender',
                'date_of_birth',
                'phone_number',
                'email_address',
                'address_line1',
                'address_line2',
                'post_code',
                'last_login_datetime',
                'profile_photo',
                'is_profile_updated',
                'is_active',
                'created_at',



            )->find($request['iam_principal_xid']);
            $userCommunityLink = IamPrincipalManageCommunityLink::with([
                'communityData' => function ($query) {
                    $query->select(
                        'id',
                        'community_profile_photo',
                        'community_banner_image',
                        'community_name',
                        'community_location',
                        'community_description',
                        'community_type_xid',
                        'activity_xid',
                        'is_active',
                        'created_at'
                    );
                }
            ])->
                select('id', 'iam_principal_xid', 'manage_community_xid', 'joined_at', 'is_active')->where('iam_principal_xid', $request['iam_principal_xid'])->get();

            $userGroupsLink = IamPrincipalManageGroupLink::with

            ([
                    'groupData' => function ($query) {
                        $query->select(
                            'id',
                            'manage_group_type_xid',
                            'title',
                            'background_image',
                            'group_image',
                            'link',
                            'description',
                            'is_active',

                            'created_at'
                        );
                    }

                ])->select('id', 'iam_principal_xid', 'manage_group_xid', 'created_at')
                ->where('iam_principal_xid', $request['iam_principal_xid'])->get();


            // if( $data->principal_type_xid == 2){

            // }

            $result = [
                'userData' => $data,
                'userCommunity' => $userCommunityLink,
                'userGroups' => $userGroupsLink
            ];



            return $result;
        } catch (Exception $ex) {
            Log::error('User Get data service failed : ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Hritik
     * Created At : 10 July 2024
     * Use : To Update User account type .
     */
    public function updateUserAccountTypeService($request)
    {
        try {

            DB::beginTransaction();
            // Retrieve the user's OTP record
            $principalTypeXid = $request->principal_type_xid;
            $email = $request->email_address;
            $iamPrincipalData = IamPrincipal::where('id', $request['iam_principal_xid'])->first();
            // dd($request->all(),$iamPrincipalData);
            if ($email != null) {
                $iamPrincipalData->email_address = $email;
                $iamPrincipalData->principal_type_xid = $principalTypeXid;
                $iamPrincipalData->save();

            }

            $iamPrincipalData->principal_type_xid = $principalTypeXid;
            $iamPrincipalData->save();

            DB::commit();


            return jsonResponseWithSuccessMessageApi(__('auth.otp_sent_successfully'), $iamPrincipalData, 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Resend Otp form service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


}
