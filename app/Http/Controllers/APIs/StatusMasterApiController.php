<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\StatusMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class StatusMasterApiController extends Controller
{
    protected $StatusMasterService;
    public function __construct(StatusMasterService $StatusMasterService)
    {
        $this->StatusMasterService = $StatusMasterService;
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show task status list
     */
    public function taskStatus()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->StatusMasterService->taskStatus();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('Task Status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add status  list
     */
    public function addStatus(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->StatusMasterService->addStatus($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To delete status  list
     */
    public function deleteStatus(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->StatusMasterService->deleteStatus($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('delete status function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
