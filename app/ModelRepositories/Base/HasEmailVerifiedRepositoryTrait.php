<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

use App\Models\Base\IHasEmailVerified;
use App\Utils\ClientSettings\DateTimer;

/**
 * Trait HasEmailVerifiedRepositoryTrait
 * @package App\ModelRepositories\Base
 * @property IHasEmailVerified|null $model
 */
trait HasEmailVerifiedRepositoryTrait
{
    /**
     * @var IHasEmailVerified
     */
    protected $newVerifiedModel;

    /**
     * @return IHasEmailVerified
     */
    protected function newVerifiedModel()
    {
        return is_null($this->newVerifiedModel) ?
            ($this->newVerifiedModel = $this->newModel(false)) : $this->newVerifiedModel;
    }

    public function getEmailVerifiedCodeAttributeName()
    {
        return $this->newVerifiedModel()->getEmailVerifiedCodeAttributeName();
    }

    public function getEmailVerifiedSentAtAttributeName()
    {
        return $this->newVerifiedModel()->getEmailVerifiedSentAtAttributeName();
    }

    public function getEmailVerifiedAttributeName()
    {
        return $this->newVerifiedModel()->getEmailVerifiedAttributeName();
    }

    public function getEmailVerifiedCodeLength()
    {
        return $this->newVerifiedModel()->getEmailVerifiedCodeLength();
    }

    public function notifyEmailVerification($again = false)
    {
        if ($this->model->emailVerified) {
            return $this->model;
        }
        $verifyCodeAttributeName = $this->getEmailVerifiedCodeAttributeName();
        if (is_null($this->model->{$verifyCodeAttributeName}) || $again == true) {
            $this->model = $this->updateUniqueValue(
                $verifyCodeAttributeName,
                true,
                $this->getEmailVerifiedCodeLength(),
                null,
                [
                    $this->getEmailVerifiedSentAtAttributeName() => DateTimer::syncNow(),
                ]
            );
            $this->model->sendEmailVerificationNotification();
        }
        return $this->model;
    }

    public function verifyEmailByCode($code)
    {
        $this->notStrict()
            ->pinModel()
            ->getByUnique($this->getEmailVerifiedCodeAttributeName(), $code, true);
        if ($this->doesntHaveModel()) {
            return null;
        }
        if ($this->model->getEmailVerificationExpired()) {
            return null;
        }
        return $this->verifyEmail();
    }

    public function verifyEmail()
    {
        if ($this->model->emailVerified) {
            return $this->model;
        }
        return $this->updateWithAttributes([
            $this->getEmailVerifiedAttributeName() => true,
        ]);
    }

    public function unverifyEmailByCode($code, $fresh = true)
    {
        $this->notStrict()
            ->pinModel()
            ->getByUnique($this->getEmailVerifiedCodeAttributeName(), $code, true);
        if ($this->doesntHaveModel()) {
            return null;
        }
        return $this->unverifyEmail($fresh);
    }

    public function unverifyEmail($fresh = true)
    {
        return $this->updateWithAttributes([
                $this->getEmailVerifiedAttributeName() => false,
            ] + ($fresh ? [
                $this->getEmailVerifiedCodeAttributeName() => null,
                $this->getEmailVerifiedSentAtAttributeName() => null,
            ] : []));
    }
}