<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Console\Commands\Base\UserCommandTrait;
use App\Models\Base\IHasEmailVerified;

class NotifyEmailVerificationCommand extends Command
{
    use UserCommandTrait;

    protected $signature = 'notify:verification:email {user} {--again}';

    protected function go()
    {
        if ($this->parseUser()) {
            if (classImplemented($this->userRepository->modelClass(), IHasEmailVerified::class)) {
                $this->userRepository->skipProtected()->notifyEmailVerification($this->option('again'));
            }
        }
    }
}