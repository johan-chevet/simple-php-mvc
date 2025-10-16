<?php

namespace Core;

class Validator
{

    private array $data;

    // private array $data_info = [];

    private string $current;

    private array $validations = [];

    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function add(string $key): static
    {
        $this->current = $key;
        return $this;
    }

    public function required(?string $message = null)
    {
        // var_dump(!empty($this->data[$this->current]));
        $this->validations[$this->current][] = [
            'callback' => fn() => !empty($this->data[$this->current]) &&
                !empty(trim($this->data[$this->current])),
            'message' => $message ?? "Field is required"
        ];
        return $this;
    }

    public function is_int(?string $message = null)
    {
        // $this->data_info[$this->current]['type'] = 'int';
        $this->validations[$this->current][] = [
            'callback' => fn() => int_validation($this->data[$this->current]),
            'message' => $message ?? "Must be a valid integer"
        ];
        return $this;
    }

    public function min_length(int $len, ?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => fn() => strlen($this->data[$this->current]) >= $len,
            'message' => $message ?? "Must have at least $len characters"
        ];
        return $this;
    }

    public function max_length(int $len, ?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => fn() => strlen($this->data[$this->current]) <= $len,
            'message' => $message ?? "Must have $len characters maximum"
        ];
        return $this;
    }

    public function less_than(int $nb, ?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => fn() => $this->data[$this->current] < $nb,
            'message' => $message ?? "Must be less than $nb"
        ];
        return $this;
    }

    public function greater_than(int $nb, ?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => fn() => $this->data[$this->current] > $nb,
            'message' => $message ?? "Must be greater than $nb"
        ];
        return $this;
    }

    public function is_date(?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => fn() => strtotime($this->data[$this->current]),
            'message' => $message ?? "Invalid date format"
        ];
        return $this;
    }

    /**
     * Summary of range
     * Check if value is in wanted range
     * Works with int, array and string
     * @param int $min
     * @param int|null $max
     * @param string|null $message
     * @return static
     */
    public function range(int $min, ?int $max = null, ?string $message = null)
    {
        $this->validations[$this->current][] = [
            'callback' => function () use ($min, $max) {
                $value = $this->data[$this->current];

                if (is_array($value)) {
                    $value = count($value);
                } else if (int_validation($value)) {
                    $value = (int)$value;
                } else {
                    $value = strlen($value);
                }

                if ($value < $min) {
                    return false;
                }
                if ($max && $value > $max) {
                    return false;
                }
                return true;
            },
            'message' => $message ?? "Invalid date format"
        ];
        return $this;
    }

    public function custom(callable $fn, string $message)
    {
        $this->validations[$this->current][] =  [
            'callback' => $fn,
            'message' => $message
        ];
        return $this;
    }

    /**
     * Executes validation functions for all keys
     * @return array of errors
     */
    public function validate(): array
    {
        foreach ($this->validations as $key => $rules) {
            $this->current = $key;
            foreach ($rules as $rule) {
                if (!$rule['callback']($this->data[$key] ?? null, $this->data)) {
                    $this->errors[$key] = $rule['message'];
                    break;
                }
            }
        }
        return $this->errors;
    }

    public function get_errors()
    {
        return $this->errors;
    }
}
