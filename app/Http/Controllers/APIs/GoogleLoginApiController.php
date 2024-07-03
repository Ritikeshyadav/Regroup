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
                'principal_source_xid' => $request->principal_source_xid,
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
}
