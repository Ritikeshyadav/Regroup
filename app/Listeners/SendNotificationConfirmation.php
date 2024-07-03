<?php

namespace App\Listeners;

use App\Events\SendNotification;
use App\Models\NotificationMaster;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SendNotificationConfirmation
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Created By: Chandan Yadav
     * Created at : 09 April 2024
     * Use: To create entry of in-app-notification in table when event is called
     * Handle the event.
     */
    public function handle(SendNotification $event): void
    {
        DB::beginTransaction();
        $data = $event->data;

        //create in app notification in table
        $notification = NotificationMaster::create($data);
        DB::commit();
    }
}
