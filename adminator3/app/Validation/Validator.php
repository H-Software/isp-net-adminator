<?php

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Validator
 *
 * @author    Haven Shen <havenshen@gmail.com>
 * @copyright Copyright (c) Haven Shen
 */
class Validator
{
    private $errors;

    private $errors_wrapper_start = '<div class="alert alert-danger" role="alert"><pre>';

    private $errors_wrapper_end = '</pre></div>';

    public function validate(
        array $data,
        array $rules,
        string $errors_wrapper_start = "",
        string $errors_wrapper_end = ""
    ) {
        $errors_wrapper_start = (empty($errors_wrapper_start)) ? $this-> errors_wrapper_start : $errors_wrapper_start;
        $errors_wrapper_end = (empty($errors_wrapper_end)) ? $this-> errors_wrapper_end : $errors_wrapper_end;

        foreach ($rules as $field => $rule) {

            if (preg_match("/#/", $field)) {
                [$field_name, $field_key] = explode("#", $field);
            } else {
                $field_name = $field_key = $field;
            }

            try {
                $rule->setName(ucfirst('"'.$field_name.'"'))->assert($data[$field_key]);
            } catch (NestedValidationException $e) {
                $this->errors[$field_key] = $errors_wrapper_start . $e->getFullMessage() . $errors_wrapper_end;
            }
        }

        return $this;
    }

    public function validatePassword($input, $hash)
    {
        return password_verify($input, $hash);
    }

    public function failed()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
