<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\ModelRepositories\PermissionRepository;
use App\ModelRepositories\RoleRepository;
use App\ModelResources\Base\ModelTransformTrait;
use App\Utils\Framework\ClientLimiter;
use App\Utils\ConfigHelper;
use App\Utils\Framework\ServerMaintainer;
use App\Utils\GuardArrayTrait;
use App\Utils\HandledFiles\Helper;
use App\Utils\SocialLogin;

abstract class PrerequisiteController extends ApiController
{
    use ModelTransformTrait, GuardArrayTrait;

    protected $dataset;

    public function __construct()
    {
        parent::__construct();

        $this->dataset = [];
    }

    public function index(Request $request)
    {
        $this->dataset($request);
        return $this->responseSuccess($this->dataset);
    }

    protected function dataset(Request $request)
    {
        $this->server($request);
        $this->roles($request);
        $this->permissions($request);
        $this->locales($request);
    }

    protected function server(Request $request)
    {
        if ($request->has('server')) {
            $socialLogin = SocialLogin::getInstance();
            $this->dataset['server'] = [
                'c' => time(),
                'm' => ($serverMaintainer = (new ServerMaintainer())->retrieve()) ? $serverMaintainer->toArray() : null,
                'l' => ($clientLimiter = (new ClientLimiter())->retrieve()) ? $clientLimiter->toArray() : null,
                'url' => url('/'),
                'ips' => $request->ips(),
                'throttle_request' => [
                    'max_attempts' => ConfigHelper::get('throttle_request.max_attempts'),
                    'decay_minutes' => ConfigHelper::get('throttle_request.decay_minutes'),
                ],
                'max_upload_file_size' => Helper::maxUploadFileSize(),
                'variables' => ConfigHelper::get('variables'),
                'app_options' => $this->guardEmptyArray(
                    $this->modelTransform((new AppOptionRepository())->getAll()->keyBy('key'))
                ),
                'gtm_code' => ConfigHelper::get('gtm_code'),
                'social_login' => [
                    'enabled' => $socialLogin->enabled(),
                    'email_domain' => [
                        'allowed' => $this->guardEmptyArray($socialLogin->allowedEmailDomains()),
                        'denied' => $this->guardEmptyArray($socialLogin->deniedEmailDomains()),
                    ],
                ],
                'forgot_password_enabled' => ConfigHelper::get('forgot_password_enabled'),
            ];
        }
    }

    protected function roles(Request $request)
    {
        if ($request->has('roles')) {
            $this->dataset['roles'] = $this->modelTransform(
                (new RoleRepository())->getNoneProtected()
            );
        }
    }

    protected function permissions(Request $request)
    {
        if ($request->has('permissions')) {
            $this->dataset['permissions'] = $this->modelTransform(
                (new PermissionRepository())->getNoneProtected()
            );
        }
    }

    protected function locales(Request $request)
    {
        if ($request->has('locales')) {
            $locales = [];
            foreach (ConfigHelper::getLocaleCodes() as $code) {
                $locales[] = [
                    'code' => $code,
                    'name' => trans('locale.' . $code),
                ];
            }
            $this->dataset['locales'] = $locales;
        }
    }
}
