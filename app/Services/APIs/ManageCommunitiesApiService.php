<?php

namespace App\Services\APIs;

use App\Models\ManageCommunity;

use Illuminate\Support\Facades\Request;
use App\Models\IamPrincipalManageCommunityLink;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ManageCommunitiesApiService
{
    /*
     * Created By : Ritikesh yadav
     * Created At : 05 july 2024
     * Use : Store user selected groups 
     */
    public function fetchManageCommunities($request)
    {
        try {


            if ($request->get('search_data')) {
                $query = $request->get('search_data');
                if (strlen($query) >= 2) { // Ensure the query length is at least 2

                    $data = ManageCommunity::select('id', 'community_profile_photo', 'community_banner_image', 'community_name', 'community_location', 'community_description', 'community_type_xid', 'activity_xid')->where('is_active', 1)
                        ->where(function ($q) use ($query) {
                            $q->where('community_name', 'LIKE', "%{$query}%");
                        })
                        ->get();
                } else {
                    $data = ManageCommunity::select('id', 'community_profile_photo', 'community_banner_image', 'community_name', 'community_location', 'community_description', 'community_type_xid', 'activity_xid')
                        ->where('is_active', 1)
                        ->get();
                }
            } else {
                $data = ManageCommunity::select('id', 'community_profile_photo', 'community_banner_image', 'community_name', 'community_location', 'community_description', 'community_type_xid', 'activity_xid')
                    ->where('is_active', 1)
                    ->get();
            }



            if ($data == null) {
                log::info('manage communities data not found');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('fetch manage community service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
     * Created By : Ritikesh yadav
     * Created At : 05 july 2024
     * Use : Store user selected groups 
     */
    public function StoreUserSelectedCommunity($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request, [
                'manage_community_xid.*' => 'required|exists:manage_communities,id',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }
            foreach (json_decode($request['manage_community_xid']) as $community) {
                if (IamPrincipalManageCommunityLink::where(['iam_principal_xid' => (int) $iamprincipal_id, 'manage_community_xid' => $community])->doesntExist()) {
                    $storeUserSelectedCommunity = IamPrincipalManageCommunityLink::create([
                        'iam_principal_xid' => (int) $iamprincipal_id,
                        'manage_community_xid' => $community,
                        'joined_at' => Carbon::now(),
                    ]);
                }
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $storeUserSelectedCommunity, 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('store user select community function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    public function searchCommunityDataService($request)
    {
        try {
            $data = [];
            // dd($request->query('search_data'));
            if ($request->get('search_data')) {
                $query = $request->get('search_data');
                if (strlen($query) >= 2) { // Ensure the query length is at least 2

                    $data = ManageCommunity::select('id', 'community_profile_photo', 'community_banner_image', 'community_name', 'community_location', 'community_description', 'community_type_xid', 'activity_xid')->where('is_active', 1)
                        ->where(function ($q) use ($query) {
                            $q->where('community_name', 'LIKE', "%{$query}%");
                        })
                        ->get();
                } else {
                    $data = ManageCommunity::select('id', 'community_profile_photo', 'community_banner_image', 'community_name', 'community_location', 'community_description', 'community_type_xid', 'activity_xid')
                        ->where('is_active', 1)
                        ->get();
                }
            }

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('Search group service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }

    }
}