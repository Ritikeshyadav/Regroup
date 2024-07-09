<?php

use App\Http\Controllers\APIs\AppleLoginApiController;
use App\Http\Controllers\APIs\AuthApiController;
use App\Http\Controllers\APIs\ColorsApiController;
use App\Http\Controllers\APIs\FacebookLoginApiController;
use App\Http\Controllers\APIs\GoogleLoginApiController;
use App\Http\Controllers\APIs\IconApiController;
use App\Http\Controllers\APIs\LogoutApiController;
use App\Http\Controllers\APIs\ManageActivitiesApiController;
use App\Http\Controllers\APIs\ManageInterestApiController;
use App\Http\Controllers\APIs\ManageProductApiController;
use App\Http\Controllers\APIs\NotificationApiController;
use App\Http\Controllers\APIs\PriorityMasterApiController;
use App\Http\Controllers\APIs\ProfileDetailsApiController;
use App\Http\Controllers\APIs\SpaceApiController;
use App\Http\Controllers\APIs\SpaceFolderApiController;
use App\Http\Controllers\APIs\SpaceFolderListLinkApiController;
use App\Http\Controllers\APIs\SpaceFolderListTaskLinkApiController;
use App\Http\Controllers\APIs\StatusMasterApiController;
use App\Http\Controllers\APIs\SubTaskApiController;
use App\Http\Controllers\APIs\SubTaskDocumentApiController;
use App\Http\Controllers\APIs\TaskDocumentApiController;
use App\Http\Controllers\APIs\TeamApiController;
use App\Http\Controllers\APIs\ManageGroupsApiController;
use App\Http\Controllers\APIs\ManageCommunitiesApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['BasicAuthApi'])->group(function () {
    Route::prefix('/v1')->group(function(){
        //===================( Start Registration & Login API'S For Regroup )===================//

        //registration API
        Route::post('/send_otp', [AuthApiController::class, 'sendOtp']);
        Route::post('/verify_otp', [AuthApiController::class, 'verifykOtp']);
        Route::post('/login', [AuthApiController::class, 'login']);
        //===================( End Registration & Login API'S For Regroup )===================//

        //Activity API's

        Route::get('/get_activity',[ManageActivitiesApiController::class,'getActivity']);
            

        //===================( Registration & Login API'S )===================//
        Route::post('/registration_form', [AuthApiController::class, 'registrationForm']);

        

        
        Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
        Route::post('/forgot-password/verify-otp', [AuthApiController::class, 'verifyOtpForgotPassword']);
        Route::post('/resend-otp', [AuthApiController::class, 'resendOtp']);
        Route::post('/google-login', [GoogleLoginApiController::class, 'googleLogin'])->name('google.login');
        Route::post('/apple-login', [AppleLoginApiController::class, 'appleLogin'])->name('apple.login');
        Route::post('/facebook-login', [FacebookLoginApiController::class, 'facebookLogin'])->name('facebook.login');
    });
    Route::group(['middleware' => ['wdi.jwt.verify']], function () {
        Route::prefix('/v1')->group(function(){
            //Update Profile
            Route::post('/add_profile', [ProfileDetailsApiController::class, 'addProfile']);

            //Store Activities

            Route::post('/store_activities',[ManageActivitiesApiController::class,'storeActivities']);
            // ===================( Logout API'S )=========================//
            Route::get('/user-logout', [LogoutApiController::class, 'userLogout']);

            // ===================( Status Master API'S )=========================//
            Route::get('/status-master', [StatusMasterApiController::class, 'taskStatus']);
            Route::post('/add-status', [StatusMasterApiController::class, 'addStatus']);
            Route::post('/delete-status', [StatusMasterApiController::class, 'deleteStatus']);

            //=====================( Priority Master API'S )============================//
            Route::get('/priority-master', [PriorityMasterApiController::class, 'taskPriority']);
            Route::post('/add-priority', [PriorityMasterApiController::class, 'addPriority']);
            Route::post('/delete-priority', [PriorityMasterApiController::class, 'deletePriority']);

            //======================( Colors API'S  )==============================//
            Route::post('/add-colors', [ColorsApiController::class, 'addColors']);
            Route::get('/fetch-colors', [ColorsApiController::class, 'fetchColors']);

            // =====================( Product API'S )================================//
            Route::post('/add-product', [ManageProductApiController::class, 'addProduct']);
            Route::get('/fetch-product', [ManageProductApiController::class, 'fetchProduct']);
            Route::post('/edit-product', [ManageProductApiController::class, 'editProduct']);
            Route::post('/delete-product', [ManageProductApiController::class, 'deleteProduct']);

            //======================( Icons API'S  )==============================//
            Route::get('/fetch-icons', [IconApiController::class, 'fetchIcons']);
            Route::post('/add-icons', [IconApiController::class, 'addIcons']);

            //======================( Space API'S  )==============================//
            Route::post('/add-space', [SpaceApiController::class, 'addSpace']);
            Route::get('/fetch-space', [SpaceApiController::class, 'fetchSpace']);
            Route::post('/delete-space', [SpaceApiController::class, 'deleteSpace']);

            //======================( Space Folder API'S  )==============================//
            Route::post('/add-space-folder', [SpaceFolderApiController::class, 'addSpaceFolder']);
            Route::get('/fetch-space-folder', [SpaceFolderApiController::class, 'fetchSpaceFolder']);
            Route::post('/delete-space-folder', [SpaceFolderApiController::class, 'deleteSpaceFolder']);

            //======================( Space Folder List Link API'S  )==============================//
            Route::post('/add-space-folder-list', [SpaceFolderListLinkApiController::class, 'addSpaceFolderList']);
            Route::get('/fetch-space-folder-list', [SpaceFolderListLinkApiController::class, 'fetchSpaceFolderList']);
            Route::post('/delete-space-folder-list', [SpaceFolderListLinkApiController::class, 'deleteSpaceFolderList']);

            //======================( Space Folder List Task Link API'S  )==============================//
            Route::post('/add-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'addSpaceFolderListTask']);
            Route::get('/fetch-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'fetchSpaceFolderListTask']);
            Route::post('/delete-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'deleteSpaceFolderListTask']);
            Route::post('/listing-task-basedon-status', [SpaceFolderListTaskLinkApiController::class, 'listingTaskBasedonStatus']);

            //======================( Sub Task API'S  )==============================//
            Route::post('/add-sub-task', [SubTaskApiController::class, 'addSubTask']);
            Route::get('/fetch-sub-task', [SubTaskApiController::class, 'fetchSubTask']);
            Route::post('/delete-sub-task', [SubTaskApiController::class, 'deleteSubTask']);

            //======================( Task Document API'S  )==============================//
            Route::post('/add-task-document', [TaskDocumentApiController::class, 'addTaskDocument']);
            Route::get('/fetch-task-document', [TaskDocumentApiController::class, 'fetchTaskDocument']);
            Route::post('/delete-task-document', [TaskDocumentApiController::class, 'deleteTaskDocument']);

            //======================( Sub Task Document API'S  )==============================//
            Route::post('/add-subtask-document', [SubTaskDocumentApiController::class, 'addSubTaskDocument']);
            Route::get('/fetch-subtask-document', [SubTaskDocumentApiController::class, 'fetchSubTaskDocument']);
            Route::post('/delete-subtask-document', [SubTaskDocumentApiController::class, 'deleteSubTaskDocument']);

            //======================( Manage Activities API'S  )==============================//
            Route::post('/add-activities', [ManageActivitiesApiController::class, 'addManageActivities']);
            Route::get('/fetch-activities', [ManageActivitiesApiController::class, 'fetchManageActivities']);

            //======================( Team API'S  )==============================//
            Route::post('/add-team', [TeamApiController::class, 'addTeam']);
            Route::get('/fetch-team', [TeamApiController::class, 'fetchTeam']);
            Route::post('/delete-team', [TeamApiController::class, 'deleteTeam']);

            //======================( Profile Details API'S  )==============================//
            // Route::post('/add-profile', [ProfileDetailsApiController::class, 'addProfile']);
            Route::get('/fetch-role', [ProfileDetailsApiController::class, 'fetchRole']);
            Route::post('/update-profile', [ProfileDetailsApiController::class, 'updateProfile']);
            Route::post('/delete-profile', [ProfileDetailsApiController::class, 'deleteProfile']);

            //======================( Send Notifications API'S  )==============================//
            Route::post('/send-notification', [NotificationApiController::class, 'sendNotification']);
            Route::get('/listing-notification', [NotificationApiController::class, 'listingNotification']);

            //========================( Manage Interest API'S)=======================================//
            Route::get('/fetch-interests',[ManageInterestApiController::class,'fetchManageInterests']);
            Route::post('/select-interests',[ManageInterestApiController::class,'storeSelectedInterests']);

            //========================( Manage Groups API'S)=======================================//
            Route::get('/fetch-groups',[ManageGroupsApiController::class,'fetchManageGroup']);
            Route::post('/select-groups',[ManageGroupsApiController::class,'storeSelectedGroup']);


            //========================( Manage Communities API'S)=======================================//
            Route::get('/fetch-communities',[ManageCommunitiesApiController::class,'fetchManageCommunities']);
            Route::post('/select-communities',[ManageCommunitiesApiController::class,'storeSelectedCommunity']);

            // ================================send mail============================//
            Route::get('/send-mail',[ManageCommunitiesApiController::class,'sendMail']);
        });
    });
});
