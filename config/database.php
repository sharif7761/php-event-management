<?php

// Load .env file manually
$envFilePath = __DIR__ . '/../.env';
if (file_exists($envFilePath)) {
    $envVariables = parse_ini_file($envFilePath);
    foreach ($envVariables as $key => $value) {
        $_ENV[$key] = $value;
    }
}

return [
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
];