<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\SpaceFolderApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpaceFolderApiController extends Controller
{

    protected $SpaceFolderApiService;
    public function __construct(SpaceFolderApiService $SpaceFolderApiService)
    {
        $this->SpaceFolderApiService = $SpaceFolderApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 26 March 2024
     * Use : To show work space folder list
     */
    public function fetchSpaceFolder()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderApiService->fetchSpaceFolderService();
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
     * Created At : 26 March 2024
     * Use : To add space folder
     */
    public function addSpaceFolder(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderApiService->addSpaceFolderService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add space folder function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 26 March 2024s
     * Use : To delete space folder
     */
    public function deleteSpaceFolder(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceFolderApiService->deleteSpaceFolderService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Space function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
