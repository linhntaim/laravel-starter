<?php

namespace App\ModelTraits;

use App\ModelRepositories\OAuthImpersonateRepository;
use App\ModelRepositories\UserRepository;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;
use App\Utils\SocialLogin;
use Illuminate\Support\Facades\Hash;

trait PassportTrait
{
    protected $via = null;

    public function findForPassport($username)
    {
        if (request()->has('_e')) {
            $username = AES::decrypt($username, ConfigHelper::getClockBlockKey());
        }
        $userRepository = new UserRepository();
        if ($advanced = json_decode($username)) {
            $socialLogin = SocialLogin::getInstance();
            if ($socialLogin->enabled()) {
                if (!empty($advanced->provider) && !empty($advanced->provider_id)) {
                    $user = $userRepository->notStrict()
                        ->getSocially($advanced->provider, $advanced->provider_id);
                    if ($user) {
                        if ($user->email && !$socialLogin->checkEmailDomain($user->email)) {
                            return null;
                        }
                        $user->via = 'social';
                    }
                    return $user;
                }
            }
            if (!empty($advanced->impersonate_token)) {
                $oAuthImpersonate = (new OAuthImpersonateRepository())->notStrict()
                    ->getByImpersonateToken($advanced->impersonate_token);
                if (!empty($oAuthImpersonate)) {
                    $user = $userRepository->notStrict()->getUniquely($oAuthImpersonate->user_id);
                    if ($user) {
                        $user->via = 'impersonate';
                    }
                    return $user;
                }
            }
        }
        return $userRepository->notStrict()
            ->getByEmail($username);
    }

    public function validateForPassportPasswordGrant($password)
    {
        if (request()->has('_e')) {
            $password = AES::decrypt($password, ConfigHelper::getClockBlockKey());
        }
        $advanced = json_decode($password);
        if ($advanced !== false) {
            if (!empty($advanced->source) && !empty($this->via)
                && $advanced->source == $this->via) return true;
        }
        return Hash::check($password, $this->password);
    }
}
