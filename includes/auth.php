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
    
    // Debug - log tentativa de login
    error_log("Login attempt: email=$email, password_hash=" . md5($password));
    if ($user) {
        error_log("User found: " . json_encode($user));
        error_log("Password field exists: " . (isset($user['password']) ? 'YES' : 'NO'));
        if (isset($user['password'])) {
            error_log("Stored password hash: " . $user['password']);
            error_log("Password match: " . (md5($password) === $user['password'] ? 'YES' : 'NO'));
        }
    } else {
        error_log("User not found for email: $email");
    }
    
    if ($user && isset($user['password']) && md5($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_plano'] = $user['plano'];
        $_SESSION['login_time'] = time();
        error_log("Login successful for user: " . $user['nome']);
        return true;
    }
    
    error_log("Login failed - password mismatch or user not found");
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
