<?php
/**
 * Diagnóstico específico para WampServer - Imagens não aparecem
 */

echo "<h2>🔍 Diagnóstico WampServer - Imagens</h2>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script atual:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<hr>";

// 1. Verificar estrutura de pastas
echo "<h3>1. Estrutura de Pastas</h3>";
$current_dir = getcwd();
echo "<strong>Diretório atual:</strong> $current_dir<br>";

$folders_to_check = [
    'uploads',
    'uploads/slides',
    'assets',
    'assets/images'
];

foreach ($folders_to_check as $folder) {
    $full_path = $current_dir . '/' . $folder;
    echo "<strong>$folder:</strong> ";
    
    if (is_dir($full_path)) {
        echo "✅ Existe";
        $perms = substr(sprintf('%o', fileperms($full_path)), -4);
        echo " (Permissões: $perms)";
        
        // Contar arquivos
        $files = glob($full_path . '/*');
        $count = count($files);
        echo " - $count arquivos";
    } else {
        echo "❌ Não existe";
    }
    echo "<br>";
}

echo "<hr>";

// 2. Verificar imagens específicas
echo "<h3>2. Verificação de Imagens das Empresas</h3>";

$imagens_teste = [
    '688b9c1595ede.jpeg',
    '688b9c2200a2c.webp', 
    '688b9c30d508e.webp',
    '688b9c8c66058.webp',
    '688b9c9f54d3d.webp',
    '688b9b3e9c08c.jpg',
    '688b9b3e9bf61.png'
];

foreach ($imagens_teste as $img) {
    $path = "uploads/$img";
    echo "<strong>$img:</strong> ";
    
    if (file_exists($path)) {
        $size = filesize($path);
        $readable = is_readable($path);
        echo "✅ Existe (" . number_format($size/1024, 1) . " KB)";
        echo " - Legível: " . ($readable ? "Sim" : "Não");
        
        // Verificar tipo MIME
        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($path);
            echo " - MIME: $mime";
        }
    } else {
        echo "❌ Não encontrada";
    }
    echo "<br>";
}

echo "<hr>";

// 3. Teste de acesso HTTP direto
echo "<h3>3. Teste de Acesso HTTP</h3>";
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

foreach (array_slice($imagens_teste, 0, 3) as $img) {
    $url = $base_url . "/uploads/$img";
    echo "<strong>$img:</strong> ";
    echo "<a href='$url' target='_blank'>$url</a> ";
    
    // Tentar acessar via HTTP
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "✅ Acessível via HTTP";
    } else {
        echo "❌ Não acessível via HTTP";
        if ($headers) {
            echo " (" . $headers[0] . ")";
        }
    }
    echo "<br>";
}

echo "<hr>";

// 4. Verificar configurações PHP
echo "<h3>4. Configurações PHP/WampServer</h3>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Apache Version:</strong> " . (function_exists('apache_get_version') ? apache_get_version() : 'N/A') . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Script Filename:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "<strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";

// Verificar .htaccess
$htaccess = '.htaccess';
if (file_exists($htaccess)) {
    echo "<strong>.htaccess:</strong> ✅ Existe<br>";
    $content = file_get_contents($htaccess);
    if (strpos($content, 'RewriteEngine') !== false) {
        echo "<strong>URL Rewrite:</strong> ⚠️ Ativo (pode interferir)<br>";
    }
} else {
    echo "<strong>.htaccess:</strong> ❌ Não existe<br>";
}

echo "<hr>";

// 5. Teste de carregamento de imagem inline
echo "<h3>5. Teste Visual de Imagens</h3>";
foreach (array_slice($imagens_teste, 0, 3) as $img) {
    if (file_exists("uploads/$img")) {
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>$img:</strong><br>";
        echo "<img src='uploads/$img' style='max-width: 100px; max-height: 100px; border: 1px solid red;' ";
        echo "onerror=\"this.style.display='none'; this.nextSibling.style.display='block';\" />";
        echo "<div style='display:none; color:red; font-weight:bold;'>❌ Imagem não carregou</div>";
        echo "</div>";
    }
}

echo "<hr>";

// 6. Verificar banco de dados
echo "<h3>6. Verificação do Banco</h3>";
try {
    require_once 'config/database.php';
    echo "✅ Conexão com banco OK<br>";
    
    $stmt = $conn->query("SELECT id, nome, logo FROM empresas WHERE logo IS NOT NULL LIMIT 5");
    $empresas = $stmt->fetchAll();
    
    echo "<strong>Empresas no banco:</strong><br>";
    foreach ($empresas as $emp) {
        $exists = file_exists("uploads/" . $emp['logo']) ? "✅" : "❌";
        echo "• {$emp['nome']} → {$emp['logo']} $exists<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro no banco: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 7. Soluções específicas para WampServer
echo "<h3>🛠️ Soluções Específicas para WampServer</h3>";
echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3;'>";
echo "<strong>Problemas comuns no WampServer:</strong><br><br>";

echo "<strong>1. Alias não configurado:</strong><br>";
echo "• Vá em WampServer → Apache → Alias directories<br>";
echo "• Adicione alias para pasta uploads se necessário<br><br>";

echo "<strong>2. Virtual Host mal configurado:</strong><br>";
echo "• Verifique se DocumentRoot está correto<br>";
echo "• Teste acessar: <code>http://localhost/[seu-projeto]/uploads/</code><br><br>";

echo "<strong>3. Permissões de pasta:</strong><br>";
echo "• Clique direito na pasta uploads → Propriedades → Segurança<br>";
echo "• Dar permissão total para 'Todos'<br><br>";

echo "<strong>4. Módulo rewrite interferindo:</strong><br>";
echo "• Desabilite mod_rewrite temporariamente<br>";
echo "• Ou adicione regras no .htaccess para permitir imagens<br><br>";

echo "<strong>5. Cache do navegador:</strong><br>";
echo "• Pressione Ctrl+F5 para forçar atualização<br>";
echo "• Ou abra em aba anônima/privada<br>";
echo "</div>";

// 8. Teste de criação de imagem
echo "<hr>";
echo "<h3>8. Teste de Criação de Imagem</h3>";
if (extension_loaded('gd')) {
    $test_img = 'uploads/teste_wamp.png';
    
    // Criar imagem teste
    $img = imagecreatetruecolor(100, 50);
    $bg = imagecolorallocate($img, 0, 100, 200);
    $text = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $bg);
    imagestring($img, 5, 10, 15, 'TESTE', $text);
    
    if (imagepng($img, $test_img)) {
        echo "✅ Imagem teste criada: $test_img<br>";
        echo "<img src='$test_img' style='border: 1px solid green;'><br>";
        echo "<a href='$test_img' target='_blank'>Abrir imagem teste</a><br>";
    } else {
        echo "❌ Erro ao criar imagem teste<br>";
    }
    
    imagedestroy($img);
} else {
    echo "❌ Extensão GD não disponível<br>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h2, h3 { color: #012d6a; }
    hr { border: 1px solid #ddd; margin: 20px 0; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
</style>