<?php

namespace RecursiveTree\Seat\AllianceIndustry\Models;

use Illuminate\Database\Eloquent\Model;
use RecursiveTree\Seat\TreeLib\Items\EveItem;
use RecursiveTree\Seat\TreeLib\Items\ToEveItem;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Universe\UniverseStation;
use Seat\Eveapi\Models\Universe\UniverseStructure;
use Seat\Web\Models\User;


class OrderItem extends Model implements ToEveItem
{
    public $timestamps = false;

    protected $table = 'seat_alliance_industry_order_items';

    public function type(){
        return $this->hasOne(InvType::class, 'typeID', 'type_id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function toEveItem()
    {
        $item = new EveItem($this->type);
        $item->amount = $this->quantity;
        return $item;
    }

    public static function formatOrderItemsList($order){
        $items = $order->items;
        if($items->count()>1) {
            $item_text = $items
                ->take(3)
                ->map(function ($item) {
                    $name = $item->type->typeName;
                    return "$item->quantity $name";
                })->implode(", ");
            $count = $items->count();
            if ($count > 3) {
                $count -= 3;
                $item_text .= ", +$count other";
            }
            return $item_text;
        } else if($items->count()==1) {
            $item = $items->first();
            $name = $item->type->typeName;
            return "$item->quantity $name";
        } else {
            return "invalid order";
        }
    }
}