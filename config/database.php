<?php
// Database configuration - PostgreSQL
$db_host = $_ENV['PGHOST'] ?? 'localhost';
$db_name = $_ENV['PGDATABASE'] ?? 'aneti_clube';
$db_user = $_ENV['PGUSER'] ?? 'postgres';
$db_pass = $_ENV['PGPASSWORD'] ?? '';
$db_port = $_ENV['PGPORT'] ?? '5432';

try {
    $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
