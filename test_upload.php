<?php
echo "<h2>Teste de Upload - Clube de Vantagens ANETI</h2>";
echo "<hr>";

// Informações do sistema
echo "<h3>Informações do Sistema</h3>";
echo "<strong>Diretório atual:</strong> " . getcwd() . "<br>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>Upload habilitado:</strong> " . (ini_get('file_uploads') ? 'SIM' : 'NÃO') . "<br>";
echo "<strong>Upload max size:</strong> " . ini_get('upload_max_filesize') . "<br>";
echo "<strong>Post max size:</strong> " . ini_get('post_max_size') . "<br>";
echo "<strong>Memory limit:</strong> " . ini_get('memory_limit') . "<br>";
echo "<hr>";

// Verificar pastas
echo "<h3>Verificação de Pastas</h3>";
$directories = ['uploads', 'uploads/slides', 'admin/uploads'];

foreach ($directories as $dir) {
    echo "<strong>$dir:</strong> ";
    if (is_dir($dir)) {
        echo "✅ Existe | ";
        echo "Permissão: " . substr(sprintf('%o', fileperms($dir)), -4) . " | ";
        echo "Gravável: " . (is_writable($dir) ? '✅ SIM' : '❌ NÃO');
        
        // Listar arquivos (primeiros 5)
        $files = array_slice(scandir($dir), 2, 5);
        if (!empty($files)) {
            echo " | Arquivos: " . implode(', ', $files);
        }
    } else {
        echo "❌ Não existe";
        // Tentar criar
        if (mkdir($dir, 0755, true)) {
            echo " | ✅ Criada com sucesso";
        } else {
            echo " | ❌ Erro ao criar";
        }
    }
    echo "<br>";
}
echo "<hr>";

// Teste de upload
if ($_POST && isset($_FILES['teste'])) {
    echo "<h3>Resultado do Upload</h3>";
    
    $file = $_FILES['teste'];
    echo "<strong>Nome original:</strong> " . $file['name'] . "<br>";
    echo "<strong>Tipo:</strong> " . $file['type'] . "<br>";
    echo "<strong>Tamanho:</strong> " . number_format($file['size'] / 1024, 2) . " KB<br>";
    echo "<strong>Erro:</strong> " . $file['error'] . "<br>";
    echo "<strong>Arquivo temporário:</strong> " . $file['tmp_name'] . "<br>";
    echo "<strong>Tmp file exists:</strong> " . (file_exists($file['tmp_name']) ? 'SIM' : 'NÃO') . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $target_dir = 'uploads/';
        $target_file = $target_dir . 'teste_' . date('Y-m-d_H-i-s') . '_' . basename($file['name']);
        
        echo "<strong>Destino:</strong> " . $target_file . "<br>";
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "<div style='color: green; font-weight: bold;'>✅ Upload realizado com sucesso!</div>";
            echo "<strong>Arquivo salvo em:</strong> " . realpath($target_file) . "<br>";
            echo "<strong>URL de acesso:</strong> <a href='$target_file' target='_blank'>$target_file</a><br>";
            
            // Verificar se é imagem e mostrar prévia
            if (getimagesize($target_file)) {
                echo "<br><img src='$target_file' style='max-width: 200px; border: 1px solid #ccc;'><br>";
            }
        } else {
            echo "<div style='color: red; font-weight: bold;'>❌ Erro no upload</div>";
            echo "<strong>Último erro:</strong> " . (error_get_last()['message'] ?? 'Nenhum erro específico') . "<br>";
            
            // Diagnósticos adicionais
            echo "<strong>Destino gravável:</strong> " . (is_writable($target_dir) ? 'SIM' : 'NÃO') . "<br>";
            echo "<strong>Espaço livre:</strong> " . number_format(disk_free_space($target_dir) / 1024 / 1024, 2) . " MB<br>";
        }
    } else {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Arquivo maior que upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'Arquivo maior que MAX_FILE_SIZE do formulário',
            UPLOAD_ERR_PARTIAL => 'Upload incompleto',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco',
            UPLOAD_ERR_EXTENSION => 'Upload parado por extensão PHP'
        ];
        echo "<div style='color: red;'>❌ " . ($errors[$file['error']] ?? 'Erro desconhecido') . "</div>";
    }
    echo "<hr>";
}

// Teste do banco de dados
echo "<h3>Teste de Conexão com Banco</h3>";
try {
    require_once 'config/database.php';
    echo "✅ Conexão com MySQL bem-sucedida<br>";
    
    // Testar uma consulta simples
    $stmt = $conn->query("SELECT COUNT(*) as total FROM empresas");
    $result = $stmt->fetch();
    echo "✅ Total de empresas: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}
echo "<hr>";
?>

<h3>Formulário de Teste</h3>
<form method="post" enctype="multipart/form-data" style="border: 1px solid #ccc; padding: 20px; background: #f9f9f9;">
    <div style="margin-bottom: 10px;">
        <label><strong>Selecione uma imagem para testar:</strong></label><br>
        <input type="file" name="teste" accept="image/*" required style="margin: 10px 0;">
    </div>
    <button type="submit" style="background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer;">
        🧪 Testar Upload
    </button>
</form>

<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196F3;">
    <strong>💡 Como usar:</strong><br>
    1. Coloque este arquivo na raiz do seu projeto<br>
    2. Acesse: <code>http://localhost/test_upload.php</code><br>
    3. Faça upload de uma imagem<br>
    4. Verifique se funciona antes de testar o painel admin
</div>

<div style="margin-top: 10px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
    <strong>⚠️ Problemas comuns:</strong><br>
    • Pasta uploads sem permissão de escrita<br>
    • PHP com file_uploads = Off<br> 
    • Limite de upload muito baixo<br>
    • Caminho relativo incorreto no código
</div>