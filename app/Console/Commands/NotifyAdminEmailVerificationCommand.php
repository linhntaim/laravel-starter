<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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