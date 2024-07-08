<?php

namespace App\Services\APIs;

use App\Models\ManageCommunity;
use App\Models\IamPrincipalManageCommunityLink;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ManageCommunitiesApiService
{
    /*
        * Created By : Ritikesh yadav
        * Created At : 05 july 2024
        * Use : Store user selected groups 
    */
    public function fetchManageCommunities()
    {
        try{
            $data = ManageCommunity::select('id','community_profile_photo','community_banner_image','community_name','community_location','community_description','community_type_xid','activity_xid')
            ->where('is_active',1)
            ->get();

            if($data == null)
            {
                log::info('manage communities data not found');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        }catch(Exception $e)
        {
            Log::error('fetch manage community service function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

    /*
        * Created By : Ritikesh yadav
        * Created At : 05 july 2024
        * Use : Store user selected groups 
    */
    public function StoreUserSelectedCommunity($request, $iamprincipal_id)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request,[
                'manage_community_xid' => 'required|exists:manage_communities,id',
            ]);

            if($validator->fails())
            {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);   
            }
            $storeUserSelectedCommunity = IamPrincipalManageCommunityLink::create([
                'iam_principal_xid'=>(int)$iamprincipal_id,
                'manage_community_xid'=>$request['manage_community_xid'],
                'joined_at' => Carbon::now(),
            ]);
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $storeUserSelectedCommunity, 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('store user select community function failed: '. $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}