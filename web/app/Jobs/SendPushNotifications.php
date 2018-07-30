<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Http\Controllers\NotificationController;

use App\Device;
use App\Notification;

use PushNotification;

class SendPushNotifications extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user_id, $msg, $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $message, $data = [])
    {
        \Log::info("NOTIFICATIONS JOB - CONSTRUCTOR");
        $this->user_id = $user_id;
        $this->msg = $message;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("NOTIFICATIONS JOB - HANDLE - START");
        NotificationController::push($this->user_id, $this->msg, $this->data);
        \Log::info("NOTIFICATIONS JOB - HANDLE - END");
    }
}
