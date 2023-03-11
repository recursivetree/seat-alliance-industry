
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTables extends Migration
{
    public function up()
    {
        Schema::rename("recursive_tree_seat_alliance_industry_orders","seat_alliance_industry_orders");
        Schema::rename("recursive_tree_seat_alliance_industry_deliveries","seat_alliance_industry_deliveries");
    }

    public function down()
    {
        Schema::rename("seat_alliance_industry_orders","recursive_tree_seat_alliance_industry_orders");
        Schema::rename("seat_alliance_industry_deliveries","recursive_tree_seat_alliance_industry_deliveries");
    }
}

