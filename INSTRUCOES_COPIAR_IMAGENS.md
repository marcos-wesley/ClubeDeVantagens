# 📁 Como Copiar as Imagens para seu Servidor Local

## Método 1: Download Automático (Recomendado)

### Passo 1: Baixar arquivo compactado
Acesse no seu navegador:
```
https://[URL-DO-REPLIT]/download_imagens.php
```

Isso baixará o arquivo `imagens_empresas.tar.gz` com todas as imagens.

### Passo 2: Extrair no servidor local
```bash
# No diretório do seu projeto local
tar -xzf imagens_empresas.tar.gz -C uploads/
```

## Método 2: Cópia Manual das Imagens

Copie estes arquivos para a pasta `uploads/` do seu servidor local:

### Logos das Empresas:
```
688b9c1595ede.jpeg  → Magalu
688b9c2200a2c.webp  → Centauro  
688b9c30d508e.webp  → O Boticario
688b9c8c66058.webp  → NetShoes
688b9c9f54d3d.webp  → Petz
688b9b3e9c08c.jpg   → Hotel Vista Mar
688baae66117a.png   → Salão Elegance
688baae662825.png   → AutoCenter Express
```

### Imagens de Detalhes:
```
688baae64fb8.png    → Magalu (detalhes)
688baae66910.png    → Centauro (detalhes)
688baae66dd86.png   → O Boticario (detalhes)
688baae672481.png   → NetShoes (detalhes)
688baae676e00.png   → Petz (detalhes)
688baae67ba5.png    → Salão Elegance (detalhes)
688baae68006e.png   → AutoCenter Express (detalhes)
```

## Método 3: URLs Diretas

Se tiver acesso ao Replit, baixe individualmente:

```bash
# Exemplo de download via wget/curl
wget https://[REPLIT-URL]/uploads/688b9c1595ede.jpeg
wget https://[REPLIT-URL]/uploads/688b9c2200a2c.webp
# ... (continuar para todas as imagens)
```

## Método 4: Script PHP de Cópia

Coloque este código em `copiar_imagens.php` no seu servidor local:

```php
<?php
$imagens = [
    '688b9c1595ede.jpeg',
    '688b9c2200a2c.webp',
    '688b9c30d508e.webp',
    '688b9c8c66058.webp',
    '688b9c9f54d3d.webp',
    '688b9b3e9c08c.jpg',
    '688baae66117a.png',
    '688baae662825.png'
];

$base_url = 'https://[SUA-URL-REPLIT]/uploads/';
$local_dir = 'uploads/';

if (!is_dir($local_dir)) {
    mkdir($local_dir, 0755, true);
}

foreach ($imagens as $imagem) {
    $url = $base_url . $imagem;
    $local_path = $local_dir . $imagem;
    
    $content = file_get_contents($url);
    if ($content !== false) {
        file_put_contents($local_path, $content);
        echo "✅ Copiada: $imagem\n";
    } else {
        echo "❌ Erro: $imagem\n";
    }
}
?>
```

## Verificação Final

Após copiar as imagens, execute:
```bash
# Acessar no navegador
http://localhost/test_upload.php
```

Deve mostrar:
- ✅ Todas as empresas com logos existentes
- ✅ Arquivos na pasta uploads com tamanhos corretos
- ✅ Homepage com carrossel funcionando

## Estrutura Final Esperada

```
seu-projeto/
├── uploads/
│   ├── 688b9c1595ede.jpeg    (6.6 KB)
│   ├── 688b9c2200a2c.webp    (4.3 KB)
│   ├── 688b9c30d508e.webp    (3.1 KB)
│   ├── 688b9c8c66058.webp    (3.3 KB)
│   ├── 688b9c9f54d3d.webp    (4.8 KB)
│   ├── 688b9b3e9c08c.jpg     (1,061 KB)
│   ├── 688baae66117a.png     (0.7 KB)
│   ├── 688baae662825.png     (0.7 KB)
│   └── [outras imagens...]
├── index.php
└── [outros arquivos...]
```

**🎯 Resultado:** Carrossel "Benefícios em Destaque" com logos coloridos funcionando perfeitamente!