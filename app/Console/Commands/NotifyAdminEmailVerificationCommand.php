<?php

namespace App\Console\Commands;

use App\ModelRepositories\AdminRepository;
use App\Utils\ClientSettings\Traits\AdminConsoleClientTrait;

class NotifyAdminEmailVerificationCommand extends NotifyEmailVerificationCommand
{
    use AdminConsoleClientTrait;

    protected $signature = 'notify:email-verification:admin {user} {--again}';

    protected function getUserRepositoryClass()
    {
        return AdminRepository::class;
    }
}