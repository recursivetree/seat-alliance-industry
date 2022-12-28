<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
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
        return SettingHelper::getSetting("marketHub","jita");
    }

    /**
     * @return string either buy or sell
     */
    protected static function getPreferredPriceType(){
        return SettingHelper::getSetting("priceType","buy");;
    }
}