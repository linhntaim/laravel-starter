<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Console\Commands\Base;

use App\ModelRepositories\Base\IUserRepository;
use App\ModelRepositories\Base\ModelRepository;
use App\ModelRepositories\UserRepository;

trait UserCommandTrait
{
    /**
     * @var IUserRepository|ModelRepository
     */
    protected $userRepository;

    protected function getUserRepositoryClass()
    {
        return UserRepository::class;
    }

    /**
     * @return IUserRepository|ModelRepository
     */
    protected function getUserRepository()
    {
        if (is_null($this->userRepository)) {
            $userRepositoryClass = $this->getUserRepositoryClass();
            $this->userRepository = new $userRepositoryClass;
        }
        return $this->userRepository;
    }

    protected function parseUser($input = 'user', $errorShown = true)
    {
        $this->getUserRepository()->pinModel()
            ->notStrict()
            ->getUniquely($this->argument($input) ?? $this->option($input));
        if ($this->getUserRepository()->doesntHaveModel()) {
            $errorShown && $this->error('Cannot find user');
            return false;
        }
        return true;
    }
}