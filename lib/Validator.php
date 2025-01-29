<?php
class Validator
{
    private $errors = [];

    public function validate($data, $rules)
    {
        foreach ($rules as $field => $rule) {
            if (isset($rule['required']) && $rule['required'] && empty($data[$field])) {
                $this->errors[$field][] = ucfirst($field) . " is required";
                continue;
            }

            if (isset($rule['email']) && $rule['email'] && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = "Invalid email format";
            }

            if (isset($rule['min']) && strlen($data[$field]) < $rule['min']) {
                $this->errors[$field][] = ucfirst($field) . " must be at least {$rule['min']} characters";
            }

            if (isset($rule['match']) && $data[$field] !== $data[$rule['match']]) {
                $this->errors[$field][] = ucfirst($field) . " does not match";
            }
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}