<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\SubTaskDocument;
use App\Services\APIs\SubTaskDocumentApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubTaskDocumentApiController extends Controller
{
    protected $SubTaskDocumentApiService;
    public function __construct(SubTaskDocumentApiService $SubTaskDocumentApiService)
    {
        $this->SubTaskDocumentApiService = $SubTaskDocumentApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To add sub task document
     */
    public function addSubTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SubTaskDocumentApiService->addSubTaskDocumentService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add sub task document function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To show sub task document listing 
     */
    public function fetchSubTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SubTaskDocumentApiService->fetchSubTaskDocumentService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch sub task document function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 04 April 2024
     * Use : To delete sub task document 
     */
    public function deleteSubTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SubTaskDocumentApiService->deleteSubTaskDocumentService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete sub task document function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
