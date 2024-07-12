<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Session as FacadesSession;
use Tymon\JWTAuth\Facades\JWTAuth;

class WdiJwtMiddleware
{
    /**
     * Created By: Chandan Yadav
     * Created at: 19 March 2024
     * Use: To handle Wdi login authentication middleware
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the custom access-token header is present
        if (!$request->hasHeader('access-token')) {
            return response()->json(['status' => 'error', 'status_code' => 401, 'message' => 'Access token not provided'], 401);
        }

        // Retrieve the token from the custom access-token header
        $token = $request->header('access-token');

        try {
            // Attempt to authenticate the user based on the token
            $user = JWTAuth::setToken($token)->authenticate();
            FacadesSession::flash('wdiToken', $token);
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'status_code' => 401, 'message' => 'Invalid token'], 401);
        }

        $token = readHeaderToken();
        if(!$token)
        {
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }

        return $next($request);
    }
}
