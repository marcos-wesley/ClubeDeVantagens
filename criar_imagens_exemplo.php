<?php
/**
 * Script para criar imagens de exemplo no servidor local
 * Execute uma vez para gerar logos de exemplo para as empresas
 */

echo "<h2>Criador de Imagens de Exemplo - Clube ANETI</h2>";
echo "<hr>";

// Verificar se GD está instalada
if (!extension_loaded('gd')) {
    die('❌ Extensão GD não está instalada. Instale php-gd primeiro.');
}

// Criar pasta uploads se não existir
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
    echo "✅ Pasta uploads/ criada<br>";
}

// Conectar ao banco
try {
    require_once 'config/database.php';
    echo "✅ Conectado ao banco MySQL<br>";
} catch (Exception $e) {
    die("❌ Erro ao conectar banco: " . $e->getMessage());
}

// Buscar empresas sem logo
$stmt = $conn->query("SELECT id, nome, logo FROM empresas ORDER BY id");
$empresas = $stmt->fetchAll();

echo "<h3>Criando logos para empresas:</h3>";

foreach ($empresas as $empresa) {
    $nome = $empresa['nome'];
    $logo_atual = $empresa['logo'];
    
    echo "<strong>$nome:</strong> ";
    
    // Se já tem logo e arquivo existe, pular
    if ($logo_atual && file_exists($upload_dir . $logo_atual)) {
        echo "✅ Logo já existe ($logo_atual)<br>";
        continue;
    }
    
    // Criar logo simples com GD
    $width = 200;
    $height = 200;
    $image = imagecreatetruecolor($width, $height);
    
    // Cores aleatórias baseadas no nome
    $hash = md5($nome);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    
    // Cor de fundo
    $bg_color = imagecolorallocate($image, $r, $g, $b);
    imagefill($image, 0, 0, $bg_color);
    
    // Cor do texto (contraste)
    $text_color = imagecolorallocate($image, 255-$r, 255-$g, 255-$b);
    
    // Pegar iniciais do nome (máximo 3 letras)
    $palavras = explode(' ', $nome);
    $iniciais = '';
    foreach ($palavras as $palavra) {
        if (strlen($iniciais) < 3 && !empty($palavra)) {
            $iniciais .= strtoupper($palavra[0]);
        }
    }
    
    // Adicionar texto centralizado
    $font_size = 5; // Fonte built-in do GD
    $text_width = strlen($iniciais) * imagefontwidth($font_size);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $iniciais, $text_color);
    
    // Salvar imagem
    $filename = uniqid() . '.png';
    $filepath = $upload_dir . $filename;
    
    if (imagepng($image, $filepath)) {
        // Atualizar banco de dados
        $stmt = $conn->prepare("UPDATE empresas SET logo = ? WHERE id = ?");
        $stmt->execute([$filename, $empresa['id']]);
        
        echo "✅ Logo criada: $filename<br>";
    } else {
        echo "❌ Erro ao criar logo<br>";
    }
    
    imagedestroy($image);
}

echo "<hr>";
echo "<h3>Também criar imagens de detalhes:</h3>";

foreach ($empresas as $empresa) {
    $nome = $empresa['nome'];
    $id = $empresa['id'];
    
    echo "<strong>$nome:</strong> ";
    
    // Verificar se já tem imagem_detalhes
    $stmt = $conn->prepare("SELECT imagem_detalhes FROM empresas WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    $imagem_atual = $result['imagem_detalhes'];
    
    if ($imagem_atual && file_exists($upload_dir . $imagem_atual)) {
        echo "✅ Imagem de detalhes já existe<br>";
        continue;
    }
    
    // Criar imagem de banner/detalhe
    $width = 800;
    $height = 400;
    $image = imagecreatetruecolor($width, $height);
    
    // Gradiente baseado no nome
    $hash = md5($nome . 'detalhes');
    $r1 = hexdec(substr($hash, 0, 2));
    $g1 = hexdec(substr($hash, 2, 2));
    $b1 = hexdec(substr($hash, 4, 2));
    $r2 = hexdec(substr($hash, 6, 2));
    $g2 = hexdec(substr($hash, 8, 2));
    $b2 = hexdec(substr($hash, 10, 2));
    
    // Criar gradiente simples
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = $r1 + ($r2 - $r1) * $ratio;
        $g = $g1 + ($g2 - $g1) * $ratio;
        $b = $b1 + ($b2 - $b1) * $ratio;
        
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }
    
    // Adicionar texto
    $text_color = imagecolorallocate($image, 255, 255, 255);
    $font_size = 5;
    $text = strtoupper($nome);
    $text_width = strlen($text) * imagefontwidth($font_size);
    $x = ($width - $text_width) / 2;
    $y = $height / 2 - 20;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Adicionar subtítulo
    $subtitle = "Beneficios exclusivos ANETI";
    $sub_width = strlen($subtitle) * imagefontwidth(3);
    $sub_x = ($width - $sub_width) / 2;
    $sub_y = $y + 30;
    
    imagestring($image, 3, $sub_x, $sub_y, $subtitle, $text_color);
    
    // Salvar
    $filename = uniqid() . '.png';
    $filepath = $upload_dir . $filename;
    
    if (imagepng($image, $filepath)) {
        $stmt = $conn->prepare("UPDATE empresas SET imagem_detalhes = ? WHERE id = ?");
        $stmt->execute([$filename, $id]);
        
        echo "✅ Imagem de detalhes criada: $filename<br>";
    } else {
        echo "❌ Erro ao criar imagem<br>";
    }
    
    imagedestroy($image);
}

echo "<hr>";
echo "<h3>Resumo Final:</h3>";

// Verificar resultado final
$stmt = $conn->query("SELECT COUNT(*) as total FROM empresas");
$total = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as com_logo FROM empresas WHERE logo IS NOT NULL");
$com_logo = $stmt->fetch()['com_logo'];

$stmt = $conn->query("SELECT COUNT(*) as com_detalhes FROM empresas WHERE imagem_detalhes IS NOT NULL");
$com_detalhes = $stmt->fetch()['com_detalhes'];

echo "✅ Total de empresas: $total<br>";
echo "✅ Empresas com logo: $com_logo<br>";
echo "✅ Empresas with detalhes: $com_detalhes<br>";

// Listar arquivos criados
echo "<h4>Arquivos na pasta uploads:</h4>";
$files = scandir($upload_dir);
$image_files = array_filter($files, function($file) {
    return in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif', 'webp']);
});

foreach ($image_files as $file) {
    $size = filesize($upload_dir . $file);
    echo "📁 $file (" . number_format($size/1024, 1) . " KB)<br>";
}

echo "<div style='margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
echo "<strong>✅ Pronto!</strong><br>";
echo "Agora suas empresas têm logos e imagens de exemplo.<br>";
echo "Acesse a homepage para ver o carrossel funcionando!";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3, h4 { color: #012d6a; }
hr { border: 1px solid #ccc; margin: 20px 0; }
</style>