# ANETI Clube de Vantagens - Sistema Web

## Overview

This is a comprehensive web system for ANETI's Benefits Club (Clube de Vantagens da ANETI) built with pure PHP, HTML, CSS, and Bootstrap. The system provides a complete membership benefits platform with advanced administrative management, partner company discount access, coupon generation, and detailed analytics dashboard.

## Recent Changes (January 31, 2025)

### Header Complete Redesign - Gradient Model
- **Two-Line Header**: Header em duas linhas seguindo modelo de referência visual
- **Gradient Background**: Degradê rosa → roxo → laranja (linear-gradient(to right, #ac2bff, #f14d71, #ff9b3f))
- **Line 1**: Logo ANETI branca + campo de busca central + botão "Entrar"
- **Line 2**: Menu horizontal de categorias com ícones (10 categorias principais)
- **White Elements**: Logo em branco, texto branco, ícones brancos flat design
- **Responsive**: Scroll horizontal para categorias em mobile, layout adaptável

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
- **Database**: Not yet implemented (will likely use MySQL/PostgreSQL when added)
- **Current State**: System appears to be in early development phase
- **Future Implementation**: Will need database for storing users, companies, coupons, and member data

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