<?php

namespace Core\Http;

class Request
{
    public readonly string $method;
    public readonly array $get;
    public readonly array $post;
    public readonly string $uri;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->get = $_GET;
        $this->post = $_POST;

        // Get requested url and trim it
        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        //Remove extra part of the url if server doesn't point to /public
        $base_url = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
        $base_url = trim($base_url, '/');

        if ($base_url) {
            $uri = ltrim(substr($uri, strlen($base_url)), '/');
        }
        $this->uri = $uri;
    }
}
