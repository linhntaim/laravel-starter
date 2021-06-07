<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Mail\Base;

use App\Exceptions\AppException;
use App\Models\Base\IContactable;
use App\Models\Base\IMailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Throwable;

class MailAddress
{
    /**
     * @var string
     */
    public $address;

    /**
     * @var string|null
     */
    public $name = null;

    /**
     * @param string $address
     * @return static
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|array $mailAddress
     * @param Throwable|string|null $exception
     * @return static|null
     * @throws
     */
    public static function from($mailAddress, $exception = null)
    {
        if (is_array($mailAddress)) {
            return static::fromAddress($mailAddress['address'] ?? null, $mailAddress['name'] ?? null, $exception);
        }
        if (is_string($mailAddress)) {
            return static::fromAddress($mailAddress, null, $exception);
        }
        if ($mailAddress instanceof IContactable) {
            return static::fromAddress($mailAddress->preferredEmail(), $mailAddress->preferredName(), $exception);
        }
        if ($mailAddress instanceof IMailable) {
            return static::fromAddress($mailAddress->preferredEmail(), null, $exception);
        }
        if ($mailAddress instanceof AnonymousNotifiable) {
            return static::fromAddress($mailAddress->routeNotificationFor('mail'), null, $exception);
        }
        return static::fromAddress(null, null, $exception);
    }

    /**
     * @param string $address
     * @param string|null $name
     * @param Throwable|string|null $exception
     * @return static|null
     * @throws
     */
    public static function fromAddress($address, $name = null, $exception = null)
    {
        if (filled($address)) {
            return (new static())->setAddress($address)->setName($name);
        }
        if (!is_null($exception)) {
            throw ($exception instanceof Throwable ? $exception : new AppException($exception));
        }
        return null;
    }
}