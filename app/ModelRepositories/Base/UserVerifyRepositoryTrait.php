<?php

namespace App\ModelRepositories\Base;

use App\Models\Base\IUserVerifyEmail;
use Illuminate\Support\Facades\DB;

/**
 * Trait UserVerifyRepositoryTrait
 * @package App\ModelRepositories\Base
 * @property IUserVerifyEmail|null $model
 */
trait UserVerifyRepositoryTrait
{
    public function notifyEmailVerification($again = false)
    {
        if ($this->model->emailVerified) {
            return $this->model;
        }
        $verifyCodeAttributeName = $this->getEmailVerifiedCodeAttributeName();
        if (is_null($this->model->{$verifyCodeAttributeName}) || $again == true) {
            $this->model = $this->updateUniqueValue($verifyCodeAttributeName, true);
            $this->model->sendEmailVerificationNotification();
        }
        return $this->model;
    }

    public function getEmailVerifiedCodeAttributeName()
    {
        return $this->newModel(false)->getEmailVerifiedCodeAttributeName();
    }

    public function verifyEmailByCode($code)
    {
        $this->notStrict()->pinModel()->getByUnique($this->getEmailVerifiedCodeAttributeName(), $code, true);
        if ($this->doesntHaveModel()) {
            return null;
        }
        if ($this->model->emailVerified) {
            return $this->model;
        }
        return $this->updateWithAttributes([
            $this->newModel(false)->getEmailVerifiedAttributeName() => true,
        ]);
    }
}