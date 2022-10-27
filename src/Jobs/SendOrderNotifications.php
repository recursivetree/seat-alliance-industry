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
use Illuminate\Support\Facades\Notification;
use Seat\Notifications\Models\NotificationGroup;


class SendOrderNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


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
            self::dispatchNotification($orders);
        }

        AllianceIndustrySettings::$LAST_NOTIFICATION_BATCH->set($now);
    }

    //stolen from https://github.com/eveseat/notifications/blob/master/src/Observers/UserObserver.php
    private static function dispatchNotification($orders){
        $handlers = config('notifications.alerts.seat_alliance_industry_new_order_notification', [])["handlers"];

        $routes = self::getRoutingCandidates();

        // in case no routing candidates has been delivered, exit
        if ($routes->isEmpty())
            return;

        // attempt to enqueue a notification for each routing candidates
        $routes->each(function ($integration) use ($handlers, $orders) {
            if (array_key_exists($integration->channel, $handlers)) {

                // extract handler from the list
                $handler = $handlers[$integration->channel];

                // enqueue the notification
                Notification::route($integration->channel, $integration->route)->notify(new $handler($orders));
            }
        });
    }

    //stolen from https://github.com/eveseat/notifications/blob/master/src/Observers/UserObserver.php
    private static function getRoutingCandidates(){
        $settings = NotificationGroup::with('alerts')
            ->whereHas('alerts', function ($query) {
                $query->where('alert', 'seat_alliance_industry_new_order_notification');
            })->get();

        $routes = $settings->map(function ($group) {
            return $group->integrations->map(function ($channel) {

                // extract the route value from settings field
                $settings = (array) $channel->settings;
                $key = array_key_first($settings);
                $route = $settings[$key];

                // build a composite object built with channel and route
                return (object) [
                    'channel' => $channel->type,
                    'route' => $route,
                ];
            });
        });

        return $routes->flatten()->unique(function ($integration) {
            return $integration->channel . $integration->route;
        });
    }
}