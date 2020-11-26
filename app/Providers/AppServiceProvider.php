<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Providers;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Manager as ClientSettingsManager;
use App\Utils\ConfigHelper;
use App\Utils\ExtraActions\FilterAction;
use App\Utils\ExtraActions\HookAction;
use App\Utils\ClientSettings\Facade;
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
        $this->app->singleton(ClientSettingsManager::class, function () {
            return new ClientSettingsManager();
        });
        $this->app->singleton(HookAction::class, function () {
            return new HookAction();
        });
        $this->app->singleton(FilterAction::class, function () {
            return new FilterAction();
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
        if (Str::startsWith(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Facade::autoFetch();

        mb_detect_order([
            'UTF-8',
            'UTF-7',
            'ASCII',
            'EUC-JP',
            'SJIS',
            'eucJP-win',
            'SJIS-win',
            'JIS',
            'ISO-2022-JP'
        ]);
    }
}
