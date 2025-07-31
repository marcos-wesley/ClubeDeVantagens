# ANETI Clube de Vantagens - Documentação do Projeto

## Visão Geral
Plataforma completa de benefícios para membros da ANETI, desenvolvida em PHP com PostgreSQL. O sistema oferece uma experiência digital moderna para descobrir e acessar descontos de empresas parceiras.

## Tecnologias Principais
- **Backend**: PHP 8.4 com servidor integrado
- **Database**: PostgreSQL 
- **Frontend**: Bootstrap 5, Font Awesome, CSS customizado
- **Mapas**: Leaflet (OpenStreetMap) para localização
- **Arquitetura**: MVC simplificado

## Estrutura do Projeto
```
/
├── admin/              # Painel administrativo
├── assets/            # CSS, JS, imagens
├── config/            # Configurações do banco
├── includes/          # Funções e componentes reutilizáveis  
├── public/            # Páginas públicas
├── uploads/           # Arquivos enviados pelos usuários
└── replit.md         # Esta documentação
```

## Funcionalidades Principais
- Sistema de autenticação de usuários
- Catálogo de empresas parceiras com categorias
- Geração de cupons de desconto
- Sistema de avaliações e reviews
- Mapa interativo com localização das empresas
- Painel administrativo completo
- Design responsivo e moderno

## Mudanças Recentes (31/07/2025)

### ✅ Reformulação Completa da Página de Detalhes (Seguindo Modelo de Referência)
- **Layout duas colunas profissional**: Conteúdo principal (col-lg-8) e sidebar (col-lg-4) seguindo exatamente o modelo enviado
- **Sistema de Cards Bootstrap**: Todas as seções organizadas em cards com sombras e hover effects
  - Card da imagem principal com bordas arredondadas
  - Card "Como Funciona" com header colorido e badges numerados
  - Card "Regulamento" com design similar e consistente
  - Card "Localização" com mapa integrado
- **Botão "USAR/LOGIN" destacado**: Botão rosa/vermelho (gradiente #e91e63 para #f06292) na sidebar
- **Sidebar Cards organizados**:
  - Card do logo da empresa circular (80px) com nome e categoria
  - Badge de desconto quando disponível
  - Card de descrição da empresa separado
  - Posição sticky para melhor UX
- **Badges numerados**: Sistema visual limpo para passos e regulamentos
- **Hover effects**: Transições suaves em cards e botões

### ✅ Sistema de Avaliações Aprimorado
- **Resumo visual**: Círculo com nota média e breakdown de estrelas com barras de progresso
- **Lista de reviews**: Design em cards com avatars circulares e layout melhorado
- **Formulário interativo**: Sistema de estrelas clicável com hover effects
- **Alertas modernos**: Notificações com ícones e dismiss buttons

### ✅ Melhorias de Interface
- **Botões de ação**: Redesign com Bootstrap buttons e texto responsivo
- **CSS customizado**: Adicionados estilos para hover effects, transições e responsividade
- **Sistema de rating**: JavaScript interativo para seleção de estrelas
- **Cards com animações**: Hover effects e transições suaves

### ✅ Funcionalidades JavaScript
- Rating system interativo no formulário de avaliação
- Hover effects nas estrelas com feedback visual
- Manutenção do estado selecionado nas avaliações
- Integração com mapas OpenStreetMap (Leaflet)

## Arquitetura de Design
- **Cores principais**: Purple/Violet (#8B5CF6) como cor primária da ANETI
- **Layout**: Cards com sombras sutis e bordas arredondadas
- **Tipografia**: Bootstrap 5 com Font Awesome icons
- **Responsividade**: Mobile-first design com breakpoints otimizados
- **UX**: Sticky sidebar, hover effects, transições suaves

## Banco de Dados
- **Empresas**: Informações completas, logos, categorias
- **Usuários**: Sistema de autenticação
- **Avaliações**: Reviews com rating de 1-5 estrelas
- **Cupons**: Sistema de geração e controle de uso

## Próximas Melhorias Sugeridas
- [ ] Sistema de notificações push
- [ ] Filtros avançados na busca
- [ ] Histórico de cupons utilizados
- [ ] Dashboard de analytics para empresas
- [ ] API REST para mobile app

## Preferências do Usuário
- Interface visual moderna e intuitiva
- Uso de Bootstrap e componentes visuais organizados
- Cards e seções bem definidas com boa separação visual
- Design responsivo e acessível
- Cores consistentes com a identidade ANETI

## Configuração do Ambiente
- PHP Server rodando na porta 5000
- PostgreSQL database configurado
- Arquivos de upload organizados por data
- Leaflet maps para funcionalidade de localização