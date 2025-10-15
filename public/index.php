<?php
require '../core/Autoloader.php';

use Core\Autoloader;
use Core\Http\Response;

Autoloader::register();

require '../config/config.php';
require '../config/routes.php';
require '../config/database.php';
require '../core/utilities/utilities.php';

use Core\Router;
use Core\Http\Request;
use Core\SessionManager;

// TODO CSRF
SessionManager::start();
// Activer l'affichage des erreurs en développement
// À désactiver en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

$request = new Request();

$router = new Router($request);

$response = $router->dispatch();

$response->send();
