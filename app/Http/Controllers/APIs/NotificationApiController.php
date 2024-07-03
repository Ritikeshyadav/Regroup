<?php

namespace App\Http\Controllers\APIs;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Onesignalhelper;
use App\Models\NotificationMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationApiController extends Controller
{

    /**
     * Created By : Chandan Yadav
     * Created At : 10 April 2024
     * Use : To send notifications
     */
    public function sendNotification(Request $request)
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                $playerId = "";
                $message = $request->description;
                $title = $request->title;
                $content_type = $request->content_type;
                $imageUrl = $request->image ? $request->image : null;

                $result = Onesignalhelper::sendNotificationApi($playerId, $title, $message, $content_type, $imageUrl, $id = null);

                $data = NotificationMaster::create([
                    'iamprincipal_xid' => $iamprincipal_id,
                    'title' => $title,
                    'description' => $message,
                    'image' => $imageUrl,
                    'is_read' => 1,
                ]);
                return jsonResponseWithSuccessMessageApi(__('success.send_notificattions'), $data, 201);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $ex) {
            Log::error('send notifications function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 10 April 2024
     * Use : To show listing notifications
     */
    public function listingNotification()
    {
        try {
            $token = readHeaderToken();
            if ($token) {
                $iamprincipal_id = $token['sub'];
                $notification_data = NotificationMaster::select('id', 'title', 'description', 'image', 'is_read', 'created_at')->where('iamprincipal_xid', $iamprincipal_id)->orderBy('id', 'desc')->get();
                if ($notification_data->isEmpty()) {
                    return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 200);
                }
                $data = $notification_data->toArray();
                foreach ($data as $k => $val) {
                    $data[$k]['image'] = ListingImageUrl('in_app_notification', $val['image']);
                }
                $responseData['result'] = $data;
                $responseData['total_records'] = count($data);

                DB::beginTransaction();
                //update as read data
                $notification_data = NotificationMaster::select('id', 'title', 'description', 'image', 'is_read', 'created_at')->where('iamprincipal_xid', $iamprincipal_id)->update(['is_read' => 1]);
                DB::commit();
                return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 200);
            } else {
                return jsonResponseWithErrorMessageApi(__('auth.you_have_already_logged_in'), 409);
            }
        } catch (Exception $e) {
            DB::rollback();
            Log::error('In-App-Notification API controller function failed: ' . $e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
