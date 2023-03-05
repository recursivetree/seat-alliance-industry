
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductionTime extends Migration
{
    public function up()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->string("priceProvider")->nullable();
        });
    }

    public function down()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->dropColumn("priceProvider");
        });
    }
}

