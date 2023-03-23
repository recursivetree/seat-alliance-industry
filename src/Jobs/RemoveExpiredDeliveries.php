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
use RecursiveTree\Seat\AllianceIndustry\Models\Delivery;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;
use Illuminate\Support\Facades\Notification;
use Seat\Notifications\Models\NotificationGroup;


class RemoveExpiredDeliveries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function tags()
    {
        return ["seat-alliance-industry", "deliveries"];
    }

    public function handle()
    {
        if(!AllianceIndustrySettings::$REMOVE_EXPIRED_DELIVERIES->get(false)) return;

        $order_table = (new Order())->getTable();
        $deliveries_table = (new Delivery())->getTable();
        $deliveries = Delivery::query()
            ->join($order_table,"$deliveries_table.order_id","$order_table.id")
            ->select("$deliveries_table.id")
            ->where("$deliveries_table.completed",false)
            ->where("$order_table.produce_until","<",now())
            ->where("$order_table.is_repeating",false)
            ->get();

        //remove delivery while triggering observers
        foreach ($deliveries as $delivery){
            $delivery->delete();
        }
    }
}