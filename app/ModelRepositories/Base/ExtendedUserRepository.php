<?php

namespace App\ModelRepositories\Base;

use App\ModelRepositories\UserRepository;

trait ExtendedUserRepository
{
    public function updateLastAccessedAt()
    {
        return (new UserRepository())
            ->withModel($this->model->user)
            ->updateLastAccessedAt();
    }
}