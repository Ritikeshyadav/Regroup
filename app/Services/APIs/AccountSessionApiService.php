<?php

namespace App\Services\APIs;

use App\Models\Abilities;
use App\Models\AccountSessions;
use App\Models\IamPrincipal;
use App\Models\IamPrincipalBusinessUserLink;

use App\Models\ManageTimelines;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\AccountSession;
use Throwable;
use Illuminate\Support\Facades\Http;

class AccountSessionApiService
{


    public function storeAccountSessionService($request)
    {
        try {
            DB::beginTransaction();
            $iamprincipal_id = $request->iam_principal_xid;

            $ipAddress = $request->ip();

            // Check if the IP address is local
            if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
                // Use a known external IP address for testing purposes
                $ipAddress = '122.179.140.110'; // Example external IP address
            }

            $response = Http::get("http://ip-api.com/json/{$ipAddress}");
            // $response = Http::get("http://ip-api.com/json/122.179.140.110");
            // dd($response,$response->json());
            if ($response->successful()) {
                $data = $response->json();
                // dd($data);

                if ($data['status'] === 'success') {
                    $country = $data['country'];
                    $region = $data['regionName'];
                    $city = $data['city'];
                    $zip = $data['zip'];
                    $isp = $data['isp'];
                    $lat = $data['lat'];
                    $lon = $data['lon'];
                    $timezone = $data['timezone'];

                    // Check if there is an existing record for the same day

                    // Update the existing record for the same day
                    $createorUpdateSession = AccountSessions::updateOrCreate([
                        'iam_principal_xid' => $iamprincipal_id,
                        'ip_address' => $ipAddress
                    ], [
                        'last_login_time' => now(),
                        'device_name' => $request->device_name,
                        'country' => $country,
                        'state' => $region,
                        'city' => $city,
                        'zip' => $zip,
                        'isp' => $isp,
                        'lat' => $lat,
                        'lon' => $lon,
                        'timezone' => $timezone,
                    ]);

                } else {
                    return response()->json(['message' => 'IP lookup failed'], 500);
                }
            } else {
                return response()->json(['message' => 'HTTP request failed'], 500);
            }



            DB::commit();


            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error(' Timeline  service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Ritikesh Yadav
     * Created At : 10 July 2024
     * Use : To fetch business profile service 
     */
    public function getListOfAbilitiesService()
    {
        try {
            $data = Abilities::select('id', 'name')
                ->where('is_active', 1)
                ->get();

            if (empty($data)) {
                Log::info('Ablilities not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 422);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $data, 200);
        } catch (Exception $e) {
            Log::error('Fetch Abilities service function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }

}




