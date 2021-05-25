<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories\Base;

interface IProtectedRepository
{
    /**
     * @return static
     */
    public function skipProtected();

    public function getProtectedValue();

    public function getProtectedValues();

    public function getNoneProtected();
}