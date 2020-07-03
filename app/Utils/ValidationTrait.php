<?php

namespace App\Utils;

use App\Exceptions\UserException;
use App\Http\Requests\Request;
use App\Rules\Rule;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    /**
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws
     */
    protected function validatedData(array $inputs, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make(
            $inputs,
            $rules,
            array_merge($this->validatedMessages($rules), $messages),
            $customAttributes
        );
        if ($validator->fails()) {
            throw (new UserException($validator->errors()->all()))->setAttachedData($validator->errors()->toArray());
        }
        return true;
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws
     */
    protected function validated(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->validatedData($request->all(), $rules, $messages, $customAttributes);
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

                    $ruleName = explode(':', $rule)[0];
                    $errorName = $inputName . (empty($ruleName) ? '' : '.' . $ruleName);
                    if ($this->__hasTransErrorWithModule($errorName)) {
                        if ($subRule instanceof Rule) {
                            $subRule->setTransPath($this->__transErrorPathWithModule($inputName));
                        }
                        $messages[$errorName] = $this->__transErrorWithModule($errorName);
                    }
                }
            }
        }
        return $messages;
    }
}
