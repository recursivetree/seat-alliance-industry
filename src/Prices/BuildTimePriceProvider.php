<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings;
use RecursiveTree\Seat\AllianceIndustry\Models\IndustryJob;
use RecursiveTree\Seat\TreeLib\Prices\AbstractPriceProvider;
use RecursiveTree\Seat\TreeLib\Prices\Illuminate;
use Illuminate\Support\Facades\DB;

class BuildTimePriceProvider extends AbstractPriceProvider
{

    public static function getPrices($items, $settings)
    {
        $config = AllianceIndustrySettings::$MANUFACTURING_TIME_COST_MULTIPLIERS->get([]);

        return $items->map(function ($item) use ($config){
            $job = DB::table("industryActivityProducts")
                ->select("industryActivity.activityID")
                ->selectRaw("time/quantity as time")
                ->where("productTypeID",$item->typeModel->typeID)
                ->join("industryActivity","industryActivityProducts.typeID","industryActivity.typeID")
                ->first();
            $time = $job->time ?? 0;
            $modifier = $config[ $job->activityID] ?? 0;

            $price = $time * $modifier;

            if($item->price == null) {
                $item->price = $price;
            }
            $item->industryPrice = $price;
            return $item;
        });
    }
}