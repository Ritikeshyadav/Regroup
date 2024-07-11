<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\APIs\ManageCMSApiService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManageCMSController extends Controller
{
    protected $manageCMSApiService;
    public function __construct(ManageCMSApiService $manageCMSApiService)
    {
        $this->manageCMSApiService = $manageCMSApiService;
    }

    public function fetchFAQs()
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCMSApiService->fetchFAQsService($token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }
        catch(Exception $e)
        {
            Log::error('Fetch faqs function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function storeContactUs(Request $request)
    {
        try{
            $token = readHeaderToken();
            if($token)
            {
                $validator = Validator::make($request->all(),[
                    'full_name' => 'required',
                    'email' => 'required',
                    'reason_to_contact' => 'required',
                    'query' => 'required',
                ]);
                if($validator->fails())
                {
                    Log::error("Store contact us validaton error: ".$validator->errors());
                    return jsonResponseWithErrorMessageApi($validator->error(),402);
                }
                $request['iam_principal_xid'] = $token['sub'];
                return $this->manageCMSApiService->storeContactUsService($request);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }
        catch(Exception $e)
        {
            Log::error('Store contact us data function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
    public function storeBugReport(Request $request)
    {
        try{
            $token = readHeaderToken();
            if($token)
            {
                $validator = Validator::make($request->all(),[
                    'device' => 'required',
                    'version' => 'required',
                    'email' => 'required',
                    'reason_to_contact' => 'required',
                    'query' => 'required',
                ]);
                if($validator->fails())
                {
                    Log::error("Store bug report validaton error: ".$validator->errors());
                    return jsonResponseWithErrorMessageApi($validator->error(),402);
                }
                $request['iam_principal_xid'] = $token['sub'];
                return $this->manageCMSApiService->storeBugReportService($request);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }
        catch(Exception $e)
        {
            Log::error('Store bug report function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchPrivacyPolicy()
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCMSApiService->fetchPrivacyPolicyService($token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }
        catch(Exception $e)
        {
            Log::error('Fetch privacy policy function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchTermsAndCondition()
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCMSApiService->fetchTermsAndConditionService($token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }
        catch(Exception $e)
        {
            Log::error('Fetch terms and condition function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}
