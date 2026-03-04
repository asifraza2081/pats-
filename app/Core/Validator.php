<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): static
    {
        $instance = new static($data);
        $instance->validate($rules);
        return $instance;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value    = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, $value, $ruleName, $param);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        $label = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || trim((string)$value) === '') {
                    $this->errors[$field][] = "{$label} is required.";
                }
                break;

            case 'min':
                if (strlen((string)$value) < (int)$param) {
                    $this->errors[$field][] = "{$label} must be at least {$param} characters.";
                }
                break;

            case 'max':
                if (strlen((string)$value) > (int)$param) {
                    $this->errors[$field][] = "{$label} must not exceed {$param} characters.";
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$label} must be a valid email address.";
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field][] = "{$label} must be a number.";
                }
                break;

            case 'cnic':
                // Pakistani CNIC: 13 digits with optional dashes
                $clean = preg_replace('/[-\s]/', '', (string)$value);
                if ($value && !preg_match('/^\d{13}$/', $clean)) {
                    $this->errors[$field][] = "{$label} must be a valid 13-digit CNIC (e.g. 3520115500015).";
                }
                break;

            case 'phone':
                // Pakistani mobile: 03xxxxxxxxx or +923xxxxxxxxx
                $clean = preg_replace('/[\s\-]/', '', (string)$value);
                if ($value && !preg_match('/^(\+92|0)3[0-9]{9}$/', $clean)) {
                    $this->errors[$field][] = "{$label} must be a valid Pakistani mobile number.";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->errors[$field][] = "{$label} confirmation does not match.";
                }
                break;

            case 'in':
                $allowed = explode(',', $param ?? '');
                if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
                    $this->errors[$field][] = "{$label} must be one of: " . implode(', ', $allowed) . '.';
                }
                break;

            case 'date':
                if ($value && !strtotime($value)) {
                    $this->errors[$field][] = "{$label} must be a valid date.";
                }
                break;

            case 'unique':
                // Usage: unique:table,column  — checked externally, placeholder here
                break;
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
