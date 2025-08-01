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
    $stmt = $conn->prepare("
        SELECT e.*, 
               COUNT(a.id) as total_avaliacoes, 
               COALESCE(AVG(a.rating), 0) as media_avaliacoes 
        FROM empresas e 
        LEFT JOIN avaliacoes a ON e.id = a.empresa_id 
        WHERE e.status = 'aprovada' AND e.destaque = 1 
        GROUP BY e.id 
        ORDER BY e.created_at DESC 
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get recent companies
 */
function getRecentCompanies($conn, $limit = 8) {
    $stmt = $conn->prepare("
        SELECT e.*, 
               COUNT(a.id) as total_avaliacoes, 
               COALESCE(AVG(a.rating), 0) as media_avaliacoes 
        FROM empresas e 
        LEFT JOIN avaliacoes a ON e.id = a.empresa_id 
        WHERE e.status = 'aprovada' 
        GROUP BY e.id 
        ORDER BY e.created_at DESC 
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
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
 * Sanitize input data
 */
function sanitizeInput($input) {
    if ($input === null) {
        return '';
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Register member API access
 */
function registrarAcessoMembro($user_id, $nome, $email, $plano) {
    global $conn;
    
    try {
        // Check if record already exists for this user_id
        $stmt = $conn->prepare("SELECT id, total_acessos FROM membros_api_access WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update last access and increment counter
            $stmt = $conn->prepare("
                UPDATE membros_api_access 
                SET ultimo_acesso = NOW(), 
                    total_acessos = total_acessos + 1,
                    nome = ?,
                    email = ?,
                    plano = ?
                WHERE user_id = ?
            ");
            $stmt->execute([$nome, $email, $plano, $user_id]);
        } else {
            // Create new record
            $stmt = $conn->prepare("
                INSERT INTO membros_api_access 
                (user_id, nome, email, plano, primeiro_acesso, ultimo_acesso, total_acessos) 
                VALUES (?, ?, ?, ?, NOW(), NOW(), 1)
            ");
            $stmt->execute([$user_id, $nome, $email, $plano]);
        }
    } catch (Exception $e) {
        // Log error silently - don't break login process
        error_log("Erro ao registrar acesso do membro: " . $e->getMessage());
    }
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
 * Get active banner slides
 */
function getBannerSlides($conn, $mobile_only = false) {
    try {
        if ($mobile_only) {
            $stmt = $conn->prepare("SELECT * FROM slides_banner WHERE status = 'ativo' AND mobile_only = 1 ORDER BY ordem ASC");
        } else {
            $stmt = $conn->prepare("SELECT * FROM slides_banner WHERE status = 'ativo' AND (mobile_only = 0 OR mobile_only IS NULL) ORDER BY ordem ASC");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Detect if current request is from mobile device
 */
function isMobileDevice() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * Get all banner slides (admin)
 */
function getAllBannerSlides($conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM slides_banner ORDER BY ordem ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Add banner slide
 */
function addBannerSlide($conn, $imagem, $ordem, $status = 'ativo', $mobile_only = false) {
    try {
        $stmt = $conn->prepare("INSERT INTO slides_banner (imagem, ordem, status, mobile_only) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$imagem, $ordem, $status, $mobile_only ? 1 : 0]);
        
        // Debug: log any errors
        if (!$result) {
            error_log("Error adding banner slide: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Exception adding banner slide: " . $e->getMessage());
        return false;
    }
}

/**
 * Update banner slide
 */
function updateBannerSlide($conn, $id, $imagem, $ordem, $status, $mobile_only = false) {
    try {
        $stmt = $conn->prepare("UPDATE slides_banner SET imagem = ?, ordem = ?, status = ?, mobile_only = ?, data_atualizacao = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$imagem, $ordem, $status, $mobile_only ? 1 : 0, $id]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Delete banner slide
 */
function deleteBannerSlide($conn, $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM slides_banner WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Toggle slide status
 */
function toggleSlideStatus($conn, $id) {
    try {
        $stmt = $conn->prepare("UPDATE slides_banner SET status = CASE WHEN status = 'ativo' THEN 'inativo' ELSE 'ativo' END WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return false;
    }
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
 * Check if user is logged in (updated for WordPress API integration)
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && 
           isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
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
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
