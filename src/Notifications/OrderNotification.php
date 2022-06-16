<?php

namespace RecursiveTree\Seat\AllianceIndustry\Notifications;

use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use Seat\Notifications\Notifications\AbstractNotification;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNotification extends AbstractNotification implements ShouldQueue
{
    use SerializesModels;

    private $order;

    public function __construct($order){
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail','slack'];
    }

    public function toMail()
    {
        $order = $this->order;
        $itemName = $order->type->typeName;

        $message = (new MailMessage)
            ->success()
            ->subject("EVE: New industry orders up for grabs")
            ->greeting("Hello Industrialist")
            ->line("A order to produce $order->quantity $itemName(s) has been put up.");

        $message->salutation("Regards, the seat-alliance-industry plugin")
            ->action("View on SeAT", route("allianceindustry.orderDetails",$order->id));

        return $message;
    }

    public function toSlack(){
        $order = $this->order;
        $itemName = $order->type->typeName;

        $pings = implode(" ", array_map(function ($role){
            return "<@&$role>";
        }, SettingHelper::getSetting("orderCreationPingRoles",[])));

        return (new SlackMessage)
            ->success()
            ->from('SeAT Alliance Industry Marketplace')
            ->content("$pings A order to produce $order->quantity $itemName(s) has been put up.")
            ->attachment(function ($attachment) use ($order) {
                $attachment
                    ->title("View on SeAT",  route("allianceindustry.orderDetails", $order->id));
            });

    }
}