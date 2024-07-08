<?php

namespace App\Services\APIs;

use App\Models\ManageInterest;
use App\Models\IamPrincipalManageInterestLink;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ManageInterestApiService
{
    /*
        * Created By : Ritikesh Yadav 
        * Created At : 05 July 2024
        * Use : To get all maganage interest data 
    */

    public function fetchInterestService()
    {
        try{
            $data = ManageInterest::select('id','name','image')
            ->where('is_active',1)
            ->get();

            if($data == null)
            {
                Log::info('manage interest data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            $responseData['result'] = $data;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData['result'], 200);
        }catch(Exception $e)
        {
            Log::error('manage interest service function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
        * Created By : Ritikesh Yadav 
        * Created At : 05 July 2024
        * Use : Store users selected interest 
    */
    public function StoreUserSelectedInterest($request, $iamprincipal_id)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request,[
                'manage_interest_xid' => 'required|exists:manage_interests,id',
            ]);

            if($validator->fails())
            {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);   
            }

            $storeUserSelectedInterest = IamPrincipalManageInterestLink::create(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_interest_xid'=>$request['manage_interest_xid']]);
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $storeUserSelectedInterest, 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('store user selected interest function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

}