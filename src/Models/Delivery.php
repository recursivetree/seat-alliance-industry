<?php

namespace RecursiveTree\Seat\AllianceIndustry\Models;

use Illuminate\Database\Eloquent\Model;
use RecursiveTree\Seat\TreeLib\Helpers\SeatInventoryPluginHelper;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Web\Models\User;

class Delivery extends Model
{
    public $timestamps = false;

    protected $table = 'seat_alliance_industry_deliveries';

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function seatInventorySource(){
        return $this->hasOne(SeatInventoryPluginHelper::$INVENTORY_SOURCE_MODEL,'id','seat_inventory_source');
    }
}