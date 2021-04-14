<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Providers;

use App\Http\Requests\Request;
use App\Utils\ClientSettings\Manager as ClientSettingsManager;
use App\Utils\ConfigHelper;
use App\Utils\Device\Manager as DeviceManager;
use App\Utils\ExtraActions\FilterAction;
use App\Utils\ExtraActions\HookAction;
use App\Utils\ExtraActions\ReplaceAction;
use App\Utils\Screen\Manager as ScreenManager;
use App\Vendors\Illuminate\Database\Connectors\ConnectionFactory;
use App\Vendors\Illuminate\Log\LogManager;
use App\Vendors\Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected function generateAppId()
    {
        $this->app['id'] = Str::uuid();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->generateAppId();

        $this->app->alias('request', Request::class);

        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
        $this->app->singleton('log', function ($app) {
            return new LogManager($app);
        });
        $this->app->singleton(ClientSettingsManager::class, function () {
            return new ClientSettingsManager();
        });
        $this->app->singleton(DeviceManager::class, function () {
            return new DeviceManager();
        });
        $this->app->singleton(ScreenManager::class, function () {
            return new ScreenManager();
        });
        $this->app->singleton(HookAction::class, function () {
            return new HookAction();
        });
        $this->app->singleton(FilterAction::class, function () {
            return new FilterAction();
        });
        $this->app->singleton(ReplaceAction::class, function () {
            return new ReplaceAction();
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

        mb_detect_order([
            'UTF-8',
            'UTF-7',
            'ASCII',
            'EUC-JP',
            'SJIS',
            'eucJP-win',
            'SJIS-win',
            'JIS',
            'ISO-2022-JP',
        ]);
    }
}
