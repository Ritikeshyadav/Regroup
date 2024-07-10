<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\BusinessProfileDetailsApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BusinessUserProfileController extends Controller
{
    protected $BusinessProfileDetailsApiService;
    public function __construct(BusinessProfileDetailsApiService $BusinessProfileDetailsApiService)
    {
        $this->BusinessProfileDetailsApiService = $BusinessProfileDetailsApiService;
    }

    /**
     * Created By : Hritik Yadav
     * Created at : 09 July 2024
     * Use : To Store Tell Us About Business Details
     */

    public function tellUsAboutYourBusiness(Request $request)
    {

        try {
            $token = readHeaderToken();
            if ($token) {

                $validator = $this->validateTellUsAboutFormOfBusinessUser($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Tell Us About YourSelf of business form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                return $this->BusinessProfileDetailsApiService->tellUsAboutYourBusinessFormService($request, $iamprincipal_id);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


      /**
     * Created By : Hritik Yadav
     * Created at : 09 July 2024
     * Use : To Update Business Profile at step 1
     */
    public function updateBusinessProfile(Request $request)
    {

        try {
            $token = readHeaderToken();
            if ($token) {

                $validator = $this->vallidateUpdateBusinessProfileForm($request);
                if ($validator->fails()) {
                    $validationErrors = $validator->errors()->all();
                    Log::error("Update Business Profile form validation error: " . implode(", ", $validationErrors));
                    return jsonResponseWithErrorMessageApi($validationErrors, 403);
                }
                $iamprincipal_id = $token['sub'];
                $user_id = $token['sub'];
                $request['iam_principal_xid'] = $user_id;

                return $this->BusinessProfileDetailsApiService->updateBusinessProfileStepOneService($request);

            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    


    public function validateTellUsAboutFormOfBusinessUser(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'business_name' => 'required',
                'business_owner_name' => 'required',
                'business_location' => 'required',
                'business_type_xid' =>'required'
                
            ],
        );
    }
    public function vallidateUpdateBusinessProfileForm(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'business_contact_number' => 'required|integer|digits:10',
                'business_email' => 'required|email|max:50',//regex:/^[\w\.\-]+@([\w\-]+\.)+[\w\-]{2,4}$/
                'business_handle' =>'required',
                'opening_hours' => 'required',
                'website_link'=>'required',
                'google_review_link'=>'required',
                'tags'=>'required',
                'business_logo'=>'required',
                'banner_image'=>'required',
                
            ],
        );
    }


    

}
