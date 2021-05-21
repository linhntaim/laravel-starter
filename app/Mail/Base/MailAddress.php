<?php

namespace App\Mail\Base;

use App\Exceptions\AppException;

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
     * @return static
     * @throws
     */
    public static function from($mailAddress)
    {
        if (is_array($mailAddress)) {
            if (blank($mailAddress['address'] ?? null)) {
                throw new AppException('E-mail address has been not set.');
            }
            return (new static())
                ->setAddress($mailAddress['address'])
                ->setName($mailAddress['name'] ?? null);
        }
        return (new static())->setAddress($mailAddress);
    }
}