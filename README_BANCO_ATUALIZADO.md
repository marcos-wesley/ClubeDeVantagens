# ANETI Clube de Vantagens - Banco de Dados Atualizado

## Data da Exportação: 31 de Julho de 2025

### Arquivo do Banco de Dados
- **Nome do arquivo:** `aneti_clube_export_20250731_221610.sql`
- **Tamanho:** 23,507 bytes
- **Formato:** MySQL dump (compatível com MySQL 8.0+)

### Principais Atualizações Incluídas

#### ✅ Sistema de Autenticação Completo
- Campo `password` adicionado à tabela `usuarios` (hash MD5)
- Todos os usuários demo configurados com senha '123456'
- Sistema de login funcional para usuários e administradores

#### ✅ Tabelas Principais
- **empresas**: 8 empresas parceiras com logos, imagens e informações completas
- **usuarios**: 6 usuários membros com diferentes níveis (Junior, Pleno, Senior)
- **admins**: 1 administrador do sistema
- **categorias**: 12 categorias de benefícios
- **cupons**: 8 cupons de desconto gerados
- **slides_banner**: Sistema de slides do banner principal
- **avaliacoes**: Sistema de avaliações das empresas (novo)
- **membros**: Informações detalhadas dos membros

#### ✅ Melhorias Recentes
- Sistema de avaliações implementado na página de detalhes das empresas
- Suporte para imagens WebP em uploads
- Estrutura otimizada para MySQL 8.0
- Views de estatísticas para analytics do dashboard

### Como Usar

1. **Importar o banco:**
   ```bash
   mysql -u root -p < aneti_clube_export_20250731_221610.sql
   ```

2. **Configurar conexão PHP:**
   - Host: localhost
   - Banco: aneti_clube
   - Usuário: root
   - Charset: utf8mb4

3. **Credenciais de teste:**
   - **Admin:** admin@aneti.net.br / admin123
   - **Usuário:** joao.silva@email.com / 123456

### Estrutura de Pastas Necessárias
- `/uploads/` - Para logos das empresas
- `/uploads/slides/` - Para slides do banner
- Permissões de escrita necessárias para upload de arquivos

### Observações Importantes
- Banco compatível com MySQL 8.0 ou superior
- Charset: utf8mb4 (suporte completo a emojis e caracteres especiais)
- Foreign keys implementadas para integridade referencial
- Índices otimizados para performance

---
**Sistema desenvolvido para ANETI - Associação Nacional dos Engenheiros, Tecnólogos e Técnicos Industriais**