# 🔧 Solução para Imagens Quebradas no Servidor Local

## Problema Identificado
As imagens do carrossel "Benefícios em Destaque" aparecem como ícones quebrados porque:
1. Os arquivos de imagem não existem na pasta `uploads/`
2. As permissões da pasta não permitem acesso
3. O caminho das imagens está incorreto

## ✅ Solução Rápida (Recomendada)

### 1. Execute o Script de Criação de Imagens
```bash
# Acesse no navegador:
http://localhost/criar_imagens_exemplo.php
```

Este script irá:
- ✅ Criar logos automaticamente para todas as empresas  
- ✅ Gerar imagens de detalhes com gradientes
- ✅ Atualizar o banco com os nomes dos arquivos
- ✅ Verificar permissões e criar pastas necessárias

### 2. Execute o Script de Diagnóstico
```bash
# Acesse no navegador:  
http://localhost/test_upload.php
```

Este script mostra:
- Status das pastas uploads
- Permissões dos diretórios
- Quais empresas têm/não têm imagens
- Teste de upload funcional

## 🛠️ Solução Manual

### 1. Verificar/Criar Pastas
```bash
# Criar pastas necessárias
mkdir uploads
mkdir uploads/slides
chmod 755 uploads
chmod 755 uploads/slides
```

### 2. Copiar Imagens do Replit
Se você tem acesso ao Replit, copie os arquivos:
```
uploads/688b9c1595ede.jpeg  (Magalu)
uploads/688b9c2200a2c.webp  (Centauro) 
uploads/688b9c30d508e.webp  (O Boticario)
uploads/688b9c8c66058.webp  (NetShoes)
uploads/688b9c9f54d3d.webp  (Petz)
uploads/688b9b3e9c08c.jpg   (Hotel Vista Mar)
uploads/688b9b3e9bf61.png   (TechStore)
```

### 3. Verificar Banco de Dados
```sql
-- Ver empresas com logos
SELECT id, nome, logo FROM empresas WHERE logo IS NOT NULL;

-- Atualizar empresa sem logo (exemplo)
UPDATE empresas SET logo = 'novo_logo.png' WHERE id = 1;
```

## 🚨 Problemas Comuns e Soluções

### Erro: "Pasta uploads não gravável"
```bash
# Linux/Mac
chmod 755 uploads/
chown www-data:www-data uploads/

# Windows (via propriedades da pasta)
# Dar permissão total ao usuário do servidor web
```

### Erro: "GD extension não encontrada"
```bash
# Ubuntu/Debian
sudo apt install php-gd

# CentOS/RHEL  
sudo yum install php-gd

# Windows XAMPP
# Descommentar extension=gd no php.ini
```

### Erro: "Arquivo muito grande"
```ini
# No php.ini
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
```

## 📋 Checklist de Verificação

- [ ] Pasta `uploads/` existe e tem permissão 755
- [ ] Arquivos de imagem existem na pasta uploads
- [ ] Banco de dados tem os nomes dos arquivos corretos
- [ ] PHP tem extensão GD habilitada
- [ ] Configurações de upload estão adequadas
- [ ] Homepage carrega as imagens corretamente

## 🎯 Resultado Esperado

Após seguir os passos:
- ✅ Carrossel "Benefícios em Destaque" com logos das empresas
- ✅ Seção "Adicionados recentemente" com imagens de capa
- ✅ Páginas de detalhes das empresas com imagens
- ✅ Upload de novas imagens pelo painel admin funcionando

## 📞 Suporte Adicional

Se ainda houver problemas:
1. Execute `test_upload.php` e anote os erros
2. Verifique o log de erro do Apache/PHP
3. Teste upload manual de uma imagem pequena
4. Confirme que o banco está conectado corretamente

---
**💡 Dica:** O script `criar_imagens_exemplo.php` é a solução mais rápida - ele cria logos coloridos automaticamente usando as iniciais dos nomes das empresas!