<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeatInventoryIntegration extends Migration
{
    public function up()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->boolean("add_seat_inventory")->default(false);
        });

        Schema::table("recursive_tree_seat_alliance_industry_deliveries",function (Blueprint $table){
            $table->bigInteger("seat_inventory_source")->nullable();
        });
    }

    public function down()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->dropColumn("add_seat_inventory");
        });

        Schema::table("recursive_tree_seat_alliance_industry_deliveries",function (Blueprint $table){
            $table->dropColumn("seat_inventory_source");
        });
    }
}

