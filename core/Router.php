<?php

namespace Core;

use Core\Http\Request;
use Core\Http\Response;

class Router
{

    private array $routes;
    private array $available_paths;
    private Request $request;

    public function __construct(Request $request)
    {
        $this->routes = ROUTES;
        $this->available_paths = array_keys($this->routes);
        $this->request = $request;
    }

    private function is_param(string $str): bool
    {
        if (str_starts_with($str, '{') && str_ends_with($str, '}')) {
            return true;
        }
        return false;
    }

    public function parse_url()
    {
        $route = null;
        $url_splited = explode('/', $this->request->uri);
        foreach ($this->available_paths as $path_available) {
            $match_found = true;
            $params = [];
            $path_splited = explode('/', trim($path_available, '/'));

            if (count($url_splited) !== count($path_splited)) {
                // no match possible
                continue;
            }
            foreach ($url_splited as $i => $url_part) {

                if ($this->is_param($path_splited[$i])) {

                    $param = explode(':', trim($path_splited[$i], '{}'));
                    if (count($param) == 2 && $param[1] === 'int' && !int_validation($url_part)) {
                        $match_found = false;
                        break;
                    }

                    $params[] = $url_part;
                } else if ($url_part !== $path_splited[$i]) {
                    $match_found = false;
                    break;
                }
            }

            if ($match_found) {
                $route = $this->routes[$path_available];
                break;
            }
        }
        return [$route, $params];
    }

    public function dispatch()
    {
        [$route, $params] = $this->parse_url();

        // No match found
        if ($route === null) {
            $response = new Response();
            return $response->body(load_view('errors/404'))->set_status(404);
        }

        // Instanciate controller
        $controller = new $route['controller']($this->request);
        $method = $route['method'];

        $controller_function = function ($request) use ($controller, $method, $params) {
            return call_user_func_array([$controller, $method], $params);
        };

        $middlewares = array_reverse($route['middleware'] ?? []);
        $next = $controller_function;

        foreach ($middlewares as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware($request, $next);
            };
        }

        return $next($this->request);
    }
}
