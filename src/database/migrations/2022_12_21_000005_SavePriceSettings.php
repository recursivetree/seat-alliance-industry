
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use Seat\Services\Models\Schedule;

class SavePriceSettings extends Migration
{
    public function up()
    {
        $default_price = SettingHelper::getSetting("minimumProfitPercentage",2.5);

        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->float("profit");
        });

        DB::update(
            "update recursive_tree_seat_alliance_industry_orders set profit = ?",
            [$default_price]
        );
    }

    public function down()
    {
        Schema::table("recursive_tree_seat_alliance_industry_orders",function (Blueprint $table){
            $table->dropColumn("profit");
        });
    }
}

