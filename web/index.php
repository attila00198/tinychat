<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start([
    "cookie_lifetime" => 16000
]);
if (!isset($_SESSION["user"])) {
    setcookie("access_token", "", time() - 3600, "/");
}

require_once __DIR__ . "/includes/class.View.php";
View::setViewsPath("views");
View::setLayout("/layout");

require_once __DIR__ . "/includes/class.Router.php";
require_once __DIR__ . "/controllers/AuthController.php";

$router = new Router();
require_once __DIR__ . "/routes.php";

$router->run();
