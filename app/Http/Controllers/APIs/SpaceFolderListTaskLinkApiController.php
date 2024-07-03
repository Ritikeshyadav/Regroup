<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\SpaceFolderListTaskLinkApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpaceFolderListTaskLinkApiController extends Controller
{
    protected $SpaceFolderListTaskLinkApiService;
    public function __construct(SpaceFolderListTaskLinkApiService $SpaceFolderListTaskLinkApiService)
    {
        $this->SpaceFolderListTaskLinkApiService = $SpaceFolderListTaskLinkApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 01 April 2024
     * Use : To add space folder list task
     */
    public function addSpaceFolderListTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SpaceFolderListTaskLinkApiService->addSpaceFolderListTaskService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add space folder list task link function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 12 April 2024
     * Use : To show work space folder list task link
     */
    public function fetchSpaceFolderListTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SpaceFolderListTaskLinkApiService->fetchSpaceFolderListTaskLinkService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch space folder list task link function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 01 April 2024
     * Use : To delete space folder list task link
     */
    public function deleteSpaceFolderListTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderListTaskLinkApiService->deleteSpaceFolderListTaskService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Space folder list link function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 11 April 2024
     * Use : To listing of task based on status.
     */
    public function listingTaskBasedonStatus(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderListTaskLinkApiService->listingTaskBasedonStatusService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Space folder list link function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
