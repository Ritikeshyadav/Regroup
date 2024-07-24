<?php

namespace App\Services\APIs;

use App\Models\Abilities;
use App\Models\AccountSessions;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalBlockedProfile;
use App\Models\IamPrincipalBusinessUserLink;

use App\Models\IamPrincipalFollowers;
use App\Models\ManageTimelines;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\AccountSession;
use Throwable;
use Illuminate\Support\Facades\Http;

class IndividualUserGuestViewService
{
    protected $individualprofileDetailService;
    protected $businessProfileDetailService;

    public function __construct(ProfileDetailsApiService $individualprofileDetailService,BusinessProfileDetailsApiService $businessProfileDetailService)
    {
        $this->individualprofileDetailService = $individualprofileDetailService;
        $this->businessProfileDetailService = $businessProfileDetailService;
    }

    
    public function getIndividualUserGuestViewService($request)
    {
        try {

            $guestUserId = $request->query('guest_user_id');

            if($guestUserId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass Guest User Id as Query Params", 500);

            }
            //the below code is Reusable From Individual -ProfileDetailsApiService
            $getIndividualUserGuestViewData = $this->individualprofileDetailService->fetchProfileService($guestUserId, $request->iam_principal_id);
           
            return $getIndividualUserGuestViewData;
           
            // return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $getAccountSessions, 200);
        } catch (Exception $e) {
            Log::error('Fetch individual user guest view service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    public function getBusinessUserGuestViewService($request)
    {
        try {

            $guestUserId = $request->query('guest_user_id');
            $data = IamPrincipal::select('id','principal_type_xid')->where('id', $guestUserId)->first();


            if($guestUserId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass Guest User Id as Query Params", 500);

            }
            if($data && $data->principal_type_xid == 1){
                return jsonResponseWithErrorMessageApi("User Not Found in Our Database", 500);
            }
            //the below code is Reusable From Individual -ProfileDetailsApiService
            $getIndividualUserGuestViewData = $this->businessProfileDetailService->fetchBusinessProfileService($guestUserId);
            return $getIndividualUserGuestViewData;
           
            // return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $getAccountSessions, 200);
        } catch (Exception $e) {
            Log::error('Fetch Business user guest view service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    public function fetchGuestUserFollowersService($request)
    {
        try {
            $search = $request->search;
            $guestUserId = $request->query('guest_user_id');

            if($guestUserId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass Guest User Id as Query Params", 500);

            }
            $followers = IamPrincipalFollowers::whereHas('follower', function ($query) use ($search) {
                $query->when($search != null, function ($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                    $q->where('user_name', 'like', '%' . $search . '%');
                    $q->orWhere('full_name', 'like', '%' . $search . '%');
                }, function ($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                });
            })
                ->with([
                    'follower' => function ($query) use ($search) {
                        $query->when($search != null, function ($q) use ($search) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                            $q->where('user_name', 'like', '%' . $search . '%');
                            $q->orWhere('full_name', 'like', '%' . $search . '%');
                        }, function ($q) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                        });
                    }
                ])
                ->where('following_iam_principal_xid',  $guestUserId )
                ->whereNotIn('iam_principal_xid', IamPrincipalBlockedProfile::where('iam_principal_xid', $guestUserId)->pluck('blocked_iam_principal_xid'))
                ->select('following_iam_principal_xid', 'iam_principal_xid')
                ->get();

            if ($followers == null) {
                Log::info('Guest User ka follower data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $followers, 200);
        } catch (Exception $e) {
            Log::error('Guest User Fetch follower service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }



    public function fetchGuestUserFollowingService($request)
    {
        try {
            $search = $request->search;
            $guestUserId = $request->query('guest_user_id');

            if($guestUserId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass Guest User Id as Query Params", 500);

            }
            $following = IamPrincipalFollowers::whereHas('following', function ($query) use ($search) {
                $query->when($search != null, function ($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                    $q->where('user_name', 'like', '%' . $search . '%');
                    $q->orWhere('full_name', 'like', '%' . $search . '%');
                }, function ($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                });
            })
                ->with([
                    'following' => function ($query) use ($search) {
                        $query->when($search != null, function ($q) use ($search) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                            $q->where('user_name', 'like', '%' . $search . '%');
                            $q->orWhere('full_name', 'like', '%' . $search . '%');
                        }, function ($q) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo','principal_type_xid');
                        });
                    }
                ])
                ->select('following_iam_principal_xid', 'iam_principal_xid')
                ->where('iam_principal_xid', $guestUserId )
                ->get();
            if ($following == null) {
                Log::info('Guest User following data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $following, 200);
        } catch (Exception $e) {
            Log::error('Guest User Fetch following service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    

}




