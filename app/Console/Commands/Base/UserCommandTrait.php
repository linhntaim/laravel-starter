<?php

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
        $userRepositoryClass = $this->getUserRepositoryClass();
        return new $userRepositoryClass;
    }

    protected function parseUser()
    {
        $this->userRepository = $this->getUserRepository();
        $this->userRepository->pinModel()
            ->notStrict()
            ->getUniquely($this->argument('user'));
        if ($this->userRepository->doesntHaveModel()) {
            $this->error('Cannot find user');
            return false;
        }
        return true;
    }
}