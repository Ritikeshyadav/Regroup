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


class FacebookLoginApiController extends Controller
{
    public function facebookLogin(Request $request)
    {


        try {

            $validator = Validator::make($request->all(), [
                // 'principal_source_xid' => 'required|integer|exists:iam_principal_source,id',
                'facebook_auth_token' => 'required|string',
            ]);
            if ($validator->fails()) {
                return jsonResponseWithErrorMessage($validator->errors()->all(), 422);
            }

            // check user is already exist or not.
            $iamPrincipalData = IamPrincipal::where('facebook_id', $request->facebook_auth_token)->first() ?? null;
            // dd($iamPrincipalData);
            // if ($iamPrincipalData == null) {
            //     $validator = Validator::make($request->all(), [
            //         'email' => 'required|email',
            //         'first_name' => 'required|string|max:150',
            //         'last_name' => 'required|string|max:150',
            //     ]);
            //     if ($validator->fails()) {
            //         return jsonResponseWithErrorMessage($validator->errors()->all(), 422);
            //     }
            // }

            // $principal_type_xid = 1; // for user
            $userData = [
                'principal_source_xid' => 5,// for FACEBOOK 
                'principal_type_xid' => 3, //means not not added in Registration
                'facebook_id' => $request->facebook_auth_token,
                // 'email_address' => $request->email,
                // 'first_name' => $request->first_name,
                // 'last_name' => $request->last_name,
                'last_login_datetime' => Carbon::now(),
                'is_profile_updated' => 0,
            ];

            DB::beginTransaction();
            if ($iamPrincipalData) {
                $user = $iamPrincipalData->update(['last_login_datetime' => Carbon::now()]);
                $response = generateToken($iamPrincipalData);
            } else {
                $iamPrincipalData = IamPrincipal::create($userData);
                $response = generateToken($iamPrincipalData);
            }
            DB::commit();

            return jsonResponseWithSuccessMessageApi(__('auth.proceed_to_register'), $response, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error("Apple SignUp in web controller function Failed: " . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }

    }
}
