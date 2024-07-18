<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\AccountSessionApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AccountSessionController extends Controller
{
    protected $accountSessionApiService;
    public function __construct(AccountSessionApiService $accountSessionApiService)
    {
        $this->accountSessionApiService = $accountSessionApiService;
    }

      /**This Module can be use as REUSABLE*/
    /**
     * Created By : Hritik
     * Created At : 18 July 2024
     * Use : To Create Account Sessions of both User
     */
    public function storeAccountSession(Request $request)
    {
        try {
          
                $validator = Validator::make(
                    $request->all(),
                    [
                        // 'ip_address' => 'required',
                        'device_name' => 'required',
                       
                        
                    ],
                );
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Store account Session form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $request['iam_principal_xid'] = auth()->user()->id;
               
                return $this->accountSessionApiService->storeAccountSessionService($request);
            
        } catch (Exception $ex) {
            Log::error('add accont sessions details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Hritik
     * Created At : 18 July 2024
     * Use : To Fetch Account Session of Particular User 
     */
    public function getAccountSessions(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->accountSessionApiService->getAccountSessionsService($iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch Account session master function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


}
