<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\SubTaskApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubTaskApiController extends Controller
{
    protected $SubTaskApiService;
    public function __construct(SubTaskApiService $SubTaskApiService)
    {
        $this->SubTaskApiService = $SubTaskApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 02 April 2024
     * Use : To add sub task
     */
    public function addSubTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SubTaskApiService->addSubTaskService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add sub task function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 02 April 2024
     * Use : To show sub task listing 
     */
    public function fetchSubTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->SubTaskApiService->fetchSubTaskService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch sub task function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 02 April 2024
     * Use : To delete sub task 
     */
    public function deleteSubTask(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SubTaskApiService->deleteSubTaskService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Sub task function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
