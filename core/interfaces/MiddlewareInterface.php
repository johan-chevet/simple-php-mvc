<?php

namespace Core\Interfaces;

use Closure;
use Core\Http\Request;
use Core\Http\Response;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Closure $next): Response;
}
