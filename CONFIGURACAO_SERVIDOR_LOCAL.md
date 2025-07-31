# Configuração do Servidor Local - Clube de Vantagens ANETI

## Problema: Imagens não funcionam no servidor local

### 1. Permissões da Pasta uploads

**No Linux/Mac:**
```bash
chmod 755 uploads/
chmod 755 uploads/slides/
chmod 644 uploads/*
```

**No Windows:**
- Clique direito na pasta `uploads`
- Propriedades → Segurança
- Dar permissão total ao usuário do servidor web

### 2. Verificar se a pasta uploads existe

Certifique-se que existem as pastas:
```
/uploads/
/uploads/slides/
/admin/uploads/
```

### 3. Configuração do PHP (php.ini)

Verificar se estão habilitados:
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

### 4. Testar Upload Manualmente

Crie um arquivo `test_upload.php` na raiz:

```php
<?php
echo "Diretório atual: " . getcwd() . "<br>";
echo "Upload dir exists: " . (is_dir('uploads') ? 'SIM' : 'NÃO') . "<br>";
echo "Upload dir writable: " . (is_writable('uploads') ? 'SIM' : 'NÃO') . "<br>";
echo "Permissões upload: " . substr(sprintf('%o', fileperms('uploads')), -4) . "<br>";

if ($_POST && isset($_FILES['teste'])) {
    $target = 'uploads/' . basename($_FILES['teste']['name']);
    if (move_uploaded_file($_FILES['teste']['tmp_name'], $target)) {
        echo "Upload funcionou: " . $target;
    } else {
        echo "Erro no upload: " . error_get_last()['message'];
    }
}
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="teste" accept="image/*">
    <button type="submit">Testar Upload</button>
</form>
```

### 5. Verificar Logs de Erro

**Apache:** `tail -f /var/log/apache2/error.log`
**PHP:** Verificar onde está o log de erro do PHP

### 6. Soluções Comuns

#### A. Caminho da pasta uploads está errado
Verificar se no código está:
```php
$upload_dir = '../uploads/'; // Para arquivos em /admin/
$upload_dir = 'uploads/';   // Para arquivos na raiz
```

#### B. Servidor não consegue criar a pasta
Adicionar no código:
```php
$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}
```

#### C. Problema de segurança do servidor
Adicionar no .htaccess da pasta uploads:
```apache
<Files "*">
    Order Allow,Deny
    Allow from all
</Files>
```

### 7. Verificação Final

1. Acesse: `http://localhost/test_upload.php`
2. Faça upload de uma imagem
3. Verifique se aparece na pasta uploads
4. Teste pelo painel administrativo

### 8. Estrutura de Pastas Esperada

```
projeto/
├── admin/
│   ├── uploads/ (para uploads do admin)
│   └── empresas.php
├── uploads/ (pasta principal de uploads)
│   ├── slides/
│   └── [arquivos de imagem]
├── assets/
├── config/
└── index.php
```

### 9. Solução de Emergência

Se nada funcionar, substitua o código de upload por:

```php
// No admin/empresa-cadastro.php, linha ~48
$upload_dir = __DIR__ . '/../uploads/';
$web_path = '../uploads/';

// Usar __DIR__ para caminho absoluto
if (!move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename)) {
    error_log("Erro upload: " . $upload_dir . $logo_filename);
    $error = 'Erro ao fazer upload. Verifique permissões.';
}
```

### 10. Debug Avançado

Adicione no início do arquivo de upload:
```php
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));
error_log("Upload dir: " . $upload_dir);
error_log("Dir exists: " . (is_dir($upload_dir) ? 'yes' : 'no'));
error_log("Dir writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));
```

Depois verificar o log de erro do PHP para ver as mensagens.