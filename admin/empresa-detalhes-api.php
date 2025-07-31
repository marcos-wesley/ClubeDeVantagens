<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit();
}

$empresa_id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->execute([$empresa_id]);
    $company = $stmt->fetch();
    
    if (!$company) {
        echo json_encode(['success' => false, 'message' => 'Empresa não encontrada']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'company' => $company
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar empresa']);
}
?>