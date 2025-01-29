<?php
require_once 'Migration.php';

$args = array_slice($argv, 1);
$command = $args[0] ?? null;

$migration = new Migration();

switch ($command) {
    case 'migrate':
        $migration->migrate();
        break;
    case 'seed':
        $migration->seed();
        break;
    case 'fresh':
        $migration->migrate();
        $migration->seed();
        break;
    default:
        echo "Invalid command. Available commands:\n";
        echo "php database/migrate.php migrate - Run migrations\n";
        echo "php database/migrate.php seed - Run seeder\n";
        echo "php database/migrate.php fresh - Run migrations and seeder\n";
}