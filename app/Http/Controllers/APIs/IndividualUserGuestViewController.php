<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\IndividualUserGuestViewService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class IndividualUserGuestViewController extends Controller
{
    protected $individualUserGuestViewService;
    public function __construct(IndividualUserGuestViewService $individualUserGuestViewService)
    {
        $this->individualUserGuestViewService = $individualUserGuestViewService;
    }

      
    /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To get Individual User Guest view Data
     */
   
    
    public function getIndividualUserGuestViewData(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
               
                $request['iam_principal_id'] = $token['sub'];
                return $this->individualUserGuestViewService->getIndividualUserGuestViewService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch get Individual User Guest View Data function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To get Business User Guest view Data
     */
   
    
     public function getBusinessUserGuestViewData(Request $request)
     {
         try {
             $token = readHeaderToken();
             if ($token) {
                
                 $request['iam_principal_id'] = $token['sub'];
                 return $this->individualUserGuestViewService->getBusinessUserGuestViewService($request);
             } else {
                 return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
             }
         } catch (Exception $ex) {
             Log::error('fetch get Individual User Guest View Data function failed: ' . $ex->getMessage());
             return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
         }
     }
 

    
  /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To fetch Follower of Guest User
     */
    public function getFollowersOfGuestUser(Request $request)
    {
        try{
            return $this->individualUserGuestViewService->fetchGuestUserFollowersService($request);
        }catch(Exception $e)    
        {
            Log::error('Fetch follower function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }



     /**
     * Created By : Hritik
     * Created At : 19 July 2024
     * Use : To fetch Following of Guest User
     */
    public function getFollowingOfGuestUser(Request $request)
    {
        try{
            return $this->individualUserGuestViewService->fetchGuestUserFollowingService($request);
        }catch(Exception $e)    
        {
            Log::error('Fetch Following function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }


}
