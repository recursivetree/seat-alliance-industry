<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\TreeLib\Prices\PriceProviderSettings;

class AllianceIndustryPriceSettings implements PriceProviderSettings
{

    public function getPreferredMarketHub()
    {
        return AllianceIndustrySettings::$MARKET_HUB->get();
    }

    public function getPreferredPriceType()
    {
        return AllianceIndustrySettings::$PRICE_TYPE->get();
    }
}