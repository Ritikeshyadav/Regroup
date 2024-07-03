<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\PriorityMaster;
use App\Services\APIs\PriorityMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class PriorityMasterApiController extends Controller
{
    protected $PriorityMasterService;
    public function __construct(PriorityMasterService $PriorityMasterService)
    {
        $this->PriorityMasterService = $PriorityMasterService;
    }
    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show task status list
     */
    public function taskPriority()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->PriorityMasterService->taskPriority();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('Task Priority function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add Priority  list
     */
    public function addPriority(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->PriorityMasterService->addPriority($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('Add Priority function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To delete Priority 
     */
    public function deletePriority(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->PriorityMasterService->deletePriority($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('Delete Priority function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
