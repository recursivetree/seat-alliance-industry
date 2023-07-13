<?php

namespace RecursiveTree\Seat\AllianceIndustry\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;
use Seat\Notifications\Models\NotificationGroup;
use Seat\Notifications\Traits\NotificationDispatchTool;


class SendOrderNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationDispatchTool;


    public function tags()
    {
        return ["seat-alliance-industry", "order","notifications"];
    }

    public function handle()
    {
        $now = now();
        $last_notifications = AllianceIndustrySettings::$LAST_NOTIFICATION_BATCH->get();

        if($last_notifications === null){
            $orders = Order::all();
        } else {
            $orders = Order::where("created_at",">=",$last_notifications)->get();
        }

        if(!$orders->isEmpty()) {
            $this->dispatchNotification($orders);
        }

        AllianceIndustrySettings::$LAST_NOTIFICATION_BATCH->set($now);
    }

    //stolen from https://github.com/eveseat/notifications/blob/master/src/Observers/UserObserver.php
    private function dispatchNotification($orders){
        $groups = NotificationGroup::with('alerts')
            ->whereHas('alerts', function ($query) {
                $query->where('alert', 'seat_alliance_industry_new_order_notification');
            })->get();

        $this->dispatchNotifications("seat_alliance_industry_new_order_notification",$groups,function ($constructor) use ($orders) {
            return new $constructor($orders);
        });
    }
}