<?php

return [
    'seat_alliance_industry_new_order_notification' => [
        'label' => 'allianceindustry::allianceindustry.seat_alliance_industry_new_order_notification',
        'handlers' => [
            'mail' => \RecursiveTree\Seat\AllianceIndustry\Notifications\OrderNotification::class,
            'slack' => \RecursiveTree\Seat\AllianceIndustry\Notifications\OrderNotification::class,
        ],
    ]
];