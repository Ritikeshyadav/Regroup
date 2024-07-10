<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\APIs\ManageCommunitiesApiService;
use App\Models\ManageCommunity;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;



class ManageCommunitiesApiController extends Controller
{
    protected $manageCommunitiesApiService;

    public function __construct(ManageCommunitiesApiService $manageCommunitiesApiService)
    {
        $this->manageCommunitiesApiService = $manageCommunitiesApiService;
    }

    public function fetchManageCommunities()
    {
        try{
            $token = readHeaderToken();
            return $token ? $this->manageCommunitiesApiService->fetchManageCommunities() : jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'),409);
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
    
}
