<?php

use Src\Controllers\HomeController;

const ROUTES = [
    '/' => [
        'controller' => HomeController::class,
        'method' => 'index'
    ],
];
