<?php

use App\Http\Controllers\APIs\AppleLoginApiController;
use App\Http\Controllers\APIs\AuthApiController;
use App\Http\Controllers\APIs\BusinessUserProfileController;
use App\Http\Controllers\APIs\FacebookLoginApiController;
use App\Http\Controllers\APIs\GoogleLoginApiController;

use App\Http\Controllers\APIs\LogoutApiController;
use App\Http\Controllers\APIs\ManageActivitiesApiController;
use App\Http\Controllers\APIs\ManageInterestApiController;
use App\Http\Controllers\APIs\ManageProductApiController;
use App\Http\Controllers\APIs\NotificationApiController;
use App\Http\Controllers\APIs\PriorityMasterApiController;
use App\Http\Controllers\APIs\ProfileDetailsApiController;

use App\Http\Controllers\APIs\ManageGroupsApiController;
use App\Http\Controllers\APIs\ManageCommunitiesApiController;

use Illuminate\Support\Facades\Route;


Route::middleware(['BasicAuthApi'])->group(function () {
    Route::prefix('/v1')->group(function () {
        //===================( Start Registration & Login API'S For Regroup )===================//

        //registration API
        Route::post('/send_otp', [AuthApiController::class, 'sendOtp']);
        Route::post('/verify_otp', [AuthApiController::class, 'verifykOtp']);
        Route::post('/login', [AuthApiController::class, 'login']);
        //===================( End Registration & Login API'S For Regroup )===================//    

        //Activity API's

        Route::get('/get_activity', [ManageActivitiesApiController::class, 'getActivity']);

        //===================( Registration & Login API'S )===================//
        Route::post('/registration_form', [AuthApiController::class, 'registrationForm']);

        Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
        Route::post('/forgot-password/verify-otp', [AuthApiController::class, 'verifyOtpForgotPassword']);
        Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
        
        
        Route::post('/resend-otp', [AuthApiController::class, 'resendOtp']);

        Route::post('/sign-in-with-google-login', [GoogleLoginApiController::class, 'signInWithGoogle']);
        Route::post('/apple-login', [AppleLoginApiController::class, 'appleLogin'])->name('apple.login');
        Route::post('/facebook-login', [FacebookLoginApiController::class, 'facebookLogin'])->name('facebook.login');
    });
    Route::group(['middleware' => ['BasicAuthApi','wdi.jwt.verify',]], function () {
        Route::prefix('/v1')->group(function () {


            //update User Account type when Register with apple of google
            Route::post('/update-user-account-type', [AuthApiController::class, 'updateUserAccountType']);

            
            //get AUth User 
            Route::get('/get-auth-user-data', [AuthApiController::class, 'getAuthUserDetails']);

            //Update Profile
            Route::post('/add_profile', [ProfileDetailsApiController::class, 'addProfile']);

            // fetch profile
            Route::get('/fetch-profile',[ProfileDetailsApiController::class, 'fetchProfile']);


            //Business user Tell Us about business API - created by hritik on 09-July,2024
            Route::post('/tell-us-about-your-business', [BusinessUserProfileController::class, 'tellUsAboutYourBusiness']);
            Route::post('/update-business-profile-step-1', [BusinessUserProfileController::class, 'updateBusinessProfile']);

            //Store Activities

            Route::post('/store_activities', [ManageActivitiesApiController::class, 'storeActivities']);
            // ===================( Logout API'S )=========================//
            Route::get('/user-logout', [LogoutApiController::class, 'userLogout']);



            // =====================( Product API'S )================================//
            Route::post('/add-product', [ManageProductApiController::class, 'addProduct']);
            Route::get('/fetch-product', [ManageProductApiController::class, 'fetchProduct']);
            Route::post('/edit-product', [ManageProductApiController::class, 'editProduct']);
            Route::post('/delete-product', [ManageProductApiController::class, 'deleteProduct']);

            //======================( Manage Activities API'S  )==============================//
            Route::post('/add-activities', [ManageActivitiesApiController::class, 'addManageActivities']);
            Route::get('/fetch-activities', [ManageActivitiesApiController::class, 'fetchManageActivities']);


            //======================( Profile Details API'S  )==============================//
            // Route::post('/add-profile', [ProfileDetailsApiController::class, 'addProfile']);
            Route::get('/fetch-role', [ProfileDetailsApiController::class, 'fetchRole']);
            Route::post('/update-profile', [ProfileDetailsApiController::class, 'updateProfile']);
            Route::post('/delete-profile', [ProfileDetailsApiController::class, 'deleteProfile']);

            //======================( Send Notifications API'S  )==============================//
            Route::post('/send-notification', [NotificationApiController::class, 'sendNotification']);
            Route::get('/listing-notification', [NotificationApiController::class, 'listingNotification']);

            //========================( Manage Interest API'S)=======================================//
            Route::get('/fetch-interests', [ManageInterestApiController::class, 'fetchManageInterests']);
            Route::post('/select-interests', [ManageInterestApiController::class, 'storeSelectedInterests']);

            //========================( Manage Groups API'S)=======================================//
            Route::get('/fetch-groups', [ManageGroupsApiController::class, 'fetchManageGroup']);
            Route::post('/select-groups', [ManageGroupsApiController::class, 'storeSelectedGroup']);


            //========================( Manage Communities API'S)=======================================//
            Route::get('/fetch-communities', [ManageCommunitiesApiController::class, 'fetchManageCommunities']);
            Route::post('/select-communities', [ManageCommunitiesApiController::class, 'storeSelectedCommunity']);

            // ================================send mail============================//
            Route::post('/send-mail',[ManageCommunitiesApiController::class,'sendMail']);
        });
    });
});
