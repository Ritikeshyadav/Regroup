<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\TeamApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeamApiController extends Controller
{
    protected $TeamApiService;
    public function __construct(TeamApiService $TeamApiService)
    {
        $this->TeamApiService = $TeamApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To add team service
     */
    public function addTeam(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->TeamApiService->addTeamService($request, $iamprincipal_id);
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
     * Created At : 05 April 2024
     * Use : To team data listing 
     */
    public function fetchTeam(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                return $this->TeamApiService->fetchTeamService($iamprincipal_id, $request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch team function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 05 April 2024
     * Use : To delete team data
     */
    public function deleteTeam(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->TeamApiService->deleteTeamService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete team document function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
