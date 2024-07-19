<?php

namespace App\Services\APIs;

use App\Models\Abilities;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalBusinessUserLink;

use App\Models\ManageTimelines;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\APIs\ProfileDetailsApiService;


class TimeLineApiService
{


    public function createTimelineOfIndividual($request)
    {
        try {
            DB::beginTransaction();
            $iamprincipal_id = $request->iam_principal_xid;
            // dd($request->all(),$iamprincipal_id);



            $timelines = ManageTimelines::create(

                [
                    'iam_principal_xid' => $iamprincipal_id,
                    'club_name' => $request->club_name,
                    'role_name' => $request->role_name,
                    'team_name' => $request->team_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'abilities_xids' => $request->abilities_xids,


                ]
            );

            DB::commit();


            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $timelines, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' Timeline  service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


 
    public function getListOfAbilitiesService()
    {
        try {
            $data = Abilities::select('id', 'name')
                ->where('is_active', 1)
                ->get();
            
            if (empty($data) ) {
                Log::info('Ablilities not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('Fetch Abilities service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }




    
    public function updateTimelineOfIndividual($request)
    {
        try {
            DB::beginTransaction();
            $iamprincipal_id = $request->iam_principal_xid;
            $timeLineId = $request->timeline_id;
            // dd($request->all(),$iamprincipal_id);



            $timelines = ManageTimelines::where('id',$timeLineId)->update([
                    'iam_principal_xid' => $iamprincipal_id,
                    'club_name' => $request->club_name,
                    'role_name' => $request->role_name,
                    'team_name' => $request->team_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'abilities_xids' => $request->abilities_xids,


                ]
            );

            DB::commit();


            return jsonResponseWithSuccessMessageApi(__('success.update_data'),[], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' Timeline  service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

}




