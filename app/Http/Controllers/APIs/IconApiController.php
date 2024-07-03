<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\IconApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IconApiController extends Controller
{
    protected $IconApiService;
    public function __construct(IconApiService $IconApiService)
    {
        $this->IconApiService = $IconApiService;
    }
    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To show icons  list
     */
    public function fetchIcons()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->IconApiService->fetchIconsService();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch icons function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To add icons  list
     */
    public function addIcons(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->IconApiService->addIconsService($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add icons function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
