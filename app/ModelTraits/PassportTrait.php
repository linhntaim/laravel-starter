<?php

namespace App\ModelTraits;

use App\Models\SysToken;
use App\Models\User;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;
use Illuminate\Support\Facades\Hash;

trait PassportTrait
{
    protected $via = null;

    public function findForPassport($username)
    {
        if (request()->has('_e')) {
            $username = AES::decrypt($username, ConfigHelper::getClockBlockKey());
        }
        if ($advanced = json_decode($username)) {
            if (ConfigHelper::isSocialLoginEnabled()) {
                if (!empty($advanced->provider) && !empty($advanced->provider_id)) {
                    $user = User::whereHas('socials', function ($query) use ($advanced) {
                        $query->where('provider', $advanced->provider)
                            ->where('provider_id', $advanced->provider_id);
                    })->first();
                    if ($user) $user->via = 'social';
                    return $user;
                }
            }
            if (!empty($advanced->token) && !empty($advanced->id)) {
                $sysToken = SysToken::where('type', SysToken::TYPE_LOGIN)
                    ->where('token', $advanced->token)->first();
                if (!empty($sysToken)) {
                    $sysToken->delete();
                    $user = User::where('id', $advanced->id)
                        ->orWhere('email', $advanced->id)
                        ->first();
                    if ($user) $user->via = 'token';
                    return $user;
                }
            }
        }
        return User::where('email', $username)->first();
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
