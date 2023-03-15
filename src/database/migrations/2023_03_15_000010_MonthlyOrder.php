<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MonthlyOrder extends Migration
{
    public function up()
    {
        Schema::table("seat_alliance_industry_orders", function (Blueprint $table) {
            $table->boolean("is_repeating")->default(false);
            $table->dateTime("repeat_date")->nullable();
            $table->smallInteger("repeat_interval")->unsignned()->nullable();
        });

        \RecursiveTree\Seat\TreeLib\Helpers\ScheduleHelper::scheduleCommand("allianceindustry:orders:repeating","1 34 * * *");
    }

    public function down()
    {
        Schema::table("seat_alliance_industry_orders", function (Blueprint $table) {
            $table->dropColumn("is_repeating");
            $table->dropColumn("repeat_date");
            $table->dropColumn("repeat_interval");
        });
    }
}

