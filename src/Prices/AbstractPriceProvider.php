<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\TreeLib\Helpers\ItemList;

abstract class AbstractPriceProvider
{
    /**
     * @return AbstractPriceProvider the preferred price provider
     */
    public static function getDefaultPriceProvider(){
        return config('allianceindustry.config.priceProvider');
    }

    /**
    * @param ItemList $items
    */
    public static abstract function getPrices($items);

    /**
     * @return string
     * returns the system name of the preferred market
    */
    protected static function getPreferredMarketHub(){
        return AllianceIndustrySettings::$MARKET_HUB->get("jita");
    }

    /**
     * @return string either buy or sell
     */
    protected static function getPreferredPriceType(){
        return AllianceIndustrySettings::$PRICE_TYPE->get("buy");
    }
}