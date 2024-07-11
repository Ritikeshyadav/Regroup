<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\APIs\ManageGroupsApiService;
use Exception;

use Illuminate\Support\Facades\Validator;

class ManageGroupsApiController extends Controller
{
    protected $manageGroupsApiService;
    public function __construct(ManageGroupsApiService $manageGroupsApiService)
    {
        $this->manageGroupsApiService = $manageGroupsApiService;
    }

    public function fetchManageGroup(Request $request)
    {
        try {
            $token = readHeaderToken();
            return $token ? $this->manageGroupsApiService->fetchGroupService($request) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('fetch manage group function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    /**
     * Created By : Hritik
     * Created at : 10 July 2024
     * Use : To Search Group by name Your Password
     */
    public function seachGroup(Request $request)
    {
        try {
            $token = readHeaderToken();
            $searchText = $request->query('search_data');
           
            return $token ? $this->manageGroupsApiService->searchGroupDataService($request) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('Search Group group function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }



    public function storeSelectedGroup(Request $request)
    {
        try {
            $token = readHeaderToken();
            return $token ? $this->manageGroupsApiService->StoreUserSelectedGroup($request->all(), $token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('store user select group function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
