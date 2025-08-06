<?php

// Basic routes
$router->get('/', function () {
    return view('home', ["title" => "Home"]);
});

$router->get('/about', function () {
    return view('about', ["title" => "About"]);
});

$router->get('/contact', function () {
    return view('contact', ["title" => "Contact"]);
});

// Routes with parameters
$router->get('/user/{id}', function ($id) {
    if(!isset($_SESSION["user"])) {
        header("location: /login");
        exit(1);
    }
    $user = AuthController::getUser($_COOKIE["access_token"] || "");
    if (!$user) {
        $_SESSION["flash"] = ["error" => "[ERROR]: User can't be found."];
        exit(1);
    }
    $data = [
        "title" => "Profile",
        "user_data" => $user
    ];
    return view("profile", $data);
});

// GET routes
$router->get("/login", function () {
    return view("login", ["title" => "Login"]);
});

$router->get("/register", function () {
    return view("register");
});

// POST routes
$router->post('/login', function () {
    AuthController::login();
});

$router->post('/register', function () {
    echo "<h1>Register POST</h1>";
    echo "<p>Processing registration...</p>";
    //UserController::register();
});

$router->get("/logout", function () {
    AuthController::logout();
});

// Custom 404 handler
$router->setNotFound(function () {
    http_response_code(404);
    return view("404");
});
