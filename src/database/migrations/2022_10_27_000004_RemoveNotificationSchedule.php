
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Seat\Services\Models\Schedule;

class RemoveNotificationSchedule extends Migration
{
    public function up()
    {
        $id =  \RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings::$NOTIFICATION_COMMAND_SCHEDULE_ID->get();
        if($id){
            Schedule::destroy($id);
            \RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings::$NOTIFICATION_COMMAND_SCHEDULE_ID->set(null);
        }
    }

    public function down()
    {
        $id = \RecursiveTree\Seat\TreeLib\Helpers\ScheduleHelper::scheduleCommand("allianceindustry:notifications","0 * * * *");

        \RecursiveTree\Seat\AllianceIndustry\AllianceIndustrySettings::$NOTIFICATION_COMMAND_SCHEDULE_ID->set($id);
    }
}

