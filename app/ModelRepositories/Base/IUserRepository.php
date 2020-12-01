<?php

namespace App\ModelRepositories\Base;

interface IUserRepository
{
    public function updateLastAccessedAt();
}