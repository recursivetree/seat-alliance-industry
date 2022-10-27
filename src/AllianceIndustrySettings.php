<?php

namespace RecursiveTree\Seat\AllianceIndustry;

use RecursiveTree\Seat\TreeLib\Helpers\Setting;

class AllianceIndustrySettings
{
    public static $LAST_NOTIFICATION_BATCH;

    public static function init(){
        self::$LAST_NOTIFICATION_BATCH = Setting::create("allianceindustry","notifications.batch.last",true);
    }
}