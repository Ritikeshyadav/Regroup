<?php

namespace App\Services\APIs;

use App\Models\IamPrincipalGroupLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ManageGroup;
use App\Models\IamPrincipalManageGroupLink;
use Exception;
use Carbon\Carbon;

class ManageGroupsApiService
{
    /*
     * Created By : Ritikesh Yadav
     * Created At : 05 July 2024
     * Use : fetch all group data 
     */
    public function fetchGroupService($request)
    {
        try {

            if ($request->get('search_data')) {
                $query = $request->get('search_data');
                if (strlen($query) >= 2) { // Ensure the query length is at least 2

                    $data = ManageGroup::select('id', 'title', 'background_image', 'group_image', 'location', 'link', 'description')->where('is_active', 1)
                        ->where(function ($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%");
                        })
                        ->get();
                } else {
                    $data = ManageGroup::select('id', 'title', 'background_image', 'group_image', 'location', 'link', 'description')
                    ->where('is_active', 1)
                    ->get();
                }
            }else{
                $data = ManageGroup::select('id', 'title', 'background_image', 'group_image', 'location', 'link', 'description')
                ->where('is_active', 1)
                ->get();
            }
            foreach($data as $key =>$val){
              
                $data[$key]['background_image'] = ListingImageUrl('group_background_image', $val->background_image);
                $data[$key]['group_image'] = ListingImageUrl('group_image', $val->group_image);

            }

            if ($data == null) {
                log::info('manage group data not found');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('fetch manage group service function failed: ' . $e->getMessage());
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
        try {
            DB::beginTransaction();
            $validator = Validator::make($request, [
                'manage_group_xid.*' => 'required|exists:manage_groups,id',
            ]);
            $storeUserSelectedGroups=[];
            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors()->all(), 403);
            }
            foreach (json_decode($request['manage_group_xid']) as $group) {
                if (IamPrincipalManageGroupLink::where(['iam_principal_xid' => (int) $iamprincipal_id, 'manage_group_xid' => $group])->doesntExist()) {
                    $storeUserSelectedGroups = IamPrincipalManageGroupLink::create(['iam_principal_xid' => (int) $iamprincipal_id, 'manage_group_xid' => $group]);
                }
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $storeUserSelectedGroups, 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('store user select group service failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }




    public function searchGroupDataService($request)
    {
        try {
            $data = [];
            // dd($request->query('search_data'));
            if ($request->get('search_data')) {
                $query = $request->get('search_data');
                if (strlen($query) >= 2) { // Ensure the query length is at least 2

                    $data = ManageGroup::select('id', 'title', 'background_image', 'group_image', 'location', 'link', 'description')->where('is_active', 1)
                        ->where(function ($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%");
                        })
                        ->get();
                } else {
                    $data = ManageGroup::select('id', 'title', 'background_image', 'group_image', 'location', 'link', 'description')
                        ->where('is_active', 1)
                        ->get();
                }
            }

            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('Search group service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }

    }


 /*
     * Created By : Hritik
     * Created At : 23 july 2024
     * Use : Store Group  
     */

     public function createGroupApiService($request)
     {
         try {
 
             DB::beginTransaction();
 
               
             // dd($request->all());
             $iamprincipal_id = $request->iam_principal_xid;
 
             if ($request->hasFile('background_image')) {
                 $image = $request->file('background_image');
                 $groupBackgroundImage = saveSingleImageWithoutCrop($image, 'group_background_image', null);
             } else {
                 $groupBackgroundImage = null;
             }
 
             if ($request->hasFile('group_image')) {
                 $image = $request->file('group_image');
                 $groupImage = saveSingleImageWithoutCrop($image, 'group_image', null);
             } else {
                 $groupImage = null;
             }
 
 
 
             $newGroup = new ManageGroup();

             
             $newGroup->manage_group_type_xid = $request->manage_group_type_xid;
 
             $newGroup->background_image = $groupBackgroundImage;
             $newGroup->group_image = $groupImage;
             $newGroup->title = $request->title;
             $newGroup->location = $request->location;
             $newGroup->link = $request->link;
             $newGroup->description = $request->description;

             $newGroup->created_by  =  $iamprincipal_id;
          
 
             $newGroup->save();
             Log::info("Group stored sucessfully");
 
             //join community =
             IamPrincipalManageGroupLink::create([
                 'iam_principal_xid' => $iamprincipal_id,
                 'manage_group_xid' => $newGroup->id,
                 'joined_at' => Carbon::now(),
                 'created_by' =>  $iamprincipal_id,
                 'is_admin' =>  1
             ]);
 
             Log::info("Group linked stored sucessfully");
 
             DB::commit();
             return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
         } catch (Exception $e) {
             DB::rollBack();
             Log::error('store user select community function failed: ' . $e->getMessage());
             return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
         }
 
     }

    
}