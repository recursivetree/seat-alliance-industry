<?php

namespace RecursiveTree\Seat\AllianceIndustry\Helpers;

class SettingHelper
{
    private const PLUGIN_SETTING_PREFIX = "recursivetree.allianceindustry";

    public static function getSetting($name, $default){
        $value = setting(self::getActualName($name),true);

        if($value === null){
            return $default;
        }

        return $value;
    }

    public static function setSetting($name, $value){
       setting([self::getActualName($name), $value], true);
    }

    private static function getActualName($name){
        return self::PLUGIN_SETTING_PREFIX.".".$name;
    }
}