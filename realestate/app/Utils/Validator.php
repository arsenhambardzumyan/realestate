<?php

namespace app\Utils;

use app\Exceptions\ValidationException;

class Validator
{
    public static function validate(array $data, array $rules)
    {
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new ValidationException("$field is required");
            }
        }
    }
}
