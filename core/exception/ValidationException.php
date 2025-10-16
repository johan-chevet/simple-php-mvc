<?php

namespace Core\Exception;

use Exception;

class ValidationException extends Exception
{
    private string $key;
    public function __construct(string $key, string $message)
    {
        parent::__construct($message, 400);
        $this->key = $key;
    }

    public function getKeyAndMessage(): array
    {
        return [$this->key, $this->getMessage()];
    }
}
