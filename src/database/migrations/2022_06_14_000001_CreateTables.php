<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        Schema::create('recursive_tree_seat_alliance_industry_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('type_id');
            $table->integer('quantity');
            $table->bigInteger('user_id');
            $table->bigInteger('unit_price');
            $table->bigInteger('location_id');
            $table->dateTime('created_at',0);
            $table->dateTime('produce_until',0);
            $table->boolean('completed')->default(false);
            $table->dateTime('completed_at')->nullable();
        });

        Schema::create('recursive_tree_seat_alliance_industry_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id');
            $table->bigInteger('user_id');
            $table->integer('quantity');
            $table->boolean('completed')->default(false);
            $table->dateTime('accepted');
            $table->dateTime('completed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recursive_tree_seat_alliance_industry_orders');
        Schema::dropIfExists('recursive_tree_seat_alliance_industry_deliveries');
    }
}

