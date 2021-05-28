<?php

namespace App\Console\Commands;

use App\ModelRepositories\AdminRepository;
use App\Utils\ClientSettings\Traits\AdminConsoleClientTrait;

class NotifyAdminEmailVerificationCommand extends NotifyEmailVerificationCommand
{
    use AdminConsoleClientTrait;

    protected $signature = 'notify:verification:email:admin {user} {--again}';

    protected function getUserRepositoryClass()
    {
        return AdminRepository::class;
    }
}