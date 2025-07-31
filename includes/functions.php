<?php
// Determine the correct path to config based on current directory
$config_path = '';
if (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false || 
    strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || 
    strpos($_SERVER['SCRIPT_NAME'], '/empresa/') !== false) {
    $config_path = '../';
}
require_once $config_path . 'config/config.php';

/**
 * Generate UUID for coupons
 */
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Get featured companies
 */
function getFeaturedCompanies($conn, $limit = 10) {
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE status = 'aprovada' AND destaque = true ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get recent companies
 */
function getRecentCompanies($conn, $limit = 8) {
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE status = 'aprovada' ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get all categories
 */
function getCategories($conn) {
    $stmt = $conn->query("SELECT * FROM categorias ORDER BY nome ASC");
    return $stmt->fetchAll();
}

/**
 * Get category icon
 */
function getCategoryIcon($category) {
    $icons = [
        'Alimentação' => 'utensils',
        'Tecnologia' => 'laptop',
        'Educação' => 'graduation-cap',
        'Saúde' => 'heartbeat',
        'Beleza' => 'spa',
        'Viagem' => 'plane',
        'Esporte' => 'dumbbell',
        'Entretenimento' => 'film',
        'Compras' => 'shopping-bag',
        'Serviços' => 'tools'
    ];
    
    return $icons[$category] ?? 'star';
}

/**
 * Search companies
 */
function searchCompanies($conn, $query, $categoria = null, $cidade = null) {
    $sql = "SELECT * FROM empresas WHERE status = 'aprovada'";
    $params = [];
    
    if (!empty($query)) {
        $sql .= " AND (nome LIKE ? OR descricao LIKE ?)";
        $params[] = "%$query%";
        $params[] = "%$query%";
    }
    
    if (!empty($categoria)) {
        $sql .= " AND categoria = ?";
        $params[] = $categoria;
    }
    
    if (!empty($cidade)) {
        $sql .= " AND cidade LIKE ?";
        $params[] = "%$cidade%";
    }
    
    $sql .= " ORDER BY nome ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get company by ID
 */
function getCompanyById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ? AND status = 'aprovada'");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Generate coupon
 */
function generateCoupon($conn, $user_id, $empresa_id) {
    $codigo = generateUUID();
    
    $stmt = $conn->prepare("INSERT INTO cupons (usuario_id, empresa_id, codigo, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $empresa_id, $codigo]);
    
    return [
        'id' => $conn->lastInsertId(),
        'codigo' => $codigo
    ];
}

/**
 * Get user coupons
 */
function getUserCoupons($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT c.*, e.nome as empresa_nome, e.logo as empresa_logo 
        FROM cupons c 
        JOIN empresas e ON c.empresa_id = e.id 
        WHERE c.usuario_id = ? 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Upload file
 */
function uploadFile($file, $allowed_types = ['image/jpeg', 'image/png', 'image/gif']) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Nenhum arquivo enviado'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo 5MB)'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = UPLOAD_PATH . $filename;
    
    if (!file_exists(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Erro ao enviar arquivo'];
    }
}

/**
 * Format date for display
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Redirect function
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
