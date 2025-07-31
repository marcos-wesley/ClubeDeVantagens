# ANETI Clube de Vantagens - Sistema Web

## Overview

This is a simple web system for ANETI's Benefits Club (Clube de Vantagens da ANETI) built with pure PHP, HTML, CSS, and Bootstrap. The system allows ANETI members to browse partner companies, generate discount coupons, and provides a registration system for partner companies.

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