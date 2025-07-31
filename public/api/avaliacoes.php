<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa_id = intval($_POST['empresa_id']);
    $usuario_nome = sanitizeInput($_POST['usuario_nome']);
    $usuario_email = sanitizeInput($_POST['usuario_email']);
    $rating = intval($_POST['rating']);
    $comentario = sanitizeInput($_POST['comentario']);
    
    // Validate input
    if (!$empresa_id || !$usuario_nome || !$rating || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }
    
    try {
        $conn = getConnection();
        
        // Insert review
        $stmt = $conn->prepare("INSERT INTO avaliacoes (empresa_id, usuario_nome, usuario_email, rating, comentario, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$empresa_id, $usuario_nome, $usuario_email, $rating, $comentario]);
        
        // Update company average rating
        $stmt = $conn->prepare("
            UPDATE empresas SET 
                avaliacao_media = (SELECT AVG(rating) FROM avaliacoes WHERE empresa_id = ?),
                total_avaliacoes = (SELECT COUNT(*) FROM avaliacoes WHERE empresa_id = ?)
            WHERE id = ?
        ");
        $stmt->execute([$empresa_id, $empresa_id, $empresa_id]);
        
        echo json_encode(['success' => true, 'message' => 'Avaliação adicionada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar avaliação: ' . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $empresa_id = intval($_GET['empresa_id']);
    
    if (!$empresa_id) {
        echo json_encode(['success' => false, 'message' => 'ID da empresa não fornecido']);
        exit;
    }
    
    try {
        $conn = getConnection();
        
        // Get reviews
        $stmt = $conn->prepare("SELECT * FROM avaliacoes WHERE empresa_id = ? ORDER BY created_at DESC");
        $stmt->execute([$empresa_id]);
        $reviews = $stmt->fetchAll();
        
        // Get rating summary
        $stmt = $conn->prepare("
            SELECT 
                AVG(rating) as media,
                COUNT(*) as total,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as star5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as star4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as star3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as star2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as star1
            FROM avaliacoes WHERE empresa_id = ?
        ");
        $stmt->execute([$empresa_id]);
        $summary = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'summary' => $summary
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao carregar avaliações: ' . $e->getMessage()]);
    }
}
?>