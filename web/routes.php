<?php

// Basic routes
$router->get('/', function () {
    return view('home', ["title" => "Home"]);
});

$router->get('/about', function () {
    return view('about', ["title" => "About"]);
});

$router->get('/contact', function () {
    return view('contact',["title" => "Contact"]);
});

// Routes with parameters
$router->get('/user/{id}', function ($id) {
    echo "<h1>User Profile</h1>";
    echo "<p>User ID: " . htmlspecialchars($id) . "</p>";
});

$router->get('/user/{id}/posts/{postId}', function ($id, $postId) {
    echo "<h1>User Post</h1>";
    echo "<p>User ID: " . htmlspecialchars($id) . "</p>";
    echo "<p>Post ID: " . htmlspecialchars($postId) . "</p>";
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
});

$router->get("/logout", function () {
    AuthController::logout();
});

// Custom 404 handler
$router->setNotFound(function () {
    http_response_code(404);
    return view("404");
});
