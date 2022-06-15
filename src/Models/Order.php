<?php

namespace RecursiveTree\Seat\AllianceIndustry\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Universe\UniverseStation;
use Seat\Eveapi\Models\Universe\UniverseStructure;
use Seat\Web\Models\User;


class Order extends Model
{
    public $timestamps = false;

    protected $table = 'recursive_tree_seat_alliance_industry_orders';

    public function deliveries(){
        return $this->hasMany(Delivery::class,"order_id","id");
    }

    public function type(){
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function station()
    {
        return $this->hasOne(UniverseStation::class, 'station_id', 'location_id');
    }

    public function structure()
    {
        return $this->hasOne(UniverseStructure::class, 'structure_id', 'location_id');
    }

    public function location(){
        return $this->station ?: $this->structure;
    }

    public function assignedQuantity(){
        return $this->deliveries->sum("quantity");
    }
}