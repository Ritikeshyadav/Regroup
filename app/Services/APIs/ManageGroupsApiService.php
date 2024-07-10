<?php 

namespace App\Services\APIs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ManageGroup;
use App\Models\IamPrincipalManageGroupLink;
use Exception;

class ManageGroupsApiService
{
    /*
        * Created By : Ritikesh Yadav
        * Created At : 05 July 2024
        * Use : fetch all group data 
    */
    public function fetchGroupService()
    {
        try{
            $data = ManageGroup::select('id','title','background_image','group_image','location','link','description')
            ->where('is_active',1)
            ->get();

            if($data == null)
            {
                log::info('manage group data not found');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422); 
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        }catch(Exception $e)
        {
            Log::error('fetch manage group service function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
        * Created By : Ritikesh yadav
        * Created At : 05 july 2024
        * Use : Store user selected groups 
    */
    public function StoreUserSelectedGroup($request, $iamprincipal_id)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request,[
                'manage_group_xid.*' => 'required|exists:manage_groups,id',
            ]);

            if($validator->fails())
            {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);   
            }
            foreach(json_decode($request['manage_group_xid']) as $group)
            {
                if(IamPrincipalManageGroupLink::where(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_group_xid'=>$group])->doesntExist())
                {
                    $storeUserSelectedGroups = IamPrincipalManageGroupLink::create(['iam_principal_xid'=>(int)$iamprincipal_id,'manage_group_xid'=>$group]);
                }
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $storeUserSelectedGroups, 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('store user select group function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}