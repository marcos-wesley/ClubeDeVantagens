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

### ✅ Melhorias na Página de Detalhes da Empresa
- **Cards organizados**: Todas as seções agora usam cards Bootstrap com sombras e hover effects
- **Como Funciona**: Layout em grid 2x2 com badges numerados e descrições detalhadas
- **Regulamento**: Design melhorado com badges numerados
- **Localização**: Mapa na esquerda, informações de contato na direita
- **Sidebar modernizada**: 
  - Card principal com logo circular, nome da empresa e botão de ação destacado
  - Card de informações sobre a empresa
  - Card de contato com ícones organizados
  - Sidebar com posição sticky para melhor usabilidade

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