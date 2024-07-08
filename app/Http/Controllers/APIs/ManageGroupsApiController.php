<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\APIs\ManageGroupsApiService;
use Exception;

class ManageGroupsApiController extends Controller
{
    protected $manageGroupsApiService;
    public function __construct(ManageGroupsApiService $manageGroupsApiService)
    {
        $this->manageGroupsApiService = $manageGroupsApiService;
    }

    public function fetchManageGroup()
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageGroupsApiService->fetchGroupService() : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('fetch manage group function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function storeSelectedGroup(Request $request)
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageGroupsApiService->StoreUserSelectedGroup($request->all(), $token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('store user select group function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
