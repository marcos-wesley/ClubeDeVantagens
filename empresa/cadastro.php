<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seo.php';

$success = '';
$error = '';

if ($_POST) {
    // Process form submission here (existing logic)
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php 
    $seo_config = [
        'title' => 'Seja um Parceiro | Clube de Vantagens ANETI',
        'description' => 'Cadastre sua empresa como parceira do Clube de Vantagens ANETI e alcance mais de 1.800 profissionais de TI qualificados.',
        'keywords' => 'parceiro ANETI, cadastro empresa, clube vantagens parceiro, rede parceiros ANETI',
        'canonical' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . 
                      $_SERVER['HTTP_HOST'] . '/empresa/cadastro.php',
        'type' => 'website'
    ];
    
    renderSEO($seo_config);
    ?>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Rest of the page content -->
</body>
</html>