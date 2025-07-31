<?php
// General configuration
define('SITE_NAME', 'Clube de Vantagens ANETI');
define('SITE_URL', 'http://localhost:5000');
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// User plans
define('USER_PLANS', [
    'junior' => 'Júnior',
    'pleno' => 'Pleno',
    'senior' => 'Sênior'
]);

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Email configuration (for future use)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
?>
