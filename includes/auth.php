<?php
/**
 * Authentication functions
 */

/**
 * Login user with email and password
 */
function loginUser($conn, $email, $password) {
    // Authenticate user with email and password
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = true");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    

    
    if ($user && isset($user['password']) && md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_plano'] = $user['plano'];
        $_SESSION['login_time'] = time();

        return true;
    }
    

    return false;
}

/**
 * Login admin
 */
function loginAdmin($conn, $email, $password) {
    // Check if admin exists and is active
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? AND status = 'ativo'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['senha'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_nivel'] = $admin['nivel'];
        $_SESSION['admin_login_time'] = time();
        return true;
    }
    
    return false;
}

/**
 * Logout user
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            logoutUser();
            return false;
        }
        $_SESSION['login_time'] = time(); // Update last activity
    }
    return true;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        redirect('../public/login.php');
    }
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn() || !checkSessionTimeout()) {
        redirect('login.php');
    }
}

/**
 * Check admin permission level
 */
function hasAdminPermission($required_level) {
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    $current_level = $_SESSION['admin_nivel'] ?? 'editor';
    
    // Hierarchy: editor < admin < super
    $levels = ['editor' => 1, 'admin' => 2, 'super' => 3];
    
    return ($levels[$current_level] ?? 0) >= ($levels[$required_level] ?? 0);
}

/**
 * Require specific admin level
 */
function requireAdminLevel($required_level) {
    if (!hasAdminPermission($required_level)) {
        redirect('index.php?error=access_denied');
    }
}
?>
