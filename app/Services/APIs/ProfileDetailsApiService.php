<?php

namespace App\Services\APIs;

use App\Models\Abilities;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalBlockedProfile;
use App\Models\IamPrincipalCertifications;
use App\Models\IamPrincipalManageGroupLink;
use App\Models\IamPrincipalManageSubGroupsLink;
use App\Models\IamRole;
use App\Models\IamPrincipalFollowers;
use App\Models\ManageCommunityManageGroupsLink;
use App\Models\ManageTimelines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Services\APIs\ManageInterestApiService;
use Carbon\Carbon;

class ProfileDetailsApiService
{
    /**
     * Created By : Vedant Chavan
     * Created At : 03 July 2024
     * Use : To add profile details Service
     */
    public function addProfileDetailService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();

            $profilePhoto = $request->file('profile_photo');
            $profilePath = saveSingleImageWithoutCrop($profilePhoto, 'profile_photos');

            $profileData = IamPrincipal::updateOrCreate(
                ['id' => $iamprincipal_id],
                [
                    'full_name' => $request->full_name,
                    'user_name' => $request->username,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'address_line1' => $request->location,
                    'profile_photo' => $profilePath,
                    'is_profile_updated' => 1
                ]
            );
            DB::commit();
            $responseData['profile'] = $profileData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add profile details service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    // /**
    //  * Created By : Chandan Yadav
    //  * Created At : 08 April 2024
    //  * Use : To fetch role master listing service
    //  */
    // public function fetchRoleService()
    // {
    //     try {
    //         $data = IamRole::select('id', 'role_name')
    //             ->where([['is_active', 1]])
    //             ->get();

    //         if ($data == null) {
    //             Log::info('role master data not found.');
    //             return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
    //         }
    //         $responseData['result'] = $data;
    //         return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
    //     } catch (Exception $ex) {
    //         Log::error('fetch role master service function failed: ' . $ex->getMessage());
    //         return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
    //     }
    // }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To update profile details service
     */
    public function updateProfileService($iamprincipal_id, $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email',
                'password' => 'required',
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 403);
            }

            $data = IamPrincipal::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            $data->update([
                'email_address' => $request->email_address,
                'password_hash' => Hash::make($request->password),
            ]);
            DB::commit();
            $responseData['profile'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('update profile details service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 08 April 2024
     * Use : To update profile details service
     */
    public function updateBothProfileService($iamprincipal_id, $request)
    {
        try {
            DB::beginTransaction();
            $userData = IamPrincipal::select('id', 'profile_photo')->where('id', $iamprincipal_id)->first();

            if (isset($request->profile_image)) {
                $image = $request->profile_image;
                $image_db = null;
            } else {
                $image = null;
                $image_db = $userData->profile_photo;
            }
            if ($request->has('profile_image')) {
                $img = saveSingleImageWithoutCrop($request->file('profile_image'), 'profile_image', $image_db);
                $request['profile_photo'] = $img;

                // remove profile_image key from request array
                $newArray = \Illuminate\Support\Arr::except($request->all(), ['profile_image']);
            }

            $interestArray = json_decode($request->interest);
            if ($interestArray) {
                $addInterestArray = (new ManageInterestApiService)->removeInterest($interestArray);
                if ($addInterestArray) {
                    $emptyData['other_interest'] = null;
                    (new ManageInterestApiService)->storeInterest($addInterestArray, $emptyData);
                }
                // remove profile_image key from request array
                $newArray = \Illuminate\Support\Arr::except($request->all(), ['interest']);
            }

            $data = IamPrincipal::where('id', $iamprincipal_id)->update($newArray ?? $request->all());
            DB::commit();
            $responseData['profile'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('update profile details service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To delete profile service
     */
    public function deleteProfileService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 422);
            }

            if ($request->id != $iamprincipal_id) {
                return jsonResponseWithErrorMessageApi(__('auth.unauthorized_action'), 403);
            }

            $data = IamPrincipal::find($request->id);
            if (!$data) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            $data->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $data, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete profile data failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /* 
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch profile service
     */
    public function fetchProfileService($iamprincipal_id, $myId)
    {
        try {
            $data = IamPrincipal::with('interestsLink.interest')->where('id', $iamprincipal_id)->first();
            $interestName = [];
            if ($data->interestsLink != null) {
                foreach ($data->interestsLink as $interests) {
                    $interestName[] = ['id' => $interests->interest->id, 'name' => $interests->interest->name];
                    // array_push($interestName, $interests->interest->name);
                }
                $data->interestName = $interestName;
            }

            $getTimelines = ManageTimelines::select('id', 'club_name', 'role_name', 'team_name', 'start_date', 'end_date', 'abilities_xids')->where('iam_principal_xid', $iamprincipal_id)->orderByDesc('id')->where('is_active', 1)->get();
            // $myJoinedSubGroups = IamPrincipalManageSubGroupsLink::select('id', 'iam_principal_xid', 'manage_group_xid', 'manage_sub_group_xid')
            //     ->with([
            //         'subGroupData' => function ($query) {
            //             $query->select('id', 'title', 'sub_group_image'); // Replace with the columns you need
            //         }
            //     ])
            //     ->where('iam_principal_xid', $iamprincipal_id)->orderByDesc('id')->get();

            $myJoinedGroups = IamPrincipalManageGroupLink::select('id', 'iam_principal_xid', 'manage_group_xid')
                ->with([
                    'groupData' => function ($query) {
                        $query->select('id', 'title', 'group_image'); // Replace with the columns you need
                    }
                ])
                ->where('iam_principal_xid', $iamprincipal_id)->orderByDesc('id')->get();
            foreach ($myJoinedGroups as $key => $item) {
                if ($item->groupData) {
                    $item->groupData->group_image = ListingImageUrl('group_image', $item->groupData->group_image);
                }
            }
            // dd( $myJoinedSubGroups );


            //for new release audio image
            foreach ($getTimelines as $key => $timeline) {

                $abilityIds = explode(',', $timeline->abilities_xids);
                $abilities = Abilities::select('id', 'name')->whereIn('id', $abilityIds)->get();
                $getTimelines[$key]['abilities'] = $abilities;
            }

            $userCertifications = IamPrincipalCertifications::select('id', 'certification_name', 'certification_image', 'certification_reason', 'certification_date', 'iam_principal_xid')->where('iam_principal_xid', $iamprincipal_id)->get();
            foreach ($userCertifications as $key => $val) {
                $userCertifications[$key]['certification_image'] = ListingImageUrl('certifications', $val->certification_image);
            }
            $date1 = Carbon::now()->format('y-m-d');
            $date2 = $data->created_at->format('y-m-d');

            $date1 = Carbon::parse($date1);
            $date2 = Carbon::parse($date2);

            $diffForHumans = $date2->diffInDays($date1);
            $isIamFollowing=0;
          
            if($myId && $myId != null){

                //iamprincipal_id  means Guest Account in GuestUser Service
                $isIamFollowing =  IamPrincipalFollowers::where('iam_principal_xid', $myId)->where('following_iam_principal_xid',$iamprincipal_id)->first() ? 1: 0;
            }


            $formatData = (array) [
                'id' => $data->id,
                'user_name' => $data->user_name,
                'location' => $data->address_line1,
                // 'pin' => $data->pin,
                'full_name' => $data->full_name,
                'gender' => $data->gender,
                'profile_photo' => ListingImageUrl('profile_photos', $data->profile_photo),

                'date_of_birth' => $data->date_of_birth,
                'interest' => $interestName,
                'about' => $data->about,
                'position' => $data->position,
                'training_scores' => $data->training_scores,
                'height' => $data->height,
                'weight' => $data->weight,
                'batting_average' => $data->batting_average,
                'follows' => $this->fetchFollowers($iamprincipal_id),
                'timelines' => $getTimelines,
                'account_visibility' => $data->is_account_visibility,
                'my_joined_groups' => $myJoinedGroups,
                // 'my_joined_subgroups' => $myJoinedSubGroups,
                'certifications' => $userCertifications,
                'days_before_joined' => $diffForHumans,
                'is_iam_following_to_guest_user'=> $isIamFollowing
            ];

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $formatData, 200);
        } catch (Exception $e) {
            Log::error('Fecth profile service function failes: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /* 
     * Created By : Hritik
     * Created At : 22 July 2024
     * Use : To fetch My joined Group service
     */
    public function myJoinedGroupsApiSerice($request)
    {
        try {

            $userId = $request->query('user_id');

            if($userId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass User Id in Query Params", 500);

            }

            $myJoinedGroups = IamPrincipalManageGroupLink::select('id', 'iam_principal_xid', 'manage_group_xid')
                ->with([
                    'groupData' => function ($query) {
                        $query->select('id', 'title', 'group_image'); // Replace with the columns you need
                    }
                ])
                ->where('iam_principal_xid', $userId )->orderByDesc('id')->get();
            // dd( $myJoinedSubGroups );
            foreach ($myJoinedGroups as $key => $item) {
                if ($item->groupData) {
                    $item->groupData->group_image = ListingImageUrl('group_image', $item->groupData->group_image);
                    $myJoinedGroups[$key]['my_joined_community_details'] = $this->getCommunityDataOfGroup($item->manage_group_xid);
                    $myJoinedGroups[$key]['membersCount'] = IamPrincipalManageGroupLink::where('manage_group_xid',$item->manage_group_xid)->count();
                    $myJoinedGroups[$key]['members_profile_photos'] = $this->getProfilePhotosOfAllUsersInGroup($item->manage_group_xid);

                }

            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $myJoinedGroups, 200);
        } catch (Exception $e) {
            Log::error('Fetch Joined Groups profile service function failes: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function getCommunityDataOfGroup($groupId)
    {
        $communityandGroupLinkData = ManageCommunityManageGroupsLink::select('id', 'manage_group_xid', 'manage_community_xid')->with([
            'communityData' => function ($query) {
                $query->select('id', 'community_name'); // Replace with the columns you need
            }
        ])->where('manage_group_xid', $groupId)->first();
        return $communityandGroupLinkData;
    }
    public function getProfilePhotosOfAllUsersInGroup($groupId)
    {
        $profilePhotos = IamPrincipalManageGroupLink::where('manage_group_xid',$groupId)->pluck('iam_principal_xid'); 
        $userProfile = IamPrincipal::select('id','profile_photo')->whereIn('id',$profilePhotos)->get();
        foreach($userProfile as $key => $userProfileItem){
            $userProfile[$key]['profile_photo'] = ListingImageUrl('profile_photos',$userProfileItem->profile_photo);
        }
        return $userProfile;
    }


    


    /* 
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch profile service
     */
    public function fetchFollowers($iamprincipal_id)
    {
        try {
            //updated by hritik on 19th July ,2024
            // ->whereNotIn('iam_principal_xid', IamPrincipalBlockedProfile::where('iam_principal_xid', $iamprincipal_id)->pluck('blocked_iam_principal_xid'))


            $data['following'] = IamPrincipalFollowers::where('iam_principal_xid', $iamprincipal_id)
                ->whereNotIn('iam_principal_xid', IamPrincipalBlockedProfile::where('iam_principal_xid', $iamprincipal_id)->pluck('blocked_iam_principal_xid'))
                ->count();

            // ->count();
            $data['followers'] = IamPrincipalFollowers::where('following_iam_principal_xid', $iamprincipal_id)
                ->whereNotIn('iam_principal_xid', IamPrincipalBlockedProfile::where('iam_principal_xid', $iamprincipal_id)->pluck('blocked_iam_principal_xid'))->count();
            return $data;
        } catch (Exception $e) {
            Log::error('Fetch follower service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To fetch notification service 
     */
    public function fetchNotificationStatusService($iam_principal_id)
    {
        try {
            $notificationStatus = IamPrincipal::select('group_notification', 'community_notification', 'follower_notification', 'new_follower_notification', 'direct_message_notification')
                ->where('id', $iam_principal_id)
                ->first();
            return jsonResponseWithSuccessMessageApi(__('Success.data_fetched_successfully'), $notificationStatus, 200);
        } catch (Exception $e) {
            Log::error('Get notification status service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To update notification service 
     */
    public function updateNotificationStatusService($request, $iam_principal_id)
    {
        try {
            DB::beginTransaction();
            IamPrincipal::where('id', $iam_principal_id)->update($request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.update_data'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update notification service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To store block profile
     */
    public function blockProfileService($request, $iam_principal_id)
    {
        try {
            DB::beginTransaction();
            $request['iam_principal_xid'] = $iam_principal_id;
            if (IamPrincipalBlockedProfile::where(['iam_principal_xid' => $iam_principal_id, 'blocked_iam_principal_xid' => $request->blocked_iam_principal_xid])->doesntExist()) {
                IamPrincipalBlockedProfile::create($request->all());
            } else {
                IamPrincipalBlockedProfile::where(['iam_principal_xid' => $iam_principal_id, 'blocked_iam_principal_xid' => $request->blocked_iam_principal_xid])->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Block profile service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch blocked profile
     */
    public function fetchBlockedProfileService($request)
    {
        try {
            $search = $request->search;
            $followers = IamPrincipalBlockedProfile::whereHas(
                'blockedProfile',
                function ($query) use ($search) {
                    $query->when($search != null, function ($q) use ($search) {
                        $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                        $q->where('user_name', 'like', '%' . $search . '%');
                        $q->orWhere('full_name', 'like', '%' . $search . '%');
                    }, function ($q) {
                        $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                    });
                }
            )
                ->with([
                    'blockedProfile' => function ($query) use ($search) {
                        $query->when($search != null, function ($q) use ($search) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                            $q->where('user_name', 'like', '%' . $search . '%');
                            $q->orWhere('full_name', 'like', '%' . $search . '%');
                        }, function ($q) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                        });
                    }
                ])
                ->select('blocked_iam_principal_xid', 'iam_principal_xid')
                ->where('iam_principal_xid', auth()->user()->id)
                ->get();
            if ($followers == null) {
                Log::info('Blocked profile data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $followers, 200);
        } catch (Exception $e) {
            Log::error('Fetch blocked profile service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch followers profile
     */
    public function fetchFollowersService($request)
    {
        try {
            $search = $request->search;
            $followers = IamPrincipalFollowers::whereHas('follower', function ($query) use ($search) {
                $query->when($search != null, function ($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                    $q->where('user_name', 'like', '%' . $search . '%');
                    $q->orWhere('full_name', 'like', '%' . $search . '%');
                }, function ($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                });
            })
                ->with([
                    'follower' => function ($query) use ($search) {
                        $query->when($search != null, function ($q) use ($search) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                            $q->where('user_name', 'like', '%' . $search . '%');
                            $q->orWhere('full_name', 'like', '%' . $search . '%');
                        }, function ($q) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                        });
                    }
                ])
                ->where('following_iam_principal_xid', auth()->user()->id)
                ->whereNotIn('iam_principal_xid', IamPrincipalBlockedProfile::where('iam_principal_xid', auth()->user()->id)->pluck('blocked_iam_principal_xid'))
                ->select('following_iam_principal_xid', 'iam_principal_xid')
                ->get();

            if ($followers == null) {
                Log::info('follower data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $followers, 201);
        } catch (Exception $e) {
            Log::error('Fetch follower service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch following profile
     */
    public function fetchFollowingsService($request)
    {
        try {
            $search = $request->search;
            $following = IamPrincipalFollowers::whereHas('following', function ($query) use ($search) {
                $query->when($search != null, function ($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                    $q->where('user_name', 'like', '%' . $search . '%');
                    $q->orWhere('full_name', 'like', '%' . $search . '%');
                }, function ($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                });
            })
                ->with([
                    'following' => function ($query) use ($search) {
                        $query->when($search != null, function ($q) use ($search) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                            $q->where('user_name', 'like', '%' . $search . '%');
                            $q->orWhere('full_name', 'like', '%' . $search . '%');
                        }, function ($q) {
                            $q->select('id', 'user_name', 'full_name', 'profile_photo', 'principal_type_xid');
                        });
                    }
                ])
                ->select('following_iam_principal_xid', 'iam_principal_xid')
                ->where('iam_principal_xid', auth()->user()->id)
                ->get();
            if ($following == null) {
                Log::info('following data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $following, 200);
        } catch (Exception $e) {
            Log::error('Fetch following service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To follow users
     */
    public function storeFollowUserService($request)
    {
        try {
            DB::beginTransaction();
            $iam_principal_id = auth()->user()->id;
            $request['iam_principal_xid'] = $iam_principal_id;
            if (IamPrincipalFollowers::where(['iam_principal_xid' => $iam_principal_id, 'following_iam_principal_xid' => $request->following_iam_principal_xid])->doesntExist()) {
                IamPrincipalFollowers::create($request->all());
            } else {
                IamPrincipalFollowers::where(['iam_principal_xid' => $iam_principal_id, 'following_iam_principal_xid' => $request->following_iam_principal_xid])->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Store follow user service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 17 July 2024
     * Use : To remove followes
     */
    public function removeFollower($request)
    {
        try {
            DB::beginTransaction();
            IamPrincipalFollowers::where(['iam_principal_xid' => $request->iam_principal_xid, 'following_iam_principal_xid' => auth()->user()->id])->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.update_data'), [], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Remove follower service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 17 July 2024
     * Use : To delete account
     */
    public function deleteMyAccount($request)
    {
        try {
            DB::beginTransaction();
            IamPrincipal::where('id', auth()->user()->id)->update(['is_deleted' => true, 'reason' => $request->reason]);
            DB::commit();
            return jsonResponseWithSuccessMessageApi('Account Deleted Successfully', [], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Delete my account service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 16 July 2024
     * Use : To make account private (account visibility)
     */
    public function accountVisibility($request)
    {
        try {
            DB::beginTransaction();
            IamPrincipal::where('id', auth()->user()->id)->update($request->all());
            DB::commit();
            $status = $request->is_account_visibility == 0 ? '(Private)' : '(Public)';
            return jsonResponseWithSuccessMessageApi('account visibility status changed to ' . $status, [], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('account visibility service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To Store Certification
     */
    public function storeCertificationOfUserService($request)
    {
        try {
            DB::beginTransaction();

            $certificationImage = $request->file('certification_image');
            $certificationImagePath = saveSingleImageWithoutCrop($certificationImage, 'certifications', null);


            $certificationData = IamPrincipalCertifications::create(
                [
                    'iam_principal_xid' => $request->iam_principal_xid,
                    'certification_name' => $request->certification_name,
                    'certification_reason' => $request->certification_reason,
                    'certification_image' => $certificationImagePath,
                    'certification_date' => $request->certification_date,
                ]
            );
            DB::commit();

            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $certificationData, 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Store Certification service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


}
