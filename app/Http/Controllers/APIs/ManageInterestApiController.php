<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageInterest;
use App\Services\APIs\ManageInterestApiService;
use Illuminate\Support\Facades\Log;
use Exception;

class ManageInterestApiController extends Controller
{
    protected $manageInterestApiService;

    public function __construct(ManageInterestApiService $manageInterestApiService)
    {
        $this->manageInterestApiService = $manageInterestApiService;
    }

    public function fetchManageInterests(Request $request)
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageInterestApiService->fetchInterestService() : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('fetch manage interests function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function storeSelectedInterests(Request $request)
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageInterestApiService->StoreUserSelectedInterest($request->all(), $token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('store user selected interest function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
