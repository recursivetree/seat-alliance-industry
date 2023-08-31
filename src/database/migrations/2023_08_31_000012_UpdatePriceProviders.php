<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('seat_alliance_industry_orders', function (Blueprint $table){
            $table->dropColumn('priceProvider');
        });
        Schema::table('seat_alliance_industry_orders', function (Blueprint $table){
            $table->bigInteger('priceProvider')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('seat_alliance_industry_orders', function (Blueprint $table){
            $table->dropColumn('priceProvider');
        });
        Schema::table('seat_alliance_industry_orders', function (Blueprint $table){
            $table->string("priceProvider")->nullable();
        });
    }
};

