<?php
// Database configuration - PostgreSQL
$db_host = $_ENV['PGHOST'] ?? 'localhost';
$db_name = $_ENV['PGDATABASE'] ?? 'aneti_clube';
$db_user = $_ENV['PGUSER'] ?? 'postgres';
$db_pass = $_ENV['PGPASSWORD'] ?? '';
$db_port = $_ENV['PGPORT'] ?? '5432';

try {
    $pdo = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $pdo = null; // Set to null on error instead of dying
    error_log("Database connection error: " . $e->getMessage());
}
?>
