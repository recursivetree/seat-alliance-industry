<?php

namespace RecursiveTree\Seat\AllianceIndustry\Observers;

class DeliveryObserver
{
    public static function saved($delivery){
        self::processOrder($delivery);
    }

    public static function deleted($delivery){
        self::processOrder($delivery);
    }

    private static function processOrder($delivery){
        $order = $delivery->order;

        //this is the case when the order observe triggers the deletion of related deliveries
        if(!$order) return;

        $is_completed = false;
        foreach ($order->deliveries as $delivery){
            if(!$delivery->completed){
                $is_completed = false;
                break;
            } else {
                $is_completed = true;
            }
        }

        if($order->assignedQuantity() >= $order->quantity && $is_completed){
            $order->completed = true;
            $order->completed_at = now();
        } else {
            $order->completed = false;
            $order->completed_at = null;
        }
        $order->save();
    }
}