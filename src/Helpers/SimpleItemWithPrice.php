<?php

namespace RecursiveTree\Seat\AllianceIndustry\Helpers;

class SimpleItemWithPrice extends \RecursiveTree\Seat\TreeLib\Helpers\SimpleItem
{
    private $price;

    /**
     * @param $price
     */
    public function __construct($type_id, $amount, $price)
    {
        parent::__construct($type_id, $amount);
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->price;
    }

    public function getTotalPrice(){
        return $this->price * $this->getAmount();
    }
}