<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScheduleExpiredDeliveriesCommand extends Migration
{
    public function up()
    {
        \RecursiveTree\Seat\TreeLib\Helpers\ScheduleHelper::scheduleCommand("allianceindustry:deliveries:expired","20 20 * * *");
    }

    public function down()
    {

    }
}

