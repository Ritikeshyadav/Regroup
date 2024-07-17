<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Abilities;
use App\Models\IamPrincipal;

use App\Services\APIs\TimeLineApiService;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TimeLineController extends Controller
{
    protected $timeLineApiService;
    public function __construct(TimeLineApiService $timeLineApiService)
    {
        $this->timeLineApiService = $timeLineApiService;
    }

    /**
     * Created By : Hritik Yadav
     * Created at : 17 July 2024
     * Use : To get list of Abilities
     */
    
    public function listOfAbilities(Request $request)
    {
        try{
            $data = Abilities::select('id', 'name')
            ->where('is_active', 1)
            ->get();
        
       
        return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        }catch(Exception $e)
        {
            Log::error('Fetch List of abilities function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Hritik Yadav
     * Created at : 17 July 2024
     * Use : To Create Timeline of my profile
     */

    public function createTimeline(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'club_name' => 'required',
                'role_name' => 'required',
                'team_name' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'abilities_xids' => 'required',
                // 'iam_principal_xid'=>'required|integer'
            ]);
            // if ($validator->fails()) {
            //     Log::error('create Timeline validation failed: ' . $validator->errors());
            //     return jsonResponseWithErrorMessageApi($validator->errors(), 400);
            // }

            
            if ($validator->fails()) {
                $validationErrors = $validator->errors()->all();
                Log::error("create Timeline validation failed " . implode(", ", $validationErrors));
                return jsonResponseWithErrorMessageApi($validationErrors, 403);
            }

            $iamprincipal_id = auth()->user()->id;
            $request['iam_principal_xid'] = $iamprincipal_id;
            return $this->timeLineApiService->createTimelineOfIndividual($request);
            // return $this->ProfileDetailsApiService->updateNotificationStatusService($request,auth()->user()->id);


        } catch (Exception $ex) {
            Log::error('add profile details function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }





}
