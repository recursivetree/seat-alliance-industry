<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixMonthlyOrder extends Migration
{
    public function up()
    {
        \RecursiveTree\Seat\TreeLib\Helpers\ScheduleHelper::removeCommand("allianceindustry:orders:repeating");
        \RecursiveTree\Seat\TreeLib\Helpers\ScheduleHelper::scheduleCommand("allianceindustry:orders:repeating","21 21 * * *");
    }

    public function down()
    {

    }
}

