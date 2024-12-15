<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include autoload
require __DIR__ . '/../config/autoload.php';

// Get the Router instance
$router = require __DIR__ . '/../routes/web.php';

// Dispatch the current request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
