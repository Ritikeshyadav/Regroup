<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\IamPrincipal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GoogleLoginApiController extends Controller
{
    /**
     * Crerated By: Pradyumnn Dwivedi
     * Created  at : 28 Feb 2024
     * Use: To get user data after login through google
     */
    public function googleLogin(Request $request)
    {
        try {
            $platform = $request->header("Platform");
            if ($platform == "web") {
                $validator = Validator::make($request->all(), [
                    'code' => 'required|string',
                    'principal_source_xid' => 'required|integer|exists:iam_principal_source,id'
                ]);
                if ($validator->fails()) {
                    return jsonResponseWithErrorMessage($validator->errors()->all(), 422);
                }
                $code = $request->input('code');

                //exchange code
                $response = exchangeCode($code);
                if (isset($response['error'])) {
                    return jsonResponseWithErrorMessage(__('auth.something_went_wrong_please_try_again'));
                }
                $access_token = $response['access_token'];
            } else {
                $validator = Validator::make($request->all(), [
                    'access_token' => 'required|string',
                    'principal_source_xid' => 'required|integer|exists:iam_principal_source,id'
                ]);
                if ($validator->fails()) {
                    return jsonResponseWithErrorMessage($validator->errors()->all(), 422);
                }
                $access_token = $request->input('access_token');
            }
            //get user data
            $userData = getUser($access_token);

            //store user data in iam_principal
            $principal_type_xid = 1; // for user
            $user_data_array = [
                'principal_type_xid' => $principal_type_xid,
                'principal_source_xid' => 3 ,//Google,
                'google_id' => $userData['id'],
                'email_address' => $userData['email'],
                'last_login_datetime' =>  Carbon::now(),
            ];
            DB::beginTransaction();
            $iamPrincipalData = IamPrincipal::updateOrCreate(['email_address' =>  $userData['email']], $user_data_array);
            if ($iamPrincipalData) {
                $response = generateToken($iamPrincipalData);
            } else {
                return jsonResponseWithSuccessMessage(__('auth.something_went_wrong'));
            }
            DB::commit();
            return jsonResponseWithSuccessMessage(__('auth.success'), $response, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error("Google SignUp|Login in web controller function Failed: " . $ex->getMessage());
            return jsonResponseWithErrorMessage(__('auth.something_went_wrong'));
        }
    }


 /**
     * Crerated By: Hritik D 
     * Created  at : 09 July 2024
     * Use: To Sign in WIth Google
     */
    public function signInWithGoogle(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'google_access_token' => 'required|string',
                // 'principal_source_xid' => 'required|integer|exists:iam_principal_source,id',
                'one_signal_player_id' => 'required|string',
            ]);
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("Sign in with Google validation error: " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 203);
            }
            $access_token = $request->input('google_access_token');
            $userData = getUser($access_token);
            // dd("ss",$userData);

            // if($userData && $userData['error'] && $userData['error']['code'] == 401){
            //     return jsonResponseWithErrorMessageApi($userData['error']['message'],500);
            // }

            $isDeactivatedAccountFound = IamPrincipal::where(['email_address' => $userData['email'], 'is_active' => 0])->first();
            if ($isDeactivatedAccountFound) {
                return jsonResponseWithSuccessMessage(__('auth.account_deactivated'));
            }
            $playerId = $request->one_signal_player_id;

            $isExistIamPrincipalData = IamPrincipal::where(['email_address' => $userData['email']])->first();

            if ($isExistIamPrincipalData) {
                $principal_type_xid = $isExistIamPrincipalData->principal_type_xid;
                // return jsonResponseWithSuccessMessage(__('auth.email_already_in_use'));
            } else {

                $principal_type_xid = 3; // for Google Login user for new registered user

            }



            //store user data in iam_principal
            $user_data_array = [
                'principal_type_xid' => $principal_type_xid,
                'principal_source_xid' => 3, // for google 
                'google_id' => $userData['id'],
                'email_address' => $userData['email'],
                'last_login_datetime' => Carbon::now(),
                'one_signal_player_id' => $playerId,
            ];
            DB::beginTransaction();
            $iamPrincipalData = IamPrincipal::updateOrCreate(['email_address' => $userData['email']], $user_data_array);
            $allDataOfUser = IamPrincipal::where('id', $iamPrincipalData->id)->first();

            if ($allDataOfUser) {
                $response = generateToken($allDataOfUser);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'));
            }
            DB::commit();
            return jsonResponseWithSuccessMessage(__('auth.proceed_to_register'), $response, 200);
        } catch (Exception $e) {
            Log::error('Sign in with Google controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }



  
}
