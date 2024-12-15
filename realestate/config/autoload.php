<?php

require __DIR__ . '/../app/Utils/ResponseHelper.php';
require __DIR__ . '/../app/Utils/Validator.php';
require __DIR__ . '/../app/Utils/Router.php';

// Database
require __DIR__ . '/database.php';

// Instantiate database connection
$pdo = Database::getConnection();