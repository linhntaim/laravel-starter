<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\Exceptions\UserException;
use App\Rules\Base\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ValidationTrait
 * @package App\Utils
 * @mixin ClassTrait
 */
trait ValidationTrait
{
    protected $validationThrown = true;

    public function setValidationThrown(bool $validationThrown)
    {
        $this->validationThrown = $validationThrown;
        return $this;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @param callable|null $hook
     * @return bool|\Illuminate\Contracts\Validation\Validator
     * @throws UserException
     */
    protected function validatedData(array $data, array $rules, array $messages = [], array $customAttributes = [], callable $hook = null)
    {
        $validator = Validator::make(
            $data,
            $rules,
            array_merge($this->validatedMessages($rules), $messages),
            $customAttributes
        );
        if ($hook) {
            $validator->after($hook);
        }
        if ($validator->fails()) {
            if ($this->validationThrown) {
                throw (new UserException($validator->errors()->all()))->setAttachedData($validator->errors()->toArray());
            }
            return $validator;
        }
        return true;
    }

    private function validatedMessages(array $rules)
    {
        $messages = [];
        foreach ($rules as $inputName => $subRules) {
            if (is_string($subRules)) {
                $subRules = explode('|', $subRules);
            }
            if (is_array($subRules)) {
                foreach ($subRules as &$subRule) {
                    $rule = '';
                    if (!is_string($subRule)) {
                        if (is_object($subRule) && method_exists($subRule, '__toString')) {
                            $rule = $subRule->__toString();
                        }
                    }
                    else {
                        $rule = $subRule;
                    }

                    $ruleName = explode(':', $rule, 2)[0];
                    $errorName = $inputName . (empty($ruleName) ? '' : '.' . $ruleName);
                    if (static::__hasTransErrorWithModule($errorName)) {
                        if ($subRule instanceof Rule) {
                            $subRule->setTransPath(static::__transErrorPathWithModule($inputName));
                        }
                        $messages[$errorName] = static::__transErrorWithModule($errorName);
                    }
                }
            }
        }
        return $messages;
    }
}
