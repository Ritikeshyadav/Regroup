<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\ManageActivitiesApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageActivitiesApiController extends Controller
{
    protected $ManageActivitiesApiService;
    public function __construct(ManageActivitiesApiService $ManageActivitiesApiService)
    {
        $this->ManageActivitiesApiService = $ManageActivitiesApiService;
    }

    /**
     * Created By : Vedant Chavan
     * Created At : 04 July 2024
     * Use : To get Activities Service
     */

    public function getActivity(){

        try {
            return $this->ManageActivitiesApiService->getActivitiesService();
        } catch (Exception $ex) {
            Log::error('add manage activities function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Vedant Chavan
     * Created At : 04 July 2024
     * Use : Store Activities to the user 
     */

    public function storeActivities(Request $request){

        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ManageActivitiesApiService->storeActivitiesService($request,$iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add manage activities function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To add manage activities
     */
    public function addManageActivities(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ManageActivitiesApiService->addActivitiesService($request, $iamprincipal_id);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add manage activities function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To show manage activities listing 
     */
    public function fetchManageActivities(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->ManageActivitiesApiService->fetchActivitiesService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch manage activities function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
