<?php

namespace App\Services\APIs;

use App\Mail\ForgotPasswordOtp;
use App\Mail\SendOtp;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalBusinessUserLink;
use App\Models\IamPrincipalOtp;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusinessProfileDetailsApiService
{



    /**
     * Created By : Hritik D
     * Created At : 09 July 2024
     * Use : To add Tell us About Yourself Of Bussines User- Service
     */
    public function tellUsAboutYourBusinessFormService($request, $iamprincipal_id)
    {
        try {
            DB::beginTransaction();

            // dd($request->all(),$iamprincipal_id);



            $profileData = IamPrincipalBusinessUserLink::updateOrCreate(
                ['id' => $iamprincipal_id],
                [
                    'business_type_xid' => $request->business_type_xid,
                    'business_owner_name' => $request->business_owner_name,
                    'business_name' => $request->business_name,
                    'business_location' => $request->business_location,
                    'iam_principal_xid' => $iamprincipal_id
                ]
            );

            $iamPrincipalData = IamPrincipal::where('id', $iamprincipal_id)->update(['is_profile_updated' => 1]);
            DB::commit();
            // $responseData['profile'] = $profileData;
            // $responseData['iam_principal_data'] = $iamPrincipalData;

            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $profileData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' tellUsAboutYourBusinessFormService service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }




    public function updateBusinessProfileStepOneService($request)
    {
        try {
            DB::beginTransaction();

            // dd($request->all(),$iamprincipal_id);
            $iamprincipal_id = $request->iam_principal_xid;

            if ($request->hasFile('business_logo')) {
                $image = $request->file('business_logo');
                $businessLogo = saveSingleImageWithoutCrop($image, 'business_logo', null);
            } else {
                $businessLogo = null;
            }


            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $bannerImage = saveSingleImageWithoutCrop($image, 'banner_image', null);
            } else {
                $bannerImage = null;
            }


            $profileData = IamPrincipalBusinessUserLink::updateOrCreate(
                ['id' => $iamprincipal_id],
                [
                    'business_contact_number' => $request->business_contact_number,
                    'business_email' => $request->business_email,
                    'business_handle' => $request->business_handle,
                    'opening_hours' => $request->opening_hours,
                    'website_link' => $request->website_link,

                    'google_review_link' => $request->google_review_link,
                    'tags' => $request->tags,
                    'business_logo' => $businessLogo,
                    'banner_image' =>$bannerImage,


                ]
            );

            // $iamPrincipalData = IamPrincipal::where('id', $iamprincipal_id)->update(['is_profile_updated' => 1]);
            DB::commit();
            // $responseData['profile'] = $profileData;
            // $responseData['iam_principal_data'] = $iamPrincipalData;

            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $profileData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' tellUsAboutYourBusinessFormService service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}




