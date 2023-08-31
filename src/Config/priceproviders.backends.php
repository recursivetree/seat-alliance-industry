<?php

use RecursiveTree\Seat\AllianceIndustry\PriceProvider\BuildTimePriceProvider;

return [
    'recursivetree/seat-alliance-industry/build-time' => [
        'backend'=> BuildTimePriceProvider::class,
        'label'=>'allianceindustry::allianceindustry.build_time_price_provider',
        'plugin'=>'recursivetree/seat-alliance-industry',
        'settings_route' => 'allianceindustry.priceprovider.buildtime.configuration',
    ]
];