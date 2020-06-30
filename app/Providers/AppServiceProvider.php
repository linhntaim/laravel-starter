<?php

namespace App\Providers;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use App\Utils\ExtraActions\HookExtraAction;
use App\Vendors\Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('request', Request::class);

        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
        $this->app->singleton(HookExtraAction::class, function () {
            return new HookExtraAction();
        });

        $publicPath = ConfigHelper::get('public_path');
        if ($publicPath) {
            $this->app->bind('path.public', function () use ($publicPath) {
                return base_path($publicPath);
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Str::startsWith(ConfigHelper::getAppUrl(), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
