<?php

namespace App\Console\Commands;

use App\ModelRepositories\AdminRepository;

class NotifyAdminEmailVerificationCommand extends NotifyEmailVerificationCommand
{
    protected $signature = 'notify:email-verification:admin {id}';

    protected function getUserRepositoryClass()
    {
        return AdminRepository::class;
    }
}