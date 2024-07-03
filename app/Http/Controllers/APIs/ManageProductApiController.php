<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Services\APIs\ManageProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageProductApiController extends Controller
{
    protected $ManageProductService;
    public function __construct(ManageProductService $ManageProductService)
    {
        $this->ManageProductService = $ManageProductService;
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To add product 
     */
    public function addProduct(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ManageProductService->addProduct($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('Add Product function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To edit product 
     */
    public function editProduct(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ManageProductService->editProduct($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('edit Product function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To fetch product 
     */
    public function fetchProduct()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ManageProductService->fetchProduct();
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('fetch Product function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /**
     * Created By : Ravindra Gawade
     * Created At : 18 March 2024
     * Use : To fetch product 
     */
    public function deleteProduct(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                return $this->ManageProductService->deleteProduct($request);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            Log::error('delete Product function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
