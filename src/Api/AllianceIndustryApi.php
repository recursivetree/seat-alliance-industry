<?php

namespace RecursiveTree\Seat\AllianceIndustry\Api;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\TreeLib\Prices\PriceProvider;
use Seat\Eveapi\Models\Universe\UniverseStation;
use Seat\Eveapi\Models\Universe\UniverseStructure;

class AllianceIndustryApi
{
    public static function create_orders($data){
        $location_id = $data["location"] ?? 60003760;

        $multibuy = $data["items"]->toMultibuy();

        $stations = UniverseStation::all();
        $structures = UniverseStructure::all();
        $mpp = AllianceIndustrySettings::$MINIMUM_PROFIT_PERCENTAGE->get(2.5);

        $price_providers = config('treelib.priceproviders');
        $default_price_provider = $price_providers[AllianceIndustrySettings::$DEFAULT_PRICE_PROVIDER->get(PriceProvider::DEFAULT_PRICE_PROVIDER)] ?? $price_providers[PriceProvider::DEFAULT_PRICE_PROVIDER];

        $allowPriceProviderSelection = AllianceIndustrySettings::$ALLOW_PRICE_PROVIDER_SELECTION->get(false);

        return view("allianceindustry::createOrder",compact("stations","default_price_provider", "structures","mpp","location_id","multibuy","allowPriceProviderSelection"));
    }
}