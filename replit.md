# ANETI Clube de Vantagens - Sistema Web

## Overview

This is a comprehensive web system for ANETI's Benefits Club (Clube de Vantagens da ANETI) built with pure PHP, HTML, CSS, and Bootstrap. The system provides a complete membership benefits platform with advanced administrative management, partner company discount access, coupon generation, and detailed analytics dashboard.

## Recent Changes (July 31, 2025)

### Authentication System Fixes & UI Improvements
- **Login/Logout Fixed**: Header agora verifica sessão e mostra botão correto (Entrar/Sair)
- **User Dropdown**: Menu dropdown com nome do usuário, link para dashboard e logout
- **Session Management**: Sistema de sessão corrigido para persistir login entre páginas
- **Logout Functionality**: Arquivo logout.php criado para destruir sessão adequadamente
- **Dashboard Layout**: Padding-top corrigido para compensar header fixo (160px)
- **Gerar Cupom Layout**: Página de gerar cupom corrigida com estilos ANETI adequados

### Company Details Page Redesign & Review System 
- **Complete Redesign**: Página empresa-detalhes.php totalmente redesenhada com layout moderno
- **Hero Section**: Gradiente ANETI (azul → verde) com logo destacado e informações organizadas
- **Tab System**: Sistema de tabs profissional (Informações/Avaliações) com navegação suave
- **Review System**: Sistema completo de avaliações com tabela 'avaliacoes' no MySQL
- **Sidebar Layout**: Layout flexbox implementado para posicionar sidebar à direita
- **Responsive Design**: Layout totalmente responsivo com ANETI branding consistente

### Database Migration: PostgreSQL → MySQL
- **Complete Migration**: Sistema migrado de PostgreSQL para MySQL 8.0
- **MySQL Server**: Configurado para rodar na porta 3306 com socket local
- **Schema Updated**: Schema MySQL aplicado com todas as tabelas e dados
- **Connection Fixed**: Conexão PHP atualizada para usar MySQL com socket
- **New Tables**: Adicionadas tabelas slides_banner, membros e avaliacoes
- **Enhanced Fields**: Empresas agora têm campos desconto, website, endereco, imagem_detalhes
- **SQL Fixes**: Corrigidas funções PHP para compatibilidade com MySQL (LIMIT com prepared statements)

### Server Configuration Support
- **Upload Issues**: Criados arquivos de diagnóstico para problemas de upload em servidor local
- **Debug Tools**: test_upload.php para testar uploads e verificar configurações
- **Documentation**: CONFIGURACAO_SERVIDOR_LOCAL.md com soluções para problemas comuns
- **Common Issues**: Permissões de pasta, paths relativos, configuração PHP

### WebP Image Support Added
- **Image Upload Support**: Todos os campos de upload de imagem agora aceitam WebP
- **Validation Updated**: Validação PHP e HTML atualizada para incluir image/webp
- **File Types**: Suporte para JPG, PNG, GIF e WebP em logos, imagens de detalhes e slides do banner
- **Error Messages**: Mensagens de erro atualizadas para incluir WebP
- **File Size Limits**: Mantidos limites de 5MB para logos/detalhes e 10MB para slides

## Previous Changes (January 31, 2025)

### Header Final Implementation - ANETI Colors
- **Two-Line Header**: Header em duas linhas com identidade visual ANETI
- **Gradient Background**: Azul ANETI → Verde (linear-gradient(to right, #012d6a, #25a244))
- **Line 1**: "Clube de Benefícios ANETI" (sem logo) + busca central + "Entrar" + "Seja um Parceiro"
- **Line 2**: Menu horizontal de categorias funcionais com ícones (10 categorias)
- **Functional Categories**: Links para /public/categorias.php com filtros por categoria
- **White Elements**: Texto branco, ícones brancos flat design, hover effects
- **Responsive**: Layout adaptável, botões menores em mobile

### Banner Slide System Implementation
- **Database**: Tabela slides_banner com campos (id, imagem, ordem, status, datas)
- **Frontend**: Carousel Bootstrap com auto-rotation (5s), controles manuais, indicadores
- **Full Width**: Slides ocupam 100% largura, altura 450px (300px mobile)
- **Admin Panel**: "Slides do Banner" - upload, ativar/desativar, reordenar, deletar
- **Default Slide**: Slide padrão ANETI quando não há slides ativos
- **Image Management**: Upload para /uploads/slides/, tamanho recomendado 1920x500px

### Homepage Complete Redesign - Following Reference Model
- **Benefits in Highlight**: Redesigned carousel with real company logos (120x150px) and names
- **Recently Added**: New card design with cover images, circular logos, ratings, and categories
- **ANETI Colors**: Replaced all purple elements with ANETI blue (#012d6a)
- **Professional Layout**: Cards with rounded corners, shadows, and responsive design
- **Interactive Features**: Functional favorite buttons with localStorage persistence

### New Homepage Features
- **Carousel Style Benefits**: 8+ companies displayed horizontally with logos and names
- **Modern Card Design**: Cover image + circular logo overlay + detailed information
- **Rating System**: Star ratings with averages displayed on each card
- **Favorite System**: Heart buttons to save benefits locally
- **Category Badges**: Color-coded category labels for easy identification
- **Responsive Grid**: 3-4 columns adaptable to screen size

### Administrative Dashboard Redesign
- Complete dashboard overhaul following ANETI brand guidelines
- Replaced purple colors with ANETI blue (#012d6a) throughout the interface
- Implemented comprehensive analytics with Chart.js integration
- Added real-time visitor statistics with period filtering (24h, 7 days, 30 days, 6 months, 12 months)
- Created interactive line charts for visits vs unique visitors tracking

### New Dashboard Features
- **Statistics Cards**: Total benefits, paused benefits, registered users, weekly visits
- **Analytics Widgets**: Visits tracking, unique visitors monitoring, device breakdown (Desktop/Mobile)
- **Ranking Systems**: Most visited benefits, most used benefits, most present users, most active users
- **User Management**: Enhanced with bulk actions, import/export functionality
- **Modern UI**: Clean design with gradient cards, proper spacing, and ANETI branding

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Technology Stack**: Pure HTML5, CSS3, and Bootstrap for responsive design
- **JavaScript**: Vanilla JavaScript for client-side interactions and form validations
- **Styling**: Custom CSS with ANETI branding colors and Bootstrap components
- **No Frontend Framework**: Intentionally built without modern frameworks to keep it simple

### Backend Architecture
- **Technology**: Pure PHP (no frameworks)
- **Architecture Pattern**: Traditional server-side rendering with PHP scripts
- **Session Management**: PHP sessions for user authentication
- **File Structure**: Modular approach with separate files for different functionalities

### Data Storage
- **Database**: MySQL 8.0 running locally with socket connection
- **Tables**: Complete schema with empresas, usuarios, membros, admins, categorias, cupons, slides_banner
- **Data**: Populated with demo data including 8 companies, 6 users, 12 categories, 8 coupons
- **Views**: Statistical views for analytics (vw_empresa_stats, vw_usuario_stats)

## Key Components

### Public Area
- **Homepage**: Features promoted companies and search functionality
- **Company Listings**: Card-based layout showing partner companies
- **Company Details**: Individual pages with company information and benefit rules
- **Search System**: Multi-criteria search (name, city, category, keywords)

### Member Area
- **Authentication**: Simple login system (email-based placeholder)
- **Member Dashboard**: Shows member name, plan level (Junior, Pleno, Senior)
- **Coupon History**: Track of generated discount coupons
- **Coupon Generation**: Create new coupons for partner companies

### Coupon System
- **Generation**: Random/UUID-based coupon codes
- **Information**: Company name, coupon code, member name, timestamp
- **Output Formats**: Screen display + downloadable PDF/printable HTML

### Partner Company Registration
- **Self-Registration**: Form-based company signup system
- **No Authentication Required**: Currently open registration process

## Data Flow

1. **Public Browsing**: Users can browse companies and view details without authentication
2. **Member Authentication**: Email-based login to access member features
3. **Coupon Generation**: Authenticated members can generate coupons for specific companies
4. **Company Registration**: Companies can self-register through web forms

## External Dependencies

### Frontend Dependencies
- **Bootstrap**: For responsive UI components and layout
- **Custom Fonts/Icons**: Likely Google Fonts or similar for typography

### Backend Dependencies
- **PHP**: Server-side scripting (pure PHP, no frameworks)
- **Future Database**: Will need MySQL/PostgreSQL or similar RDBMS

### Development Dependencies
- **Web Server**: Apache/Nginx with PHP support
- **PDF Generation**: Will need PHP PDF library for coupon downloads

## Deployment Strategy

### Current Setup
- **Environment**: Traditional LAMP/LEMP stack
- **Files**: Static file serving for CSS/JS assets
- **PHP Processing**: Server-side rendering for all dynamic content

### Recommended Deployment
- **Web Server**: Apache or Nginx with PHP-FPM
- **Database**: MySQL or PostgreSQL (when implemented)
- **SSL/HTTPS**: Required for login and sensitive data
- **Backup Strategy**: Regular database and file backups

### Development Workflow
- **Local Development**: XAMPP/WAMP for local testing
- **Version Control**: Git-based workflow
- **Testing**: Manual testing on multiple devices/browsers due to Bootstrap responsive design

## Key Design Decisions

### Technology Choices
- **Pure PHP**: Chosen for simplicity and to avoid framework complexity
- **Bootstrap**: Provides professional UI without custom CSS development
- **No Database Initially**: Allows rapid prototyping and testing

### Architecture Decisions
- **Server-Side Rendering**: Traditional approach for better SEO and simpler deployment
- **Session-Based Auth**: Simple authentication without complex token systems
- **Modular File Structure**: Separates concerns while maintaining simplicity

### Future Considerations
- **Database Integration**: Will need proper data persistence layer
- **Security Enhancements**: Input validation, CSRF protection, password hashing
- **Performance Optimization**: Caching, image optimization, minification