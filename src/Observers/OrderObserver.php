<?php

namespace RecursiveTree\Seat\AllianceIndustry\Observers;

class OrderObserver
{

    public static function deleted($order){
        foreach ($order->deliveries as $delivery){
            $delivery->delete();
        }
    }
}