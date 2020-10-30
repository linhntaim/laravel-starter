<?php

namespace App\ModelRepositories\Base;

interface IProtectedRepository
{
    public function skipProtected();

    public function getProtectedValue();

    public function getProtectedValues();

    public function getNoneProtected();
}