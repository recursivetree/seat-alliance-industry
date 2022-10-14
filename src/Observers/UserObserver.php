<?php

namespace RecursiveTree\Seat\AllianceIndustry\Observers;

use RecursiveTree\Seat\AllianceIndustry\Models\Delivery;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;

class UserObserver
{

    public static function deleted($user){
        $deliveries = Delivery::where("user_id", $user->id)->pluck("id");
        Delivery::destroy($deliveries);

        $orders = Order::where("user_id", $user->id)->pluck("id");
        Order::destroy($orders);
    }
}