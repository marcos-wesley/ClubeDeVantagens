<?php
/**
 * Script simples para copiar imagens do Replit para servidor local
 * Coloque este arquivo no seu servidor local e execute
 */

echo "<h2>Cópia de Imagens - Replit → Servidor Local</h2>";
echo "<hr>";

// URL base do seu Replit (ALTERE AQUI)
$replit_url = 'https://seu-projeto.replit.app'; // ← ALTERE PARA SUA URL DO REPLIT
$local_dir = 'uploads/';

// Lista de todas as imagens necessárias (baseado no seu resultado)
$imagens = [
    // Logos principais
    '688b9c1595ede.jpeg',   // Magalu
    '688b9c2200a2c.webp',   // Centauro
    '688b9c30d508e.webp',   // O Boticario
    '688b9c8c66058.webp',   // NetShoes
    '688b9c9f54d3d.webp',   // Petz
    '688b9b3e9c08c.jpg',    // Hotel Vista Mar
    '688baae66117a.png',    // Salão Elegance
    '688baae662825.png',    // AutoCenter Express
    
    // Imagens de detalhes
    '688baae64fb8.png',     // Magalu detalhes
    '688baae66910.png',     // Centauro detalhes
    '688baae66dd86.png',    // O Boticario detalhes
    '688baae672481.png',    // NetShoes detalhes
    '688baae676e00.png',    // Petz detalhes
    '688baae67ba5.png',     // Salão Elegance detalhes
    '688baae68006e.png',    // AutoCenter Express detalhes
    
    // Outras imagens existentes
    '688b7a1d21a88.png',
    '688b7a831aef9.png',
    '688b7ab70647b.png',
    '688b7b182b029.png',
    '688b7b41007aa.jpg',
    '688b8d5075198.jpeg',
    '688b9b3e9bf61.png',
    '688b9b3e9c08c.jpg'
];

// Criar pasta se não existir
if (!is_dir($local_dir)) {
    if (mkdir($local_dir, 0755, true)) {
        echo "✅ Pasta $local_dir criada<br>";
    } else {
        echo "❌ Erro ao criar pasta $local_dir<br>";
        exit;
    }
}

echo "<h3>Copiando imagens...</h3>";

$sucessos = 0;
$erros = 0;

foreach ($imagens as $imagem) {
    $url = $replit_url . '/uploads/' . $imagem;
    $local_path = $local_dir . $imagem;
    
    echo "<strong>$imagem:</strong> ";
    
    // Tentar baixar a imagem
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (compatible; PHP)'
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    
    if ($content !== false && strlen($content) > 0) {
        if (file_put_contents($local_path, $content)) {
            $size = number_format(strlen($content) / 1024, 1);
            echo "✅ Copiada ($size KB)<br>";
            $sucessos++;
        } else {
            echo "❌ Erro ao salvar localmente<br>";
            $erros++;
        }
    } else {
        echo "❌ Não encontrada no Replit<br>";
        $erros++;
    }
}

echo "<hr>";
echo "<h3>Resultado:</h3>";
echo "✅ Sucessos: $sucessos<br>";
echo "❌ Erros: $erros<br>";

if ($sucessos > 0) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<strong>🎉 Imagens copiadas com sucesso!</strong><br>";
    echo "Agora acesse sua homepage para ver o carrossel funcionando.";
    echo "</div>";
    
    // Conectar ao banco e verificar
    try {
        require_once 'config/database.php';
        $stmt = $conn->query("SELECT COUNT(*) as total FROM empresas WHERE logo IS NOT NULL");
        $total_com_logo = $stmt->fetch()['total'];
        echo "<p>📊 Empresas com logo no banco: $total_com_logo</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Não foi possível verificar o banco: " . $e->getMessage() . "</p>";
    }
}

if ($erros > 0) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<strong>⚠️ Alguns arquivos não foram copiados</strong><br>";
    echo "Verifique se a URL do Replit está correta no topo deste arquivo.";
    echo "</div>";
}
?>

<div style="background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>📝 Instruções:</strong><br>
    1. Altere a URL do Replit na linha 9 deste arquivo<br>
    2. Execute este script no seu servidor local<br>
    3. Acesse a homepage para ver o resultado<br>
    4. Se alguma imagem não copiar, baixe manualmente
</div>

<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>🔧 Alternativa Manual:</strong><br>
    Se este script não funcionar, baixe o arquivo compactado:<br>
    <a href="download_imagens.php" style="background: #007cba; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;">
        📁 Baixar imagens_empresas.tar.gz
    </a>
</div>