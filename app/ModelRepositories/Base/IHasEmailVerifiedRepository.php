<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

interface IHasEmailVerifiedRepository
{
    public function notifyEmailVerification($again = false);

    public function verifyEmailByCode($code);

    public function verifyEmail();

    public function unverifyEmailByCode($code, $fresh = true);

    public function unverifyEmail($fresh = true);
}