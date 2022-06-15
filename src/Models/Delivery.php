<?php

namespace RecursiveTree\Seat\AllianceIndustry\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Web\Models\User;

class Delivery extends Model
{
    public $timestamps = false;

    protected $table = 'recursive_tree_seat_alliance_industry_deliveries';

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}