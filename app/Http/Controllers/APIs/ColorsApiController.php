<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\ColorsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Faker\Core\Color;
use Illuminate\Support\Facades\Validator;

class ColorsApiController extends Controller
{
    protected $ColorsService;
    public function __construct(ColorsService $ColorsService)
    {
        $this->ColorsService = $ColorsService;
    }
    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To show colors  list
     */
    public function fetchColors()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ColorsService->fetchColors();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('fetch colors function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ravindra Gawade
     * Created At : 15 March 2024
     * Use : To add colors  list
     */
    public function addColors(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ColorsService->addColors($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('add colors function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
