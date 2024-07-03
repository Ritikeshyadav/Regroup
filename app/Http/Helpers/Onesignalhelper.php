<?php

namespace App\Http\Helpers;

use Ladumor\OneSignal\OneSignal;

class Onesignalhelper
{
    public static function sendNotificationApi($playerId, $title, $message, $content_type, $imageUrl, $id)
    {
        // Retrieve configuration based on the specified app
        // $config = config("one-signal.apps.$app");

        $fields = [
            'include_player_ids' => [$playerId],
            // 'included_segments' => ['All'],
            'contents' => ['en' => $message],
            'headings' => ['en' => $title],
            'isAndroid' => true,
            'content_available' => true,
            'data' => [
                'content_type' => $content_type,
                'id' => $id,
            ],
            'big_picture' => $imageUrl,
            // 'app_id' => env('ONE_SIGNAL_APP_ID'),
            // 'authorization' => env('ONE_SIGNAL_AUTHORIZE')
        ];

        $result = OneSignal::sendPush($fields);
    }
}
