<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\SpaceFolderListLinkApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpaceFolderListLinkApiController extends Controller
{
    protected $SpaceFolderListLinkApiService;
    public function __construct(SpaceFolderListLinkApiService $SpaceFolderListLinkApiService)
    {
        $this->SpaceFolderListLinkApiService = $SpaceFolderListLinkApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To add space folder
     */
    public function addSpaceFolderList(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderListLinkApiService->addSpaceFolderListService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add space folder list link function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To show work space folder list link
     */
    public function fetchSpaceFolderList()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderListLinkApiService->fetchSpaceFolderListLinkService();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 28 March 2024
     * Use : To delete space folder list link
     */
    public function deleteSpaceFolderList(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderListLinkApiService->deleteSpaceFolderListService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Space folder list link function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
