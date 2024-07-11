<?php

namespace App\Services\APIs;

use App\Models\ManageFaqs;
use App\Models\ManageContactUs;
use App\Models\ManagePrivacyPolicy;
use App\Models\BugReport;
use App\Models\IamPrincipal;
use App\Models\ManageTermsAndCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;



class ManageCMSApiService
{
    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To fetch FAQs data
     */
    public function fetchFAQsService($iam_principal_id)
    {
        try{
            $data = ManageFaqs::select('id','question','answer')
            ->where(['is_active'=>1,'iam_principal_type_xid'=>$this->getIamPrincipalType($iam_principal_id)])
            ->get();
            if($data == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),200);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),$data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch FAQs service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To store contact us data
     */
    public function storeContactUsService($request)
    {
        try{
            DB::beginTransaction();
            ManageContactUs::create($request->toArray());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),200) ;
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store contact us service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To store bug report data
     */
    public function storeBugReportService($request)
    {
        try{
            DB::beginTransaction();
            BugReport::create($request->toArray());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),200) ;
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store bug report service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To fetch privacy policy data
     */
    public function fetchPrivacyPolicyService($iam_principal_id)
    {
        try{
            $data = ManagePrivacyPolicy::select('id','content')
            ->where(['is_active'=>1,'iam_principal_type_xid'=>$this->getIamPrincipalType($iam_principal_id)])
            ->get();
            if($data == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),200);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),$data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch privacy policy service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To fetch terms and condition data
     */
    public function fetchTermsAndConditionService($iam_principal_id)
    {
        try{
            $data = ManageTermsAndCondition::select('id','content')
            ->where(['is_active'=>1,'iam_principal_type_xid'=>$this->getIamPrincipalType($iam_principal_id)])
            ->get();
            if($data == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),200);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),$data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch terms and condition service function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 11 July 2024
     * Use : To get iam_principal_type_id data
     */
    public function getIamPrincipalType($iam_principal_id)
    {
        try{
            $iamPrincipalType = IamPrincipal::select('principal_type_xid')
            ->where('id',$iam_principal_id)
            ->first();

            return $iamPrincipalType->principal_type_xid;
        }catch(Exception $e)
        {
            Log::error('Fetch iamPrincipal Type function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}