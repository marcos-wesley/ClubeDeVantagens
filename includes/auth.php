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
    
    if ($user && md5($password) === $user['password']) {
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
    // Simple admin authentication
    // In production, implement proper password hashing
    
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? AND password = ? AND ativo = true");
    $stmt->execute([$email, md5($password)]); // Using MD5 for simplicity, use bcrypt in production
    $admin = $stmt->fetch();
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nome'] = $admin['nome'];
        $_SESSION['admin_email'] = $admin['email'];
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
?>
