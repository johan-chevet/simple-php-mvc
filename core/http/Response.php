<?php

namespace Core\Http;

class Response
{
    private int $status_code = 200;
    private array $headers = [];
    private string $body;

    public function with_header(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function set_status(int $status): static
    {
        $this->status_code = $status;
        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function get_body(): string
    {
        return $this->body;
    }

    public function redirect(string $path, int $code = 301): static
    {
        $this->set_status($code);
        if ($path === '/') {
            $path = '';
        }
        $this->with_header("Location", BASE_URL . "/$path");
        return $this;
    }

    public function send()
    {
        http_response_code($this->status_code);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->body;
    }
}
