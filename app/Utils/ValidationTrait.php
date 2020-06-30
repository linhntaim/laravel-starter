<?php

namespace App\Utils;

use App\Exceptions\UserException;
use App\Http\Requests\Request;
use App\Rules\Rule;
use Illuminate\Contracts\Validation\Factory;

trait ValidationTrait
{
    protected function getValidator()
    {
        return app(Factory::class);
    }

    protected function validatedInputs(array $inputs, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidator()->make(
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

    protected function validated(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->validatedInputs($request->all(), $rules, $messages, $customAttributes);
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
