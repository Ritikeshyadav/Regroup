<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\APIs\ManagePinnedApiService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManagePinnedApiController extends Controller
{
    protected $managePinnedApiService;
    public function __construct(ManagePinnedApiService $managePinnedApiService) 
    {
        $this->managePinnedApiService = $managePinnedApiService;
    }

    public function fetchPinnedDetails()
    {
        try{
            return $this->managePinnedApiService->fetchPinnedDetailsService();
        }catch(Exception $e)
        {
            Log::error('Fetch pinned details function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function pinUnpin(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'manage_tags_xid' => 'sometimes|required|exists:manage_tags,id',
                'manage_communities_xid' => 'sometimes|required|exists:manage_communities,id',
                'pin_iam_principal_xid' => 'sometimes|required|exists:iam_principal,id',
            ]);
            if($validator->fails() || $request->all() == null)
            {
                $error = $validator->errors()->all() == null ? 'No id added to pinned': $validator->errors();
                Log::info('Pin or unpin function validation failed: '.$error);
                return jsonResponseWithErrorMessageApi($error,403);
            }
            return $this->managePinnedApiService->pinUnpinService($request);
        }catch(Exception $e)
        {
            Log::error('Fetch pinned details function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchDataForPinned()
    {
        try{
            return $this->managePinnedApiService->fetchDataForPinnedService();
        }catch(Exception $e)
        {
            Log::error('Fetch data for pinned function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}
