<?php

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
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws
     */
    protected function validatedData(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make(
            $data,
            $rules,
            array_merge($this->validatedMessages($rules), $messages),
            $customAttributes
        );
        if ($validator->fails()) {
            throw (new UserException($validator->errors()->all()))->setAttachedData($validator->errors()->toArray());
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
                    } else {
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
