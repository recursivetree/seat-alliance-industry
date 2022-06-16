<?php

namespace RecursiveTree\Seat\AllianceIndustry;

use RecursiveTree\Seat\AllianceIndustry\Models\Delivery;
use RecursiveTree\Seat\AllianceIndustry\Models\Order;
use RecursiveTree\Seat\AllianceIndustry\Observers\DeliveryObserver;
use RecursiveTree\Seat\AllianceIndustry\Observers\OrderObserver;
use RecursiveTree\Seat\AllianceIndustry\Policies\UserPolicy;
use Seat\Services\AbstractSeatPlugin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;

class AllianceIndustryServiceProvider extends AbstractSeatPlugin
{
    public function boot(){
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/', 'allianceindustry');
        $this->loadViewsFrom(__DIR__ . '/resources/views/', 'allianceindustry');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        Gate::define('allianceindustry.same-user', UserPolicy::class.'@checkUser');

        Delivery::observe(DeliveryObserver::class);
        Order::observe(OrderObserver::class);

        Blade::directive('checked', function($condition) {
            return "<?php if($condition){ echo \"checked=\\\"checked\\\"\"; } ?>";
        });
        Blade::directive('selected', function($condition) {
            return "<?php if($condition){ echo \"selected=\\\"selected\\\"\"; } ?>";
        });
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