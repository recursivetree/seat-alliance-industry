<?php

namespace RecursiveTree\Seat\AllianceIndustry;

use Seat\Services\AbstractSeatPlugin;

class AllianceIndustryServiceProvider extends AbstractSeatPlugin
{
    public function boot(){
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/', 'allianceindustry');
        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'allianceindustry');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

//        $this->publishes([
//            __DIR__ . '/resources/js' => public_path('rattingmonitor/js')
//        ]);
    }

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/Config/allianceindustry.sidebar.php','package.sidebar');
        $this->registerPermissions(__DIR__ . '/Config/allianceindustry.permissions.php', 'allianceindustry');
    }

    public function getName(): string
    {
        return 'SeAT Alliance Industry Operations Planner';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/recursivetree/seat-alliance-industry';
    }

    public function getPackagistPackageName(): string
    {
        return 'seat-alliance-industry';
    }

    public function getPackagistVendorName(): string
    {
        return 'recursivetree';
    }
}