<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\TaskDocumentApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskDocumentApiController extends Controller
{
    protected $TaskDocumentApiService;
    public function __construct(TaskDocumentApiService $TaskDocumentApiService)
    {
        $this->TaskDocumentApiService = $TaskDocumentApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To add task document
     */
    public function addTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->TaskDocumentApiService->addTaskDocumentService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add task document function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To show task document listing 
     */
    public function fetchTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->TaskDocumentApiService->fetchTaskDocumentService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch task document function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 03 April 2024
     * Use : To delete task document 
     */
    public function deleteTaskDocument(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->TaskDocumentApiService->deleteTaskDocumentService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete task document function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
