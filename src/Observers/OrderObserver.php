<?php

namespace RecursiveTree\Seat\AllianceIndustry\Observers;

use Illuminate\Support\Facades\Notification;
use Seat\Notifications\Models\NotificationGroup;

class OrderObserver
{

    public static function deleted($order){
        foreach ($order->deliveries as $delivery){
            $delivery->delete();
        }
    }

    public static function created($order){
        //send notification
        self::dispatchNotification($order);
    }

    //stolen from https://github.com/eveseat/notifications/blob/master/src/Observers/UserObserver.php
    private static function dispatchNotification($order){
        $handlers = config('notifications.alerts.seat_alliance_industry_new_order_notification', [])["handlers"];

        $routes = self::getRoutingCandidates();

        // in case no routing candidates has been delivered, exit
        if ($routes->isEmpty())
            return;

        // attempt to enqueue a notification for each routing candidates
        $routes->each(function ($integration) use ($handlers, $order) {
            if (array_key_exists($integration->channel, $handlers)) {

                // extract handler from the list
                $handler = $handlers[$integration->channel];

                // enqueue the notification
                Notification::route($integration->channel, $integration->route)->notify(new $handler($order));
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