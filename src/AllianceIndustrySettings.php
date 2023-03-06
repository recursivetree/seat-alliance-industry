<?php

namespace RecursiveTree\Seat\AllianceIndustry;

use RecursiveTree\Seat\TreeLib\Helpers\Setting;

class AllianceIndustrySettings
{
    public static $LAST_NOTIFICATION_BATCH;
    public static $MINIMUM_PROFIT_PERCENTAGE;
    public static $MARKET_HUB;
    public static $PRICE_TYPE;
    public static $ORDER_CREATION_PING_ROLES;
    public static $ALLOW_PRICES_BELOW_AUTOMATIC;
    public static $DEFAULT_ORDER_LOCATION;
    public static $DEFAULT_PRICE_PROVIDER;
    public static $ALLOW_PRICE_PROVIDER_SELECTION;
    public static $MANUFACTURING_TIME_COST_MULTIPLIERS;
    public static $REACTION_TIME_COST_MULTIPLIERS;


    //used in an earlier iteration of the notification system, still used in migrations
    public static $NOTIFICATION_COMMAND_SCHEDULE_ID;

    public static function init(){
        self::$LAST_NOTIFICATION_BATCH = Setting::create("allianceindustry","notifications.batch.last",true);
        self::$DEFAULT_ORDER_LOCATION = Setting::create("allianceindustry","order.location.default",true);
        self::$DEFAULT_PRICE_PROVIDER = Setting::create("allianceindustry","order.price.provider.default",true);
        self::$ALLOW_PRICE_PROVIDER_SELECTION = Setting::create("allianceindustry","order.price.provider.change.allowed",true);
        self::$MANUFACTURING_TIME_COST_MULTIPLIERS = Setting::create("allianceindustry","price.provider.modifiers.manufacturing",true);
        self::$REACTION_TIME_COST_MULTIPLIERS = Setting::create("allianceindustry","price.provider.modifiers.reactions",true);

        //with manual key because it is migrated from the old settings system
        self::$MINIMUM_PROFIT_PERCENTAGE = Setting::createFromKey("recursivetree.allianceindustry.minimumProfitPercentage",true);
        self::$MARKET_HUB = Setting::createFromKey("recursivetree.allianceindustry.marketHub",true);
        self::$PRICE_TYPE = Setting::createFromKey("recursivetree.allianceindustry.priceType",true);
        self::$ORDER_CREATION_PING_ROLES = Setting::createFromKey("recursivetree.allianceindustry.orderCreationPingRoles",true);
        self::$ALLOW_PRICES_BELOW_AUTOMATIC = Setting::createFromKey("recursivetree.allianceindustry.allowPricesBelowAutomatic",true);
        self::$NOTIFICATION_COMMAND_SCHEDULE_ID = Setting::createFromKey("recursivetree.allianceindustry.notifications_schedule_id",true);
    }
}