<?php

namespace RecursiveTree\Seat\AllianceIndustry\Api;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
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

        return view("allianceindustry::createOrder",compact("stations", "structures","mpp","location_id","multibuy"));
    }
}