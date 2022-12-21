<?php

namespace RecursiveTree\Seat\AllianceIndustry\Prices;

use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RecursiveTree\Seat\AllianceIndustry\Helpers\SimpleItemWithPrice;

class EvePraisalPriceProvider extends AbstractPriceProvider
{

    public static function getPrices($items)
    {
        $evepraisal_request = [];

        foreach ($items->iterate() as $item){
            $evepraisal_request[] = [
                "type_id"=>$item->getTypeId(),
                "quantity"=>1
            ];
        }

        //appraise on evepraisal
        try {
            $market = SettingHelper::getSetting("marketHub","jita");

            $client = new Client([
                'timeout'  => 5.0,
            ]);
            $response = $client->request('POST', "https://evepraisal.com/appraisal/structured.json",[
                'json' => [
                    'market_name' => $market,
                    'persist' => 'false',
                    'items'=>$evepraisal_request,
                ]
            ]);
            //decode request
            $data = json_decode( $response->getBody());
        } catch (GuzzleException $e){
            throw new Exception("Failed to load prices from evepraisal!");
        }

        $priceType = self::getPreferredPriceType();

        return array_map(function ($item) use ($priceType) {
            if($priceType==="sell"){
                $price = $item->prices->sell->min;
            } else {
                $price = $item->prices->buy->max;
            }

            return new SimpleItemWithPrice(
                $item->typeID,
                $item->quantity,
                $price,
            );
        },$data->appraisal->items);
    }
}