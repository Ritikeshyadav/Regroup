<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Created By : Chandan Yadav
         * Created at : 19 March 2024
         * Use : To validate request before sending response
         */
        $locale = $request->header('Accept-Language');
        if ($locale) {
            app()->setLocale($locale);
        }

        $authorizedUsers = [
            'RegroupUserName' => '71%@L%es^bUX94`J9XT*@bh,._WWM{$%^^&&', // Replace with actual credentials
        ];

        $authUser = $request->getUser();
        $authPass = $request->getPassword();

        if (!isset($authorizedUsers[$authUser]) || $authorizedUsers[$authUser] !== $authPass) {
            return response()->json([
                'error' => 'Authorization Required',
                'message' => 'Access denied'
            ], 401);
        }

        $lang = $request->header('Accept-Language', null);
        if (!empty($lang)) {
            app()->setLocale($lang);
        }

        return $next($request);
    }
}
