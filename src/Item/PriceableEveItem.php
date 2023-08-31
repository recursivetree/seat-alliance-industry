<?php

namespace RecursiveTree\Seat\AllianceIndustry\Item;

use RecursiveTree\Seat\PricesCore\Contracts\IPriceable;
use RecursiveTree\Seat\TreeLib\Items\EveItem;

class PriceableEveItem extends EveItem implements IPriceable
{
    public function getTypeID(): int
    {
        return $this->typeModel->typeID;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}