<?php

use App\Http\Controllers\APIs\AppleLoginApiController;
use App\Http\Controllers\APIs\AuthApiController;
use App\Http\Controllers\APIs\ColorsApiController;
use App\Http\Controllers\APIs\FacebookLoginApiController;
use App\Http\Controllers\APIs\GoogleLoginApiController;
use App\Http\Controllers\APIs\IconApiController;
use App\Http\Controllers\APIs\LogoutApiController;
use App\Http\Controllers\APIs\ManageActivitiesApiController;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['BasicAuthApi'])->group(function () {

    //===================( Start Registration & Login API'S For Regroup )===================//

        //registration API
        Route::post('/send_otp', [AuthApiController::class, 'sendOtp']);
        Route::post('/verify_otp', [AuthApiController::class, 'verifykOtp']);
    
    //===================( End Registration & Login API'S For Regroup )===================//


    //===================( Registration & Login API'S )===================//
    Route::post('/registration_form', [AuthApiController::class, 'registrationForm']);

    Route::post('/login', [AuthApiController::class, 'login']);

    
    Route::post('/v1/forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('/v1/forgot-password/verify-otp', [AuthApiController::class, 'verifyOtpForgotPassword']);
    Route::post('/v1/resend-otp', [AuthApiController::class, 'resendOtp']);
    Route::post('/v1/google-login', [GoogleLoginApiController::class, 'googleLogin'])->name('google.login');
    Route::post('/v1/apple-login', [AppleLoginApiController::class, 'appleLogin'])->name('apple.login');
    Route::post('/v1/facebook-login', [FacebookLoginApiController::class, 'facebookLogin'])->name('facebook.login');

    Route::group(['middleware' => ['wdi.jwt.verify']], function () {

        //Update Profile
        Route::post('/add_profile', [ProfileDetailsApiController::class, 'addProfile']);


        // ===================( Logout API'S )=========================//
        Route::get('/v1/user-logout', [LogoutApiController::class, 'userLogout']);

        // ===================( Status Master API'S )=========================//
        Route::get('/v1/status-master', [StatusMasterApiController::class, 'taskStatus']);
        Route::post('/v1/add-status', [StatusMasterApiController::class, 'addStatus']);
        Route::post('/v1/delete-status', [StatusMasterApiController::class, 'deleteStatus']);

        //=====================( Priority Master API'S )============================//
        Route::get('/v1/priority-master', [PriorityMasterApiController::class, 'taskPriority']);
        Route::post('/v1/add-priority', [PriorityMasterApiController::class, 'addPriority']);
        Route::post('/v1/delete-priority', [PriorityMasterApiController::class, 'deletePriority']);

        //======================( Colors API'S  )==============================//
        Route::post('/v1/add-colors', [ColorsApiController::class, 'addColors']);
        Route::get('/v1/fetch-colors', [ColorsApiController::class, 'fetchColors']);

        // =====================( Product API'S )================================//
        Route::post('/v1/add-product', [ManageProductApiController::class, 'addProduct']);
        Route::get('/v1/fetch-product', [ManageProductApiController::class, 'fetchProduct']);
        Route::post('/v1/edit-product', [ManageProductApiController::class, 'editProduct']);
        Route::post('/v1/delete-product', [ManageProductApiController::class, 'deleteProduct']);

        //======================( Icons API'S  )==============================//
        Route::get('/v1/fetch-icons', [IconApiController::class, 'fetchIcons']);
        Route::post('/v1/add-icons', [IconApiController::class, 'addIcons']);

        //======================( Space API'S  )==============================//
        Route::post('/v1/add-space', [SpaceApiController::class, 'addSpace']);
        Route::get('/v1/fetch-space', [SpaceApiController::class, 'fetchSpace']);
        Route::post('/v1/delete-space', [SpaceApiController::class, 'deleteSpace']);

        //======================( Space Folder API'S  )==============================//
        Route::post('/v1/add-space-folder', [SpaceFolderApiController::class, 'addSpaceFolder']);
        Route::get('/v1/fetch-space-folder', [SpaceFolderApiController::class, 'fetchSpaceFolder']);
        Route::post('/v1/delete-space-folder', [SpaceFolderApiController::class, 'deleteSpaceFolder']);

        //======================( Space Folder List Link API'S  )==============================//
        Route::post('/v1/add-space-folder-list', [SpaceFolderListLinkApiController::class, 'addSpaceFolderList']);
        Route::get('/v1/fetch-space-folder-list', [SpaceFolderListLinkApiController::class, 'fetchSpaceFolderList']);
        Route::post('/v1/delete-space-folder-list', [SpaceFolderListLinkApiController::class, 'deleteSpaceFolderList']);

        //======================( Space Folder List Task Link API'S  )==============================//
        Route::post('/v1/add-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'addSpaceFolderListTask']);
        Route::get('/v1/fetch-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'fetchSpaceFolderListTask']);
        Route::post('/v1/delete-space-folder-list-task', [SpaceFolderListTaskLinkApiController::class, 'deleteSpaceFolderListTask']);
        Route::post('/v1/listing-task-basedon-status', [SpaceFolderListTaskLinkApiController::class, 'listingTaskBasedonStatus']);

        //======================( Sub Task API'S  )==============================//
        Route::post('/v1/add-sub-task', [SubTaskApiController::class, 'addSubTask']);
        Route::get('/v1/fetch-sub-task', [SubTaskApiController::class, 'fetchSubTask']);
        Route::post('/v1/delete-sub-task', [SubTaskApiController::class, 'deleteSubTask']);

        //======================( Task Document API'S  )==============================//
        Route::post('/v1/add-task-document', [TaskDocumentApiController::class, 'addTaskDocument']);
        Route::get('/v1/fetch-task-document', [TaskDocumentApiController::class, 'fetchTaskDocument']);
        Route::post('/v1/delete-task-document', [TaskDocumentApiController::class, 'deleteTaskDocument']);

        //======================( Sub Task Document API'S  )==============================//
        Route::post('/v1/add-subtask-document', [SubTaskDocumentApiController::class, 'addSubTaskDocument']);
        Route::get('/v1/fetch-subtask-document', [SubTaskDocumentApiController::class, 'fetchSubTaskDocument']);
        Route::post('/v1/delete-subtask-document', [SubTaskDocumentApiController::class, 'deleteSubTaskDocument']);

        //======================( Manage Activities API'S  )==============================//
        Route::post('/v1/add-activities', [ManageActivitiesApiController::class, 'addManageActivities']);
        Route::get('/v1/fetch-activities', [ManageActivitiesApiController::class, 'fetchManageActivities']);

        //======================( Team API'S  )==============================//
        Route::post('/v1/add-team', [TeamApiController::class, 'addTeam']);
        Route::get('/v1/fetch-team', [TeamApiController::class, 'fetchTeam']);
        Route::post('/v1/delete-team', [TeamApiController::class, 'deleteTeam']);

        //======================( Profile Details API'S  )==============================//
        // Route::post('/v1/add-profile', [ProfileDetailsApiController::class, 'addProfile']);
        Route::get('/v1/fetch-role', [ProfileDetailsApiController::class, 'fetchRole']);
        Route::post('/v1/update-profile', [ProfileDetailsApiController::class, 'updateProfile']);
        Route::post('/v1/delete-profile', [ProfileDetailsApiController::class, 'deleteProfile']);

        //======================( Send Notifications API'S  )==============================//
        Route::post('/v1/send-notification', [NotificationApiController::class, 'sendNotification']);
        Route::get('/v1/listing-notification', [NotificationApiController::class, 'listingNotification']);
    });
});
