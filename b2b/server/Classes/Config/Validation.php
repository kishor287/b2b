<?php

namespace Panel\Server\Classes\Config;

class Validation
{
    protected $data;
    protected $rules;
    protected $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $fieldRules = explode('|', $rules);

            foreach ($fieldRules as $rule) {
                $this->validateRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    protected function validateRule($field, $rule)
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $param) = explode(':', $rule, 2);
            $params = explode(',', $param);
        }

        $methodName = 'validate' . ucfirst($rule);
        if (method_exists($this, $methodName)) {
            $isValid = $this->$methodName($field, $params);
            if (!$isValid) {
                $this->addError($field, $rule);
            }
        } else {
            throw new \Exception("Validation rule '$rule' does not exist.");
        }
    }

    protected function addError($field, $rule)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $rule;
    }

    protected function validateRequired($field, $params)
    {
        return isset($this->data[$field]) && trim($this->data[$field]) !== '';
    }

    protected function validateEmail($field, $params)
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateNumeric($field,$params){
        return filter_var($this->data[$field], FILTER_VALIDATE_INT) !== false;
    }


    // Add more validation methods as needed...

    public function getErrors()
    {
        return $this->errors;
    }
}
