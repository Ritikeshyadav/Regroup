<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\IamPrincipalCertifications;
use App\Services\APIs\ProfileDetailsApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileDetailsApiController extends Controller
{
    protected $ProfileDetailsApiService;
    public function __construct(ProfileDetailsApiService $ProfileDetailsApiService)
    {
        $this->ProfileDetailsApiService = $ProfileDetailsApiService;
    }

    /**
     * Created By : Vedant Chavan
     * Created At : 03 July 2024
     * Use : To add profile details
     */
    public function addProfile(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                
                $validator = $this->validateUserDetails($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Registration form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->addProfileDetailService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    // /**
    //  * Created By : Chandan Yadav
    //  * Created At : 08 April 2024
    //  * Use : To role master listing 
    //  */
    // public function fetchRole(Request $request)
    // {
    //     try {
    //         $token = readHeaderToken();
    //         if ($token) {
    //             $iamprincipal_id = $token['sub'];
    //             return $this->ProfileDetailsApiService->fetchRoleService($iamprincipal_id, $request);
    //         } else {
    //             return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
    //         }
    //     } catch (Exception $ex) {
    //         Log::error('fetch role master function failed: ' . $ex->getMessage());
    //         return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
    //     }
    // }


    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To update profile  
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // 'email_address' => 'required|email|unique:iam_principal,email_address,' . auth()->user()->id,
                'full_name' => 'required',
                'profile_image' => 'nullable|mimes:jpeg,jpg,png,gif|max:2048',
                'user_name' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
                'interest.*' => 'required|numeric',
                'about' => 'required',
                'position' => 'required',
                'training_scores' => 'required',
                'height' => 'required',
                'weight' => 'required',
                'batting_average' => 'required',
                'address_line1' => 'required',
            ]);
            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 403);
            }
            return $this->ProfileDetailsApiService->updateBothProfileService(auth()->user()->id, $request);
        } catch (Exception $ex) {
            Log::error('update profile function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 08 April 2024
     * Use : To delete profile  
     */
    public function deleteProfile(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ProfileDetailsApiService->deleteProfileService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete profile controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    /**
     * Created By : Vedant Chavan
     * Created at : 03 July 2024
     * Use : To validate Profile User Data
     */

    public function validateUserDetails(Request $request){
        return Validator::make(
            $request->all(),
            [
                'full_name' => 'required',
                'username' => 'required',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender' => 'required',
                'profile_photo' => 'required',
                'location' => 'required',
                
            ],
        );
    }

    /*
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch user profile
    */
    public function fetchProfile()
    {
        try{
            $token = readHeaderToken();
            if($token)
            {
                return $this->ProfileDetailsApiService->fetchProfileService($token['sub'],null);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('Fetch profile function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

 /*
     * Created By : Hritik
     * Created At : 09 July 2024
     * Use : To fetch user Joined Group
    */
    public function myJoinedGroups(Request $request)
    {
        try{
            $token = readHeaderToken();
            if($token)
            {
               
                return $this->ProfileDetailsApiService->myJoinedGroupsApiSerice($request);
            }
            return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('Fetch myJoinedGroups function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
 /*
     * Created By : Hritik
     * Created At : 24 July 2024
     * Use : To fetch user Certification List setting
    */
    public function myCertificateLists(Request $request)
    {
        try {
            $userId = $request->query('user_id');

            if($userId == null){
                return jsonResponseWithErrorMessageApi("Kindly Pass User Id in Query Params", 500);

            }

            $userCertifications = IamPrincipalCertifications::select('id', 'certification_name', 'certification_image', 'certification_reason', 'certification_date', 'iam_principal_xid')->where('iam_principal_xid', $userId)->get();
            foreach ($userCertifications as $key => $val) {
                $userCertifications[$key]['certification_image'] = ListingImageUrl('certifications', $val->certification_image);
            }
                    

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $userCertifications, 200);
        } catch (Exception $e) {
            Log::error('Fecth Certification of User service function failes: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    
    
    /*
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch user notification setting
    */
    public function fetchNotificationSetting()
    {
        try{
            return $this->ProfileDetailsApiService->fetchNotificationStatusService(auth()->user()->id);
        }catch(Exception $e)
        {
            Log::error('Fetch notification setting function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch user notification setting
    */
    public function updateNotificationSetting(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'group_notification' => 'nullable',
                'community_notification' => 'nullable',
                'follower_notification' => 'nullable',
                'new_follower_notification' => 'nullable',
                'direct_message_notification' => 'nullable',
            ]);
            if($validator->fails())
            {
                Log::error('Update notificaiton status validation failed: '. $validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->updateNotificationStatusService($request,auth()->user()->id);
        }catch(Exception $e)
        {
            Log::error('update notification setting function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
     * Created By : Ritikesh Yadav
     * Created At : 09 July 2024
     * Use : To fetch user notification setting
    */
    public function blockProfile(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'blocked_iam_principal_xid' => 'required',
            ]);
            if($validator->fails())
            {
                Log::error('block profile function validation error : '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->blockProfileService($request,auth()->user()->id);
        }catch(Exception $e)
        {
            Log::error('Blocked profile function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch blocked profile
     */
    public function fetchBlockedProfile(Request $request)
    {
        try{
            return $this->ProfileDetailsApiService->fetchBlockedProfileService($request);
        }catch(Exception $e)
        {
            Log::error('Fetch blocked profile function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch Follower
     */
    public function fetchFollowers(Request $request)
    {
        try{
            return $this->ProfileDetailsApiService->fetchFollowersService($request);
        }catch(Exception $e)    
        {
            Log::error('Fetch follower function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To fetch Following
     */
    public function fetchFollowings(Request $request)
    {
        try{
            return $this->ProfileDetailsApiService->fetchFollowingsService($request);
        }catch(Exception $e)
        {
            Log::error('Fetch following function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 12 July 2024
     * Use : To follow users
     */
    public function followUsers(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'following_iam_principal_xid'=>'required',
            ]);
            if($validator->fails())
            {
                Log::error('Follow Users function validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->storeFollowUserService($request);
        }catch(Exception $e)
        {
            Log::error('Follow user function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 17 July 2024
     * Use : To remove follower
     */
    public function removeFollower(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),['iam_principal_xid'=>'required|exists:iam_principal_followers,iam_principal_xid']);
            if($validator->fails())
            {
                log::info('Remove follower function validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->removeFollower($request);
        }catch(Exception $e)
        {
            Log::error('Remove follower function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 17 July 2024
     * Use : To delete account
     */
    public function deleteMyAccount(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),['reason'=>'required']);
            if($validator->fails())
            {
                log::info('Delete account API validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->deleteMyAccount($request);
        }catch(Exception $e)
        {
            Log::error('Delete my account function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 17 July 2024
     * Use : To make account private (account visibility)
     */
    public function accountVisibility(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),['is_account_visibility'=>'required']);
            if($validator->fails())
            {
                Log::info('Account visibility validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            return $this->ProfileDetailsApiService->accountVisibility($request);
        }catch(Exception $e)
        {
            Log::error('Account visibility function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }


    /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To store Certifications
     **/
    public function storeCertification(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                
                'certification_name'=>'required',
                'certification_reason'=>'required',
                'certification_image'=>'required|mimes:png,jpg,jpeg|max:2048',
                'certification_date'=>'required',
                
            ]);
            if($validator->fails())
            {
                Log::info('Store Certification validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            $request['iam_principal_xid']= auth()->user()->id;
            return $this->ProfileDetailsApiService->storeCertificationOfUserService($request);
        }catch(Exception $e)
        {
            Log::error('Account visibility function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }


  /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To store Certifications
     **/
    public function deleteCertification(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                
                'certification_id'=>'required|integer',
                
            ]);
            if($validator->fails())
            {
                Log::info('Delete Certification validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            $request['iam_principal_xid']= auth()->user()->id;

            $deleteCertification = IamPrincipalCertifications::where('id',$request->certification_id)->delete();
            return jsonResponseWithSuccessMessageApi(__('success.delete'), [], 200);

        }catch(Exception $e)
        {
            Log::error('Delete Certification function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    


    
}
