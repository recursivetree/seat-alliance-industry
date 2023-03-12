<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MultiItemOrder extends Migration
{
    public function up()
    {
        Schema::create("seat_alliance_industry_order_items", function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("order_id")->unsigned()->index();
            $table->bigInteger("type_id")->unsigned();
            $table->integer('quantity')->unsigned();
        });

        //implement this without the model to avoid breaking it when the model changes
        $updates = DB::table("seat_alliance_industry_orders")
            ->select("*")
            ->get()
            ->map(function ($order) {
                return [
                    "order_id" => $order->id,
                    "type_id" => $order->type_id,
                    "quantity" => $order->quantity,
                ];
            })->toArray();
        DB::table("seat_alliance_industry_order_items")
            ->insert($updates);

        Schema::table("seat_alliance_industry_orders", function (Blueprint $table) {
            $table->dropColumn("type_id");
            $table->renameColumn("unit_price","price");
        });
    }

    public function down()
    {
        Schema::drop("seat_alliance_industry_order_items");
        Schema::table("seat_alliance_industry_orders", function (Blueprint $table) {
            $table->bigInteger('type_id');
            $table->renameColumn("price","unit_price");
        });
    }
}

