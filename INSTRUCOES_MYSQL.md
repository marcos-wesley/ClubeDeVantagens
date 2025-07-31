# Instruções para Usar o Banco MySQL - ANETI Clube de Vantagens

## Arquivos Disponíveis
- **aneti_clube_complete.sql**: Dump completo do MySQL com estrutura e dados
- **aneti_clube_mysql.tar.gz**: Pacote completo com banco + arquivos + instruções
- **Tipo**: Dump completo do MySQL com estrutura e dados
- **Encoding**: UTF-8 (utf8mb4)
- **Tamanho**: ~21KB (349 linhas)

## Como Importar o Banco

### 1. Usando MySQL Command Line
```bash
mysql -u seu_usuario -p < aneti_clube_complete.sql
```

### 2. Usando phpMyAdmin
1. Acesse o phpMyAdmin
2. Crie um banco de dados chamado `aneti_clube`
3. Selecione o banco criado
4. Vá na aba "Importar"
5. Escolha o arquivo `aneti_clube_complete.sql`
6. Clique em "Executar"

### 3. Usando MySQL Workbench
1. Abra o MySQL Workbench
2. Conecte-se ao servidor MySQL
3. Vá em "Server" > "Data Import"
4. Selecione "Import from Self-Contained File"
5. Escolha o arquivo `aneti_clube_complete.sql`
6. Clique em "Start Import"

## Estrutura do Banco

### Tabelas Principais:
- **empresas** - Dados das empresas parceiras
- **usuarios** - Usuários do sistema (membros)
- **categorias** - Categorias de benefícios
- **cupons** - Cupons de desconto gerados
- **admins** - Administradores do sistema
- **membros** - Informações dos membros ANETI
- **slides_banner** - Slides do banner principal

### Dados Incluídos:
- ✅ 8 empresas cadastradas (algumas com logos WebP)
- ✅ 6 usuários de exemplo
- ✅ 12 categorias completas
- ✅ 8 cupons de exemplo
- ✅ 1 administrador (admin@aneti.net.br / senha: admin123)
- ✅ 6 membros ANETI
- ✅ 1 slide de banner ativo

## Configuração de Conexão

### PHP (arquivo config/database.php)
```php
$host = 'localhost';
$dbname = 'aneti_clube';
$username = 'seu_usuario';
$password = 'sua_senha';
$charset = 'utf8mb4';
```

### Para conexão local (socket):
```php
$socket = '/tmp/mysql.sock'; // ou caminho do seu socket
```

## Suporte a WebP
O sistema está configurado para aceitar imagens WebP em todos os uploads:
- Logos de empresas
- Imagens de detalhes
- Slides do banner

## Requisitos Mínimos
- MySQL 8.0+ (recomendado)
- PHP 7.4+
- Suporte a WebP no PHP (extensão GD)

## Acesso Administrativo
- **URL**: /admin/
- **Email**: admin@aneti.net.br
- **Senha**: admin123

## Observações Importantes
1. O banco já vem com dados de exemplo prontos para uso
2. Todas as senhas estão em hash MD5 (para produção, use bcrypt)
3. As imagens de exemplo estão na pasta `/uploads/`
4. O sistema suporta WebP, JPEG, PNG e GIF
5. Tamanhos máximos: 5MB para logos, 10MB para slides

---
**Sistema ANETI Clube de Vantagens**  
Desenvolvido em PHP puro com MySQL