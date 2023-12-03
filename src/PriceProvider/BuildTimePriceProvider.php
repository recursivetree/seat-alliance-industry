<?php

namespace RecursiveTree\Seat\AllianceIndustry\PriceProvider;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RecursiveTree\Seat\PricesCore\Contracts\IPriceProviderBackend;
use RecursiveTree\Seat\PricesCore\Exceptions\PriceProviderException;
use Seat\Services\Contracts\IPriceable;

class BuildTimePriceProvider implements IPriceProviderBackend
{

    /**
     * Fetches the prices for the items in $items
     * Implementations should store the computed price directly on the Priceable object using the setPrice method.
     * In case an error occurs, a PriceProviderException should be thrown, so that an error message can be shown to the user.
     *
     * @param Collection<IPriceable> $items The items to appraise
     * @param array $configuration The configuration of this price provider backend.
     * @throws PriceProviderException
     */
    public function getPrices(Collection $items, array $configuration): void
    {
        $config = [
            1  => $configuration['manufacturing'],
            11 => $configuration['reactions'],
        ];

        foreach ($items as $item){
            $job = DB::table("industryActivityProducts")
                ->select("industryActivity.activityID")
                ->selectRaw("time/quantity as time")
                ->where("productTypeID",$item->getTypeID())
                ->join("industryActivity","industryActivityProducts.typeID","industryActivity.typeID")
                ->first();

            $time = $job->time ?? 0;
            $modifier = $config[$job->activityID] ?? 0;

            $price = $time * $modifier;

            $item->setPrice($price);
        }
    }
}