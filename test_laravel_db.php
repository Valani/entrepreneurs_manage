<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$db = $app->make('db');

try {
    $result = $db->select('SELECT 1');
    echo "Connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}