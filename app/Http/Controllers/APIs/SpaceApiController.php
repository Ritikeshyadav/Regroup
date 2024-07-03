<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\SpaceApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpaceApiController extends Controller
{
    protected $SpaceApiService;
    public function __construct(SpaceApiService $SpaceApiService)
    {
        $this->SpaceApiService = $SpaceApiService;
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To show space  list
     */
    public function fetchSpace()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceApiService->fetchSpaceService();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch space function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To add space  list
     */
    public function addSpace(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceApiService->addSpaceService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add space function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To delete space 
     */
    public function deleteSpace(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->SpaceApiService->deleteSpaceService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Space function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
