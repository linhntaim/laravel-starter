<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Rules\Base;

use Illuminate\Contracts\Validation\Rule as IRule;
use Illuminate\Support\Str;

abstract class Rule implements IRule
{
    protected $attribute;
    protected $transPath;
    protected $name;
    protected $overriddenMessage;

    public function __construct()
    {
        $this->transPath = 'error.rules';
    }

    protected function getAttributeName()
    {
        return strtolower(implode(' ', explode('_', Str::snake($this->attribute))));
    }

    public abstract function passes($attribute, $value);

    public function setTransPath($transPath)
    {
        $this->transPath = $transPath;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return empty($this->overriddenMessage) ? trans($this->transPath . '.' . $this->name, [
            'attribute' => $this->getAttributeName(),
        ]) : $this->overriddenMessage;
    }

    public function overrideMessage($message)
    {
        $this->overriddenMessage = $message;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
