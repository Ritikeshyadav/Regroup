<?php

use App\Models;
use App\Models\IamPrincipal;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
/**
 * Created By : Chandan Yadav
 * Created at : 13 March 2024
 * Uses : To Generate random OTP
 */
if (!function_exists('generateRandomOTP')) {
    function generateRandomOTP()
    {
        return (rand(1000, 9999));
        // return (1234);
    }
}

/**
 * Created By : Chandan Yadav
 * Created at : 13 March 2024
 * Use : Json response with success message for API
 */
if (!function_exists('jsonResponseWithSuccessMessageApi')) {
    function jsonResponseWithSuccessMessageApi($message, $data = [], $statusCode = 200)
    {
        // Set the HTTP status code
        http_response_code($statusCode);

        // Prepare the response array
        $response = [
            'status' => 'success',
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, $statusCode);

        // Stop further execution (optional)
        // exit();
    }
}

/**
 * Created By : Chandan Yadav
 * Created at : 13 March 2024
 * Use : Json response with error message for API
 */
if (!function_exists('jsonResponseWithErrorMessageApi')) {
    function jsonResponseWithErrorMessageApi($errorMessage, $statusCode = 500)
    {
        // Set the HTTP status code
        http_response_code($statusCode);

        // Prepare the response array
        $response = [
            'status' => 'error',
            'status_code' => $statusCode,
            'message' => $errorMessage,
        ];
        return response()->json($response, $statusCode);

        // Stop further execution (optional)
        exit();
    }
}


/**
 * Created By : Pradyumn Dwivedi
 * Created at : 13 Feb 2024
 * Use : Get google token data by using code
 */
if (!function_exists('exchangeCode')) {
    function exchangeCode($code)
    {
        // Exchange the authorization code for an access token
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.google.web_client_id'),
            'client_secret' => config('services.google.web_client_secret'),
            'redirect_uri' => config('services.google.web_redirect'),
            'code' => $code,
        ]);
        return $response;
    }
}

/**
 * Created By : Pradyumn Dwivedi
 * Created at : 13 Feb 2024
 * Use : Get user data by using access code
 */
if (!function_exists('getUser')) {
    function getUser($accessToken)
    {
        if (!$accessToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Fetch user data from Google using the access token
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://www.googleapis.com/oauth2/v1/userinfo');

        return $response->json();
    }
}


/**
 * Created By : Pradyumn Dwivedi
 * Created at : 14 Feb 2024
 * Use : Get user data and generateToken
 */
if (!function_exists('generateToken')) {
    function generateToken($iamPrincipalData)
    {
        // //login user and generate token
        // Auth::login($iamPrincipalData);
        // $user = auth()->user();
        // $token = $user->createToken('MyApp', ['*']);
        // $token->token->expires_at = now()->addDays(30);
        // $token->token->save();
        $now = Carbon::now();
        if ($iamPrincipalData) {

            $iamPrincipalData->update(['last_login_datetime' => $now]);
            $token = JWTAuth::fromUser($iamPrincipalData);
            $responseData['access-token'] = $token;
            $responseData['is_profile_updated'] = $iamPrincipalData->is_profile_updated;
            $responseData['is_account_type_updated'] = $iamPrincipalData->principal_type_xid;

        }
        $accessToken = $token;
        $response = [
            'token' => $accessToken,
            'user_id' => $iamPrincipalData->id,
            'email' => $iamPrincipalData->email_address,
            'is_profile_updated' => $iamPrincipalData->is_profile_updated,
           'is_account_type_updated' => $iamPrincipalData->principal_type_xid,
        ];
        
        return $response;
    }
}


/**
 *   Created by : Chandan Yadav
 *   Created On : 14 March 2024
 *   Uses: This function will be used to full search data in api.
 */
if (!function_exists('fullSearchQuery')) {
    function fullSearchQuery($query, $word, $params)
    {
        $orwords = explode('|', $params);
        $query = $query->where(function ($query) use ($word, $orwords) {
            foreach ($orwords as $key) {
                $query->orWhere($key, 'like', '%' . $word . '%');
            }
        });
        return $query;
    }
}

/**
 * Created by : Chandan Yadav
 * Created at : 14 March 2024
 * Use : To check and validate login vendor token
 */
if (!function_exists('readHeaderToken')) {
    function readHeaderToken()
    {
        $tokenData = Session::get('wdiToken');
        $token = JWTAuth::setToken($tokenData)->getPayload();

        //convert iat to readable format
        $iat = date('Y-m-d H:i:s', $token['iat']);

        // check token issued time for single device login
        $check_iat = IamPrincipal::where([['id', $token['sub']],])->first();
        if ($check_iat) {
            return $token;
        } else {
            return false;
        }
    }
}

/**
 * Created By : Chandan Yadav
 * Created at : 22 January 2024
 * Use : json response with success message
 */
if (!function_exists('jsonResponseWithSuccessMessage')) {
    function jsonResponseWithSuccessMessage($message, $data = [], $statusCode = 200)
    {
        $response = [
            // 'status' => 'success',
            // 'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }
}

/**
 * Created By : Chandan Yadav
 * Created at : 22 January 2024
 * Use : json response with error message
 */
if (!function_exists('jsonResponseWithErrorMessage')) {
    function jsonResponseWithErrorMessage($errorMessage, $statusCode = 400)
    {
        $response = [
            // 'status' => 'error',
            // 'status_code' => $statusCode,
            'message' => $errorMessage,
        ];
        return response()->json($response, $statusCode);
    }
}