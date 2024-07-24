<?php

namespace App\Services\APIs;

use App\Models\IamPrincipalManageCommunityLink;
use App\Models\IamPrincipalPinnedLink;
use App\Models\ManageTags;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ManagePinnedApiService
{
    /**
     * Created By : Ritikesh Yadav
     * Created At : 24 July 2024
     * Use : To Get Pinned Details
    */
    public function fetchPinnedDetailsService()
    {
        try{
            $pinned_data = IamPrincipalPinnedLink::with(['tag'=>function($q)
                {$q->select('id','name');},'community'=>function($q){
                    $q->select('id','community_profile_photo','community_banner_image','community_name');
                },'pin_user'=>function($q){
                   $q->select('id','user_name','full_name','profile_photo'); 
                }])
                ->where('iam_principal_xid',auth()->user()->id)
                ->select('id','iam_principal_xid','manage_tags_xid','manage_communities_xid','pin_iam_principal_xid')
                ->get();
            return jsonResponseWithSuccessMessageApi(__($pinned_data == null ? 'success.data_not_found' : 'success.data_fetched_successfully'), $pinned_data, 200);;
        }catch(Exception $e)
        {
            Log::error('Fetch pinned details service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 24 July 2024
     * Use : To Add or Remove From Pin
    */
    public function pinUnpinService($request)
    {
        try{
            $request['iam_principal_xid'] = auth()->user()->id;
            if(IamPrincipalPinnedLink::where($request->all())->doesntExist())
            {
                IamPrincipalPinnedLink::create($request->all());
            }else{
                IamPrincipalPinnedLink::where($request->all())->delete();
                return jsonResponseWithSuccessMessageApi(__('success.delete'),[],200);
            }
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),[],200);
        }catch(Exception $e)
        {
            Log::error('Pin or Unpin service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchDataForPinnedService()
    {
        try{
            $communities = IamPrincipalManageCommunityLink::with(['community'=>function($q){
                $q->select('id','community_profile_photo','community_banner_image','community_name');
            }])
            ->where('iam_principal_xid',auth()->user()->id)
            ->select('id','iam_principal_xid','manage_community_xid')
            ->get();

            if($communities == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),[],200);
            }
            $data = [];
            $communityCount = 0;
            $communities_xids = [];
            foreach($communities as $community)
            {
                $communities_xids[] = $community->manage_community_xid;
                $data['community'][$communityCount++] = ['id' => $community->manage_community_xid, 'community_name' => $community->community->community_name];
            }
            $data['tags'] = ManageTags::whereIn('manage_community_xid',$communities_xids)->get(['id','name']);
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch data for pinned service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}