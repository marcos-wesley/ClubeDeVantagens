<?php
/**
 * Script para download de todas as imagens das empresas
 * Use este arquivo para baixar as imagens do Replit para seu servidor local
 */

// Verificar se o arquivo tar existe
$tar_file = 'imagens_empresas.tar.gz';

if (!file_exists($tar_file)) {
    echo "❌ Arquivo $tar_file não encontrado. Execute primeiro:<br>";
    echo "<code>cd uploads && tar -czf ../imagens_empresas.tar.gz *.png *.jpg *.jpeg *.webp</code>";
    exit;
}

// Forçar download do arquivo
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $tar_file . '"');
header('Content-Length: ' . filesize($tar_file));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Enviar arquivo
readfile($tar_file);
exit;
?>