<?php
require __DIR__ . "/../vendor/autoload.php";
require "../helpers/helpers.php";

use Framework\Router;
use Framework\Session;

Session::start();

// spl_autoload_register(function ($class) {
//     $path = basePath("Framework/{$class}.php");
//     if(file_exists($path)) {
//         require $path;
//     }
// });

// config router
$router = new Router();

require basePath("routes.php");

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$method = $_SERVER["REQUEST_METHOD"];

$router->route($uri, $method);





