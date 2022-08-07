<?php

namespace RecursiveTree\Seat\AllianceIndustry\Api;

use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Universe\UniverseStation;
use Seat\Eveapi\Models\Universe\UniverseStructure;

class AllianceIndustryApi
{
    public static function create_orders($data){
        $location_id = $data["location"] ?? 60003760;

        $multibuy = "";
        foreach ($data["items"] as $item){
            $type = InvType::find($item["type_id"]);

            if($type === null) continue;

            $quantity = $item["quantity"] ?? $item["amount"];
            $multibuy .= "$type->typeName $quantity" . PHP_EOL;
        }

        $stations = UniverseStation::all();
        $structures = UniverseStructure::all();
        $mpp = SettingHelper::getSetting("minimumProfitPercentage",2.5);

        return view("allianceindustry::createOrder",compact("stations", "structures","mpp","location_id","multibuy"));
    }
}