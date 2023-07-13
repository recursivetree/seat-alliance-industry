<?php

namespace RecursiveTree\Seat\AllianceIndustry\Notifications;

use Seat\Notifications\Notifications\AbstractMailNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderNotificationMail extends AbstractMailNotification implements ShouldQueue
{
    use SerializesModels;

    private $orders;

    public function __construct($orders){
        $this->orders = $orders;
    }

    public function populateMessage(MailMessage $message, $notifiable)
    {

        $message->success()
            ->subject("New Industry Orders")
            ->greeting("Hello Industrialist")
            ->line("New industry orders have been put up.")
            ->action("View on SeAT", route("allianceindustry.orders"));

        $message->salutation("Regards, the seat-alliance-industry plugin");

        return $message;
    }
}