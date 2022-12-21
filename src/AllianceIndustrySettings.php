<?php

namespace RecursiveTree\Seat\AllianceIndustry;

use RecursiveTree\Seat\TreeLib\Helpers\Setting;

class AllianceIndustrySettings
{
    public static $LAST_NOTIFICATION_BATCH;



    //used in an earlier iteration of the notification system, still used in migrations
    public static $NOTIFICATION_COMMAND_SCHEDULE_ID;

    public static function init(){
        self::$LAST_NOTIFICATION_BATCH = Setting::create("allianceindustry","notifications.batch.last",true);

        self::$NOTIFICATION_COMMAND_SCHEDULE_ID = Setting::createFromKey("recursivetree.allianceindustry.notifications_schedule_id",true);
    }
}