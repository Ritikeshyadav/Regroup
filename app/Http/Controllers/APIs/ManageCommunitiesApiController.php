<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\ManageCommunityType;
use Illuminate\Http\Request;
use App\Services\APIs\ManageCommunitiesApiService;
use App\Models\ManageCommunity;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;

use Illuminate\Support\Facades\Validator;


class ManageCommunitiesApiController extends Controller
{
    protected $manageCommunitiesApiService;

    public function __construct(ManageCommunitiesApiService $manageCommunitiesApiService)
    {
        $this->manageCommunitiesApiService = $manageCommunitiesApiService;
    }

    public function fetchManageCommunities(Request $request)
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCommunitiesApiService->fetchManageCommunities($request) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
        }catch(Exception $e){
            Log::error('fetch manage communities function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function storeSelectedCommunity(Request $request)
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCommunitiesApiService->StoreUserSelectedCommunity($request->all(), $token['sub']) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        }catch(Exception $e)
        {
            Log::error('store user selected communities function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    public function sendMail(Request $request)
    {
        try{
            $email = $request->email;
            $mailData['body'] = rand(1000,9999);
            Mail::to($email)->send(new SendOtp($mailData));
            return jsonResponseWithSuccessMessageApi(__('success.send_mail'), 200);
        }catch(Exception $e)
        {
            Log::error('Error in sending mail: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

     /**
     * Created By : Hritik
     * Created at : 10 July 2024
     * Use : To Search Group by name Your Password
     */
    public function searchCommunity(Request $request)
    {
        try {
            $token = readHeaderToken();
            $searchText = $request->query('search_data');
           
            return $token ? $this->manageCommunitiesApiService->searchCommunityDataService($request) : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
        } catch (Exception $e) {
            Log::error('Search Group group function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


   
  /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To Create Communitu
     */
    public function createCommunity(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                
                'community_profile_photo' => 'required|mimes:png,jpg,jpeg|max:2048',
                'community_banner_image' => 'required|mimes:png,jpg,jpeg|max:2048',
                'community_name' => 'required',
                'community_location' => 'required',
                'community_description' => 'required',
                'community_type_xid' => 'required|exists:manage_community_types,id',
                'activity_xid' => 'required|exists:manage_activities,id',

                
            ]);
            if($validator->fails())
            {
                Log::info('Store Community validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
            $request['iam_principal_xid'] = auth()->user()->id;
            return $this->manageCommunitiesApiService->createCommunityApiService($request);
        }catch(Exception $e)    
        {
            Log::error('Create Community function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }


        /**
     * Created By : Hritik
     * Created at : 22 July 2024
     * Use : To get Community Details Of User
     */
    public function editCommunity(Request $request)
    {
        try {

            $communityId = $request->query('community_id');
            $communityData = ManageCommunity::with('activityData','communityTypeData')->select('id','community_profile_photo','community_banner_image','community_name','community_location','community_description','community_type_xid','activity_xid','is_active')->where('id',$communityId)->first();
          
            $communityData->community_profile_photo = ListingImageUrl('community_profile_photo',$communityData->community_profile_photo);
            $communityData->community_banner_image = ListingImageUrl('community_banner_image',$communityData->community_banner_image);

            $typesOfCommunity = ManageCommunityType::select('id','name')->get();
            $result = [
                'typesOfCommunity'=>$typesOfCommunity,
                'communityData'=>$communityData
            ];

           

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $result, 200);
        } catch (Exception $e) {
            Log::error('Fetch a  Timeline function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


  /**
     * Created By : Hritik
     * Created at : 22 July 2024
     * Use : To Update Community Details Of Admin
     */

    public function updateCommunity(Request $request)
    {

        try {

            $validator = Validator::make($request->all(),[
                
                'community_profile_photo' => 'required|mimes:png,jpg,jpeg|max:2048',
                'community_banner_image' => 'required|mimes:png,jpg,jpeg|max:2048',
                'community_name' => 'required',
                'community_location' => 'required',
                'community_description' => 'required',
                'community_type_xid' => 'required|exists:manage_community_types,id',
                'activity_xid' => 'required|exists:manage_activities,id',
                'community_id'=>'required|exists:manage_communities,id',

                
            ]);
            if($validator->fails())
            {
                Log::info('Update Community validation error: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors()->all(),403);
            }
          

            $iamprincipal_id = auth()->user()->id;
            $request['iam_principal_xid'] = $iamprincipal_id;
            return $this->manageCommunitiesApiService->updateCommunityApiService($request);
       

        } catch (Exception $ex) {
            Log::error('Update Community details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    
    
    
}
