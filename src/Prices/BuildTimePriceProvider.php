<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\Models\IndustryJob;
use RecursiveTree\Seat\TreeLib\Prices\AbstractPriceProvider;
use RecursiveTree\Seat\TreeLib\Prices\Illuminate;
use Illuminate\Support\Facades\DB;

class BuildTimePriceProvider extends AbstractPriceProvider
{

    public static function getPrices($items, $settings)
    {
        return $items->map(function ($item){
            $job = DB::table("industryActivityProducts")
                ->select("time","industryActivity.activityID")
                ->where("productTypeID",$item->typeModel->typeID)
                ->join("industryActivity","industryActivityProducts.typeID","industryActivity.typeID")
                ->first();
            $time = $job->time ?? 0;
            $activity = $job->activityID ?? 0;

            $price = $time;

            if($item->price == null) {
                $item->price = $price;
            }
            $item->industryPrice = $price;
            return $item;
        });
    }
}