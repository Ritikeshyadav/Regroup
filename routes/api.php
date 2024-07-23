<?php

use App\Http\Controllers\APIs\AccountSessionController;
use App\Http\Controllers\APIs\AppleLoginApiController;
use App\Http\Controllers\APIs\AuthApiController;
use App\Http\Controllers\APIs\BusinessUserProfileController;
use App\Http\Controllers\APIs\FacebookLoginApiController;
use App\Http\Controllers\APIs\GoogleLoginApiController;

use App\Http\Controllers\APIs\IndividualUserGuestViewController;
use App\Http\Controllers\APIs\LogoutApiController;
use App\Http\Controllers\APIs\ManageActivitiesApiController;
use App\Http\Controllers\APIs\ManageCMSController;
use App\Http\Controllers\APIs\ManageInterestApiController;
use App\Http\Controllers\APIs\ManageProductApiController;
use App\Http\Controllers\APIs\NotificationApiController;
use App\Http\Controllers\APIs\PriorityMasterApiController;
use App\Http\Controllers\APIs\ProfileDetailsApiController;

use App\Http\Controllers\APIs\ManageGroupsApiController;
use App\Http\Controllers\APIs\ManageCommunitiesApiController;
use App\Http\Controllers\APIs\ManagePostsApiController;
use App\Http\Controllers\APIs\TimeLineController;
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
        Route::post('/apple-login', [AppleLoginApiController::class, 'appleLogin'])->name('apple.login'); //not in use
        Route::post('/apple-login-or-registration', [AppleLoginApiController::class, 'appleRegistration'])->name('apple-login-or-registration');


        Route::post('/facebook-login', [FacebookLoginApiController::class, 'facebookLogin'])->name('facebook.login');
    });
    Route::group(['middleware' => ['wdi.jwt.verify']], function () {
        Route::prefix('/v1')->group(function () {


            Route::post('/logout', [AuthApiController::class, 'logout']);

            //update User Account type when Register with apple of google
            Route::post('/update-user-account-type', [AuthApiController::class, 'updateUserAccountType']);


            //get AUth User 
            Route::get('/get-auth-user-data', [AuthApiController::class, 'getAuthUserDetails']);

            //Update Profile
            Route::post('/add_profile', [ProfileDetailsApiController::class, 'addProfile']);

            // fetch profile
            Route::get('/fetch-profile', [ProfileDetailsApiController::class, 'fetchProfile']);


            //Business user Tell Us about business API - created by hritik on 09-July,2024
            Route::post('/tell-us-about-your-business', [BusinessUserProfileController::class, 'tellUsAboutYourBusiness']);
            Route::post('/update-business-profile-step-1', [BusinessUserProfileController::class, 'updateBusinessProfile']);

            // fetch or update bussiness profile
            Route::get('/fetch-business-profile', [BusinessUserProfileController::class, 'fetchBusinessProfile']);
            Route::post('/update-business-profile', [BusinessUserProfileController::class, 'updateBusinessProfileFunction']);

            // update password
            Route::post('/update-password-send-otp', [BusinessUserProfileController::class, 'updatePasswordSendMailOtp']);
            Route::post('/verify-update-password-otp', [BusinessUserProfileController::class, 'verifyUpdatePasswordOtp']);

            //Store Activities
            Route::post('/store_activities', [ManageActivitiesApiController::class, 'storeActivities']);

            // ===================( Logout API'S )=========================//
            Route::get('/user-logout', [LogoutApiController::class, 'userLogout']);




            //======================( Manage Activities API'S  )==============================//
            Route::post('/add-activities', [ManageActivitiesApiController::class, 'addManageActivities']);
            Route::get('/fetch-activities', [ManageActivitiesApiController::class, 'fetchManageActivities']);


            //======================( Profile Details API'S  )==============================//
            // Route::post('/add-profile', [ProfileDetailsApiController::class, 'addProfile']);
            Route::get('/fetch-role', [ProfileDetailsApiController::class, 'fetchRole']);
            Route::post('/update-profile', [ProfileDetailsApiController::class, 'updateProfile']);
            Route::post('/delete-profile', [ProfileDetailsApiController::class, 'deleteProfile']);
            Route::post('/block-profile', [ProfileDetailsApiController::class, 'blockProfile']);
            Route::post('/share-profile', [ProfileDetailsApiController::class, 'shareProfile']);
            Route::get('/fetch-blocked-profile', [ProfileDetailsApiController::class, 'fetchBlockedProfile']);
            Route::post('/delete-my-account', [ProfileDetailsApiController::class, 'deleteMyAccount']);
            Route::post('/account-visibility', [ProfileDetailsApiController::class, 'accountVisibility']);

            // ============================( Follow API's)===================================//
            Route::get('/fetch-followers', [ProfileDetailsApiController::class, 'fetchFollowers']);
            Route::get('/fetch-following', [ProfileDetailsApiController::class, 'fetchFollowings']);
            Route::post('/follow-user', [ProfileDetailsApiController::class, 'followUsers']);
            Route::post('/remove-follower', [ProfileDetailsApiController::class, 'removeFollower']);


            //========================( Manage Interest API'S)=======================================//
            Route::get('/fetch-interests', [ManageInterestApiController::class, 'fetchManageInterests']);
            Route::post('/select-interests', [ManageInterestApiController::class, 'storeSelectedInterests']);
            Route::post('/remove-interests', [ManageInterestApiController::class, 'removeInterest']);

            //========================( Manage Groups API'S)=======================================//
            Route::get('/fetch-groups', [ManageGroupsApiController::class, 'fetchManageGroup']);
            Route::post('/select-groups', [ManageGroupsApiController::class, 'storeSelectedGroup']);
            Route::get('/search-group', [ManageGroupsApiController::class, 'seachGroup']);




            //========================( Manage Communities API'S)=======================================//
            Route::get('/fetch-communities', [ManageCommunitiesApiController::class, 'fetchManageCommunities']);
            Route::post('/select-communities', [ManageCommunitiesApiController::class, 'storeSelectedCommunity']);
            Route::get('/search-community', [ManageCommunitiesApiController::class, 'searchCommunity']);
            Route::post('/create-community', [ManageCommunitiesApiController::class, 'createCommunity']);
            Route::get('/edit-community', [ManageCommunitiesApiController::class, 'editCommunity']);

            Route::post('/update-community', [ManageCommunitiesApiController::class, 'updateCommunity']);
            
            
            
            // ================================send mail============================//
            Route::post('/send-mail', [ManageCommunitiesApiController::class, 'sendMail']);

            // ===========================( Manage CMS Api)===============================//
            Route::get('/fetch-faqs', [ManageCMSController::class, 'fetchFAQs']);
            Route::post('/contact-us', [ManageCMSController::class, 'storeContactUs']);
            Route::post('/bug-report', [ManageCMSController::class, 'storeBugReport']);
            Route::get('/fetch-privacy-policy', [ManageCMSController::class, 'fetchPrivacyPolicy']);
            Route::get('/fetch-terms-and-condition', [ManageCMSController::class, 'fetchTermsAndCondition']);

            // ===============================( Notification's )=============================== //
            Route::get('/fetch-notification-settings', [ProfileDetailsApiController::class, 'fetchNotificationSetting']);
            Route::post('/update-notification-settings', [ProfileDetailsApiController::class, 'updateNotificationSetting']);

            // ===============================( Timeline's )=============================== //
            Route::post('/create-timeline', [TimeLineController::class, 'createTimeLine']);
            Route::get('/list-of-abilities', [TimeLineController::class, 'listOfAbilities']);
            Route::get('/get-timeline-data', [TimeLineController::class, 'getsingleTimelineData']);
            Route::post('/update-timeline', [TimeLineController::class, 'updateTimeline']);
            Route::post('/delete-timeline', [TimeLineController::class, 'deleteTimeline']);


            // ===============================( Account Session's )=============================== //
            Route::post('/store-account-session', [AccountSessionController::class, 'storeAccountSession']);
            Route::get('/get-account-session', [AccountSessionController::class, 'getAccountSessions']);

            // ===============================( Guest View of Individual User's )=============================== //
            Route::get('/get-guest-view-of-individual-user-profile', [IndividualUserGuestViewController::class, 'getIndividualUserGuestViewData']);
            Route::get('/get-guest-user-followers', [IndividualUserGuestViewController::class, 'getFollowersOfGuestUser']);
            Route::get('/get-guest-user-following', [IndividualUserGuestViewController::class, 'getFollowingOfGuestUser']);

            // ===============================( Guest View of Individual User's )=============================== //
            Route::get('/get-guest-view-of-individual-user-profile', [IndividualUserGuestViewController::class, 'getIndividualUserGuestViewData']);

            Route::get('/get-guest-user-followers', [IndividualUserGuestViewController::class, 'getFollowersOfGuestUser']);
            Route::get('/get-guest-user-following', [IndividualUserGuestViewController::class, 'getFollowingOfGuestUser']);

            Route::get('/get-guest-view-of-business-user-profile', [IndividualUserGuestViewController::class, 'getBusinessUserGuestViewData']);
            // ===============================( Certifications )=============================== //

            Route::post('/store-certification', [ProfileDetailsApiController::class, 'storeCertification']);
            Route::post('/delete-certification', [ProfileDetailsApiController::class, 'deleteCertification']);
            Route::get('/my-joined-groups',[ProfileDetailsApiController::class, 'myJoinedGroups']);              
            
            Route::controller(ManagePostsApiController::class)->group(function(){

                // ==================================( Manage Post's )=======================================
                Route::get('fetch-communities-with-tags','fetchCommunitiesWithTags');
                Route::post('store-tags','storeTags');
                Route::post('store-post','storePost');
                Route::get('fetch-post','fetchPost');
                Route::get('fetch-latest-post','fetchLatestPost');
                Route::post('like-post','storePostLike');
                Route::get('fetch-like-icons','fetchLikeIcons');
                Route::post('save-post','savePost');
                Route::post('fetch-like-list','fetchUserLikedList');

                // ===================================( Post Comments )================================
                Route::post('store-comment','commentOnPost');
                Route::post('reply-on-comment','replyOnComment');
                Route::post('delete-comment','deleteComment');
                Route::post('delete-reply-on-comment','deleteReplyOnComment');
                Route::post('fetch-comment-with-replied-comment','fetchCommentWithRepliedComment');
            });
        });
    });
});
