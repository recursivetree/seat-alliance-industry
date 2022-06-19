<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use RecursiveTree\Seat\AllianceIndustry\Helpers\SettingHelper;
use Seat\Services\Models\Schedule;

class ScheduleOrderNotifications extends Migration
{
    public function up()
    {
        $schedule = new Schedule();
        $schedule->command = "allianceindustry:notifications";
        $schedule->expression = "0 * * * *";
        $schedule->allow_overlap = false;
        $schedule->allow_maintenance = false;
        $schedule->save();

        SettingHelper::setSetting("notifications_schedule_id",$schedule->id);
    }

    public function down()
    {
        $id =  SettingHelper::getSetting("notifications_schedule_id",null);
        if($id){
            Schedule::destroy($id);
        }
    }
}

