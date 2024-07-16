<?php

namespace App\Services\APIs;

use App\Models\IamPrincipal;
use App\Models\IamPrincipalBlockedProfile;
use App\Models\IamRole;
use App\Models\IamPrincipalFollowers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Hash;

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
                ['full_name' => $request->full_name,
                 'user_name' => $request->username,
                 'date_of_birth' => $request->date_of_birth,
                 'gender' => $request->gender,
                 'address_line1' => $request->location,
                 'profile_photo' => $profilePath,
                 'is_profile_updated'=>1
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

    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To fetch role master listing service
     */
    public function fetchRoleService()
    {
        try {
            $data = IamRole::select('id', 'role_name')
                ->where([['is_active', 1]])
                ->get();

            if ($data == null) {
                Log::info('role master data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch role master service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

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
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
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
            $validator = Validator::make($request->all(), [
                'email_address' => 'required|email|unique:iam_principal,email_address,'.$iamprincipal_id,
                'profile_image' => 'mimes:jpeg,jpg,png,gif|max:2048',
            ]);

            $userData = IamPrincipal::select('id','profile_photo')->where('id',$iamprincipal_id)->first();

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            if (isset($request->profile_image)) {
                $image = $request->profile_image;
                $image_db = null;
            } else {
                $image = null;
                $image_db = $userData->profile_photo;
            }
            if($request->has('profile_image'))
            {
                $img = saveSingleImageWithoutCrop($request->file('profile_image'), 'profile_image', $image_db);
                $request['profile_photo'] = $img;

                // remove profile_image key from request array
                $newArray = \Illuminate\Support\Arr::except($request->all(),['profile_image']);
            }

            $data = IamPrincipal::where('id',$iamprincipal_id)->update($newArray ?? $request->all());
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
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
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
    public function fetchProfileService($iamprincipal_id)
    {
        try {
            $data = IamPrincipal::with('interestsLink.interest')->where('id',$iamprincipal_id)->first();
            $interestName = [];
            if($data->interestsLink != null)
            {
                foreach($data->interestsLink as $interests)
                {
                    array_push($interestName,$interests->interest->name);
                }
                $data->interestName = $interestName;
            }
            $formatData = (array)[
                'id' => $data->id,
                'user_name' => $data->user_name,
                // 'pin' => $data->pin,
                'full_name' => $data->full_name,
                'gender' => $data->gender,
                'date_of_birth' => $data->date_of_birth,
                'interest' => $interestName,
                'about' => $data->about,
                'position' => $data->position,
                'training_scores' => $data->training_scores,
                'height' => $data->height,
                'weight' => $data->weight,
                'batting_average' => $data->batting_average,
                'follows' => $this->fetchFollowers($iamprincipal_id),
            ];

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $formatData,200);
        } catch (Exception $e) {
            Log::error('Fecth profile service function failes: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /* 
        * Created By : Ritikesh Yadav
        * Created At : 09 July 2024
        * Use : To fetch profile service
     */
    public function fetchFollowers($iamprincipal_id)
    {
        try{
            $data['following'] = IamPrincipalFollowers::where('iam_principal_xid',$iamprincipal_id)->count();
            $data['followers'] = IamPrincipalFollowers::where('following_iam_principal_xid',$iamprincipal_id)->count();
            return $data;
        }catch(Exception $e)
        {
            Log::error('Fetch follower service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To fetch notification service 
     */
    public function fetchNotificationStatusService($iam_principal_id)
    {
        try{
            $notificationStatus = IamPrincipal::select('group_notification','community_notification','follower_notification','new_follower_notification','direct_message_notification')
                ->where('id',$iam_principal_id)
                ->first();
            return jsonResponseWithSuccessMessageApi(__('Success.data_fetched_successfully'),$notificationStatus,200);
        }catch(Exception $e)
        {
            Log::error('Get notification status service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To update notification service 
     */
    public function updateNotificationStatusService($request,$iam_principal_id)
    {
        try{
            DB::beginTransaction();
            IamPrincipal::where('id',$iam_principal_id)->update($request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.update_data'),200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Update notification service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To store block profile
     */
    public function blockProfileService($request,$iam_principal_id)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = $iam_principal_id;
            if(IamPrincipalBlockedProfile::where(['iam_principal_xid'=>$iam_principal_id,'blocked_iam_principal_xid'=>$request->blocked_iam_principal_xid])->doesntExist())
            {
                IamPrincipalBlockedProfile::create($request->all());
            }else{
                IamPrincipalBlockedProfile::where(['iam_principal_xid'=>$iam_principal_id,'blocked_iam_principal_xid'=>$request->blocked_iam_principal_xid])->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Block profile service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch blocked profile
     */
    public function fetchBlockedProfileService()
    {
        try{
            $followers = IamPrincipalBlockedProfile::with(['blockedProfile'=>function($query){
                $query->select('id','user_name','full_name','profile_photo');
            }])
            ->select('blocked_iam_principal_xid','iam_principal_xid')
            ->where('iam_principal_xid',auth()->user()->id)
            ->get();
            if($followers == null)
            {
                Log::info('Blocked profile data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$followers,200);
        }catch(Exception $e)
        {
            Log::error('Fetch blocked profile service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch followers profile
     */
    public function fetchFollowersService($request)
    {
        try{
            $search = $request->search;
            $followers = IamPrincipalFollowers::whereHas('follower', function($query) use ($search) {
                $query->when($search != null, function($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo');
                    $q->where('user_name', 'like', '%'.$search.'%');
                    $q->orWhere('full_name', 'like', '%'.$search.'%');
                }, function($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo');
                });
            })
            ->with(['follower' => function($query) use ($search) {
                $query->when($search != null, function($q) use ($search) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo');
                    $q->where('user_name', 'like', '%'.$search.'%');
                    $q->orWhere('full_name', 'like', '%'.$search.'%');
                }, function($q) {
                    $q->select('id', 'user_name', 'full_name', 'profile_photo');
                });
            }])
            ->where('following_iam_principal_xid', auth()->user()->id)
            ->select('following_iam_principal_xid', 'iam_principal_xid')
            ->get();

            if($followers == null)
            {
                Log::info('follower data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);

            // return jsonResponseWithSuccessMessageApi("Shubhammmmmmmmm",$followers,200);
        }catch(Exception $e)
        {
            Log::error('Fetch follower service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch following profile
     */
    public function fetchFollowingsService($request)
    {
        try{
            $search = $request->search;
            $following = IamPrincipalFollowers::whereHas('following',function($query) use ($search){
                $query->when($search != null,function($q) use ($search){
                    $q->select('id','user_name','full_name','profile_photo');
                    $q->where('user_name','like','%'.$search.'%');
                    $q->orWhere('full_name','like','%'.$search.'%');
                },function($q){
                    $q->select('id','user_name','full_name','profile_photo');
                });
            })
            ->with(['following'=>function($query) use ($search){
                $query->when($search != null,function($q) use ($search){
                    $q->select('id','user_name','full_name','profile_photo');
                    $q->where('user_name','like','%'.$search.'%');
                    $q->orWhere('full_name','like','%'.$search.'%');
                },function($q){
                    $q->select('id','user_name','full_name','profile_photo');
                });
            }])
            ->select('following_iam_principal_xid','iam_principal_xid')
            ->where('iam_principal_xid',auth()->user()->id)
            ->get();
            if($following == null)
            {
                Log::info('following data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$following,200);
        }catch(Exception $e)
        {
            Log::error('Fetch following service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To follow users
     */
    public function storeFollowUserService($request)
    {
        try{
            DB::beginTransaction();
            $iam_principal_id = auth()->user()->id;
            $request['iam_principal_xid'] = $iam_principal_id;
            if(IamPrincipalFollowers::where(['iam_principal_xid'=>$iam_principal_id,'following_iam_principal_xid'=>$request->following_iam_principal_xid])->doesntExist())
            {
                IamPrincipalFollowers::create($request->all());
            }else{
                IamPrincipalFollowers::where(['iam_principal_xid'=>$iam_principal_id,'following_iam_principal_xid'=>$request->following_iam_principal_xid])->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store follow user service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 16 July 2024
     * Use : To remove followes
     */
    public function removeFollower($request)
    {
        try{
            DB::beginTransaction();
            IamPrincipalFollowers::where(['iam_principal_xid'=>$request->iam_principal_xid,'following_iam_principal_xid'=>auth()->user()->id])->delete();
            DB::commit();
            return jsonResponseWithSuccessMessage('Follower removed');

        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Remove follower service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}
