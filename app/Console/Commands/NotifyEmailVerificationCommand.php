<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\Command;
use App\Console\Commands\Base\UserCommandTrait;
use App\ModelRepositories\Base\IUserVerifyEmailRepository;

class NotifyEmailVerificationCommand extends Command
{
    use UserCommandTrait;

    protected $signature = 'notify:verification:email {user} {--again}';

    protected function go()
    {
        if ($this->parseUser()) {
            if ($this->userRepository instanceof IUserVerifyEmailRepository) {
                $this->userRepository->skipProtected()->notifyEmailVerification($this->option('again'));
            }
        }
    }
}