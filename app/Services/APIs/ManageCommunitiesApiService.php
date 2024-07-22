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
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 403);
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





    /*
     * Created By : Hritik
     * Created At : 22 july 2024
     * Use : Store Community  
     */

    public function createCommunityApiService($request)
    {
        try {

            DB::beginTransaction();


            // dd($request->all());
            $iamprincipal_id = $request->iam_principal_xid;

            if ($request->hasFile('community_profile_photo')) {
                $image = $request->file('community_profile_photo');
                $communityProfilePhoto = saveSingleImageWithoutCrop($image, 'community_profile_photo', null);
            } else {
                $communityProfilePhoto = null;
            }

            if ($request->hasFile('community_banner_image')) {
                $image = $request->file('community_banner_image');
                $communityBannerImage = saveSingleImageWithoutCrop($image, 'community_banner_image', null);
            } else {
                $communityBannerImage = null;
            }



            $newCommunity = new ManageCommunity();

            $newCommunity->community_profile_photo = $communityProfilePhoto;
            $newCommunity->community_banner_image = $communityBannerImage;
            $newCommunity->community_name = $request->community_name;
            $newCommunity->community_location = $request->community_location;
            $newCommunity->community_description = $request->community_description;
            $newCommunity->community_type_xid = $request->community_type_xid;
            $newCommunity->activity_xid = $request->activity_xid;


            $newCommunity->save();
            Log::info("Community stored sucessfully");

            //join community =
            IamPrincipalManageCommunityLink::create([
                'iam_principal_xid' => $iamprincipal_id,
                'manage_community_xid' => $newCommunity->id,
                'joined_at' => Carbon::now(),
                'is_admin' => 1
            ]);

            Log::info("Community linked stored sucessfully");

            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('store user select community function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }

    }


    /*
     * Created By : Hritik
     * Created At : 22 july 2024
     * Use : Update Community  
     */


    public function updateCommunityApiService($request)
    {
        try {
            DB::beginTransaction();
            $iamprincipal_id = $request->iam_principal_xid;
            $communityId = $request->community_id;

            
            // dd($request->all(),$iamprincipal_id);
            $communityData = ManageCommunity::where('id', $communityId)->first();

            if ($request->hasFile('community_profile_photo')) {
                $image = $request->community_profile_photo;
                $image_db = null;
            } else {
                $image = null;
                $image_db = $communityData->community_profile_photo;
            }

            $communityProfileImage = saveSingleImageWithoutCrop($image, 'community_profile_photo', $image_db);


            if ($request->hasFile('community_banner_image')) {
                $image = $request->community_banner_image;
                $image_db = null;
            } else {
                $image = null;
                $image_db = $communityData->community_banner_image;
            }
            $communityBannerImage = saveSingleImageWithoutCrop($image, 'community_banner_image', $image_db);



            $updateCommunity = ManageCommunity::where('id', $communityId)->update(
                [

                    'community_profile_photo' => $communityProfileImage,
                    'community_banner_image' => $communityBannerImage,

                    'community_name' => $request->community_name,
                    'community_location' => $request->community_location,
                    'community_description' => $request->community_description,
                    'community_type_xid' => $request->community_type_xid,
                    'activity_xid' => $request->activity_xid,


                ]
            );

            DB::commit();


            return jsonResponseWithSuccessMessageApi(__('success.update_data'), [], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' Timeline  service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }

    }




}


