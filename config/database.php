<?php
// Database configuration - MySQL
$db_host = $_ENV['MYSQL_HOST'] ?? 'localhost';
$db_name = $_ENV['MYSQL_DATABASE'] ?? 'aneti_clube';
$db_user = $_ENV['MYSQL_USER'] ?? 'root';
$db_pass = $_ENV['MYSQL_PASSWORD'] ?? '';
$db_port = $_ENV['MYSQL_PORT'] ?? '3306';
$db_socket = $_ENV['MYSQL_SOCKET'] ?? __DIR__ . '/../mysql_data/mysql.sock';

try {
    // Use socket connection for local MySQL in Replit
    if ($db_host === 'localhost' && file_exists($db_socket)) {
        $conn = new PDO("mysql:unix_socket=$db_socket;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    } else {
        $conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
