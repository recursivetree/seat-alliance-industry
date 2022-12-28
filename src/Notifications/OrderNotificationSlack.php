<?php

namespace RecursiveTree\Seat\AllianceIndustry\Notifications;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use Seat\Notifications\Notifications\AbstractNotification;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNotificationSlack extends AbstractNotification implements ShouldQueue
{
    use SerializesModels;

    private $orders;

    public function __construct($orders){
        $this->orders = $orders;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack(){
        $orders = $this->orders;

        $pings = implode(" ", array_map(function ($role){
            return "<@&$role>";
        }, AllianceIndustrySettings::$ORDER_CREATION_PING_ROLES->get([])));

        return (new SlackMessage)
            ->success()
            ->from("SeAT Alliance Industry Marketplace")
            ->content("$pings New orders have been put up.")
            ->attachment(function ($attachment) use ($orders) {
                $attachment
                    ->title("View on SeAT",  route("allianceindustry.orders"));
                foreach ($orders as $order){
                    $itemName = $order->type->typeName;
                    $location = $order->location()->name;
                    $quantity = number($order->quantity,0);
                    $price = number_metric($order->unit_price);
                    $totalPrice = number_metric($order->unit_price * $order->quantity);

                    $attachment->field(function ($field) use ($totalPrice, $price, $quantity, $location, $itemName) {
                        $field
                            ->long()
                            ->title("$quantity $itemName(s)")
                            ->content("$price ISK/unit |  $totalPrice ISK total  | $location");
                    });
                }
            });

    }
}