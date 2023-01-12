
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderPriority extends Migration
{
    public function up()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->integer("priority")->unsigned();
        });

        $default_priority = 2;//same as seat-info

        DB::update(
            "update recursive_tree_seat_alliance_industry_orders set priority = ?",
            [$default_priority]
        );
    }

    public function down()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->dropColumn("priority");
        });
    }
}

