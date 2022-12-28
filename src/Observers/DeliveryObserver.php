<?php

namespace RecursiveTree\Seat\AllianceIndustry\Observers;

use RecursiveTree\Seat\TreeLib\Helpers\SeatInventoryPluginHelper;

class DeliveryObserver
{
    public static function saved($delivery)
    {
        self::updateOrderCompletionState($delivery);
    }

    public static function saving($delivery){
        //delivery is completed, remove the virtual source
        if ($delivery->completed) {
            self::deleteInventorySource($delivery);
        }
        //create/update the delivery
        else {
            $order = $delivery->order;
            $source = $delivery->seatInventorySource;
            if ($order->add_seat_inventory && $source === null) {
                $workspace = SeatInventoryPluginHelper::$WORKSPACE_MODEL::where("name","like","%add2allianceindustry%")->first();

                if(!$workspace) return;

                $user_name = $delivery->user->name;
                $source = new SeatInventoryPluginHelper::$INVENTORY_SOURCE_MODEL();
                $source->location_id = SeatInventoryPluginHelper::$LOCATION_MODEL::where("structure_id", $order->location_id)->orWhere("station_id", $order->location_id)->first()->id;
                $source->source_name = "AllianceIndustry Delivery from $user_name";
                $source->source_type = "alliance_industry_delivery";
                $source->workspace_id = $workspace->id;
                $source->save();

                $item = new SeatInventoryPluginHelper::$INVENTORY_ITEM_MODEL();
                $item->source_id = $source->id;
                $item->type_id = $order->type_id;
                $item->amount = $delivery->quantity;
                $item->save();

                $delivery->seat_inventory_source = $source->id;
            }
        }
    }

    public static function deleted($delivery)
    {
        self::updateOrderCompletionState($delivery);
        self::deleteInventorySource($delivery);
    }

    private static function deleteInventorySource($delivery)
    {
        if ($delivery->seatInventorySource) {
            foreach ($delivery->seatInventorySource->items as $item) {
                $item->delete();
            }
            $delivery->seatInventorySource->delete();
            $delivery->seat_inventory_source = null;
        }
    }

    private static function updateOrderCompletionState($delivery)
    {
        $order = $delivery->order;

        //this is the case when the order observe triggers the deletion of related deliveries
        if (!$order) return;

        $is_completed = false;
        foreach ($order->deliveries as $delivery) {
            if (!$delivery->completed) {
                $is_completed = false;
                break;
            } else {
                $is_completed = true;
            }
        }

        if ($order->assignedQuantity() >= $order->quantity && $is_completed) {
            $order->completed = true;
            $order->completed_at = now();
        } else {
            $order->completed = false;
            $order->completed_at = null;
        }
        $order->save();
    }
}