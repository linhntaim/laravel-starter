<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

use App\Console\Commands\Base\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;

/**
 * Class ConsoleException
 * @package App\Exceptions
 * @method static ConsoleException from($exception, $message = '', $publicAttachedData = [], $privateAttachedData = [])
 */
class ConsoleException extends AppException implements ExceptionInterface
{
    /**
     * @var Command
     */
    protected $command;

    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}
