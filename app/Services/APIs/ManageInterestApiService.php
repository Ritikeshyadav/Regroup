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
            $interestArray = json_decode($request['manage_interest_xid']);
            // if($request['other_interest'])
            // {
            //     if(ManageInterest::where('name','like',$request['other_interest'])->doesntExist())
            //     {
            //         $otherInterestId = ManageInterest::insertGetId(['name'=>$request['other_interest']]);
            //         array_push($interestArray,$otherInterestId);
            //     }else{
            //         return jsonResponseWithErrorMessageApi('This Activity Already Exist',403);
            //     }
            // }

            // foreach($interestArray as $interest)
            // {
            //     if(IamPrincipalManageInterestLink::where(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_interest_xid'=>$interest])->doesntExist())
            //     {
            //         $storeUserSelectedInterest = IamPrincipalManageInterestLink::create(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_interest_xid'=>$interest]);
            //     }else{
            //         continue;
            //     }
            // }
            $this->storeInterest($interestArray, $request);
            DB::commit();

            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('store user selected interest function failed: '. $e->getMessage() . $e->getLine());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
    
    /*
        * Created By : Ritikesh Yadav 
        * Created At : 05 July 2024
        * Use : Store users selected interest 
    */
    public function storeInterest($interestArray, $request = null)
    {
        try{
            $iamprincipal_id = auth()->user()->id;
            DB::beginTransaction();
            if($request['other_interest'])
            {
                if(ManageInterest::where('name','like',$request['other_interest'])->doesntExist())
                {
                    $otherInterestId = ManageInterest::insertGetId(['name'=>$request['other_interest']]);
                    array_push($interestArray,$otherInterestId);
                }else{
                    return jsonResponseWithErrorMessageApi('This Activity Already Exist',403);
                }
            }

            foreach($interestArray as $interest)
            {
                if(IamPrincipalManageInterestLink::where(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_interest_xid'=>$interest])->doesntExist())
                {
                    $storeUserSelectedInterest = IamPrincipalManageInterestLink::create(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_interest_xid'=>$interest]);
                }else{
                    continue;
                }
            }
            DB::commit();
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store Interest service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /*
        * Created By : Ritikesh Yadav 
        * Created At : 05 July 2024
        * Use : remove interest 
    */
    public function removeInterest($newArray)
    {
        try{
            DB::beginTransaction();
            $oldArray = IamPrincipalManageInterestLink::where('iam_principal_xid',auth()->user()->id)->pluck('manage_interest_xid');

            // for removing old data with id
            $removeArrayList = [];
            foreach($oldArray as $interestID)
            {
                if(!in_array($interestID,$newArray))
                {
                    array_push($removeArrayList,$interestID);
                }
            }

            // add new data with id
            $addArrayList = [];
            foreach($newArray as $interestID)
            {
                if(!in_array($interestID,$oldArray->toArray()))
                {
                    array_push($addArrayList,$interestID);
                }
            }
            if($removeArrayList != null)
            {
                foreach($removeArrayList as $interestID)
                {
                    IamPrincipalManageInterestLink::where(['iam_principal_xid'=>auth()->user()->id,'manage_interest_xid'=>$interestID])->delete();
                }
            }
            DB::commit();
            return $addArrayList;
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Remove Interest service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

}