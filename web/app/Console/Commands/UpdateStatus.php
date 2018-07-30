<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\NotificationController;

use App\Jobs\SendPushNotifications;

use App\Order;

class UpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update orders statuses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = new \DateTime();
        $now = $now->getTimestamp();

        $orders = Order::whereRaw('(status = 1 AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)) OR (status = 2 AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)) OR (status = 3 AND updated_at < DATE_SUB(NOW(), INTERVAL 3 HOUR)) OR (status = 5 AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY))')->get();

        foreach ($orders as $order) {

            $then = strtotime($order->updated_at);
            $diff = (int)(($now - $then) / 60);
            $changed = true;
            $data = ['order_id' => $order->id];

            // 1. ORDER

            // Proposal is not recieved in 60 minutes
            if ($order->status == 1 && $diff >= 60) {
                $order->status = 7;
                $order->save();
                $this->dispatch(new SendPushNotifications($order->customer->user_id, 'הזמנתך בוטלה עקב חוסר תגובה', $data));
                // NotificationController::push($order->customer->user_id, 'הזמנתך בוטלה עקב חוסר תגובה', $data);

            // Proposal is not accepted in 60 minutes
            } else if ($order->status == 2 && $diff >= 60) {
                $order->status = 7;
                $order->save();
                $this->dispatch(new SendPushNotifications($order->customer->user_id, 'הזמנתך בוטלה עקב חוסר תגובה', $data));
                // NotificationController::push($order->customer->user_id, 'הזמנתך בוטלה עקב חוסר תגובה', $data);
            
            // Order is not closed by shop in 180 minutes after proposal is accepted
            } else if ($order->status == 3 && $diff >= 180) {
                $order->status = 8;
                $order->save();

            // Order is not paid in 31 days
            } else if ($order->status == 5 && $diff >= 44640) {
                $order->status = 8;
                $order->save();
            
            // It seems that query returned more results
            } else {
                $changed = false;
            }

            // 2. PROPOSALS

            if ($changed) {
                foreach ($order->proposals as $proposal) {

                    if ($proposal->status == 1 || $proposal->status == 2) {
                        $proposal->status = 7;
                        $proposal->save();
                    } else if ($proposal->status == 3 || $proposal->status == 5) {
                        $proposal->status = 8;
                        $proposal->save();
                    } else {
                        // We don't have to change it
                    }

                }
            }

        }
    }
}
