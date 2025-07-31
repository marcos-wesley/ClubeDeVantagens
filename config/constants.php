<?php
// Configurações gerais do site
define('SITE_NAME', 'Clube de Benefícios ANETI');
define('SITE_URL', 'http://localhost:5000');
define('ADMIN_EMAIL', 'admin@aneti.org.br');

// Configurações de upload
define('UPLOAD_PATH', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Status das empresas
define('STATUS_ATIVA', 'ativa');
define('STATUS_PENDENTE', 'pendente');
define('STATUS_INATIVA', 'inativa');

// Configurações de paginação
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);
?>