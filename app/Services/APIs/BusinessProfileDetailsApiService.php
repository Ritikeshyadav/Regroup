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
use App\Mail\UpdatePasswordOtp;
use App\Services\APIs\ProfileDetailsApiService;

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

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : To fetch business profile service 
     */
    public function fetchBusinessProfileService($iamprincipal_id)
    {
        try{
            $data = IamPrincipalBusinessUserLink::with('businessType')
            ->select('id','business_type_xid','business_owner_name','business_name','business_location','business_contact_number','business_email','business_handle','website_link','google_review_link','business_logo','tags','banner_image')
            ->where('iam_principal_xid',$iamprincipal_id)
            ->first();
            $data['follows'] = (new ProfileDetailsApiService)->fetchFollowers($iamprincipal_id);
            if ($data == null) {
                Log::info('business profile data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch business profile service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : To update business profile service 
     */
    public function updateBusinessProfile($iamprincipal_id,$request)
    {
        try{
            DB::beginTransaction();
            // getting old image
            $businessData = IamPrincipalBusinessUserLink::where('iam_principal_xid',$iamprincipal_id)->first();
            if (isset($request->business_profile)) {
                $image = $request->business_profile;
                $image_db = null;
            } else {
                $image = null;
                $image_db = $businessData->business_profile_image;
            }
            if($request->has('business_profile'))
            {
                $img = saveSingleImageWithoutCrop($request->file('business_profile'), 'business_profile', $image_db);
                $request['business_profile_image'] = $img;

                // remove profile_image key from request array
                $newArray = \Illuminate\Support\Arr::except($request->all(),['business_profile']);
            }
            $businessData = IamPrincipalBusinessUserLink::where('iam_principal_xid',$iamprincipal_id)->update($newArray ?? $request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $businessData, 201);

        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Update business profile service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : To send otp on mail for update password service 
     */
    public function sendMailOtpForUpdatePasswordService($email,$iamprincipal_id)
    {
        try{
            $otp = generateRandomOTP();
            IamPrincipalOtp::updateOrCreate(['principal_xid'=>$iamprincipal_id],[
                'email_id' => $email,
                'otp_code' => $otp,
                'otp_purpose' => 'Update Password',
                'valid_till' => Carbon::now()->addMinutes(2),
                'is_used' => 0,
            ]);
            Mail::to($email)->send(new UpdatePasswordOtp($otp));
            return jsonResponseWithSuccessMessageApi(__('success.otp_sent_successfully'), [], 200);
        }catch(Exception $e)
        {
            Log::error('Send mail otp for update password service function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : To send otp on mail for update password service 
     */
    public function verifyOtpForUpdatePasswordService($iamprincipal_id,$request)
    {
        try{
            DB::beginTransaction();
            $storedOtp = IamPrincipalOtp::where("iam_principal_xid",$iamprincipal_id)->first();
            if($storedOtp->valid_till > carbon::now() || $storedOtp->otp_code != $request->otp)
            {
                $validationErrors = $storedOtp > carbon::now() ? 'OTP has been expired!' : 'OTP not matched!';
                Log::error("Update Business Profile validation error: " . $validationErrors);
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }
            if($storedOtp->otp_code == $request->otp)
            {
                IamPrincipal::where('id',$iamprincipal_id)->update(['password_hash'=>Hash::make($request->new_password)]);
                DB::commit();
                return jsonResponseWithSuccessMessageApi(__('success.update_data'),200);
            }
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Verify Otp for update password service function: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}




