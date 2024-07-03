<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutApiController extends Controller
{
    /**
     * Created By : Chandan Yadav
     * Created at : 10 April 2024
     * Use : Logout User Account 
     */
    public function userLogout()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                JWTAuth::invalidate($token);
                Auth::logout();
                return jsonResponseWithSuccessMessageApi(__('auth.logout'), 200);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.user_not_authenticated'), 401);
            }
        } catch (Exception $e) {
            Log::error('Account Logout failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
