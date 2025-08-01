# ANETI Clube de Vantagens - Sistema Web

## Overview
This web system for ANETI's Benefits Club provides a complete membership benefits platform. It offers advanced administrative management, partner company discount access, coupon generation, and a detailed analytics dashboard. The project aims to provide a comprehensive and user-friendly platform for ANETI members to access exclusive benefits, while also offering robust tools for administration and partner management. It focuses on delivering a streamlined experience for users and efficient management for administrators, with a vision to enhance member engagement and satisfaction.

## User Preferences
Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
The system uses pure HTML5, CSS3, and Bootstrap for a responsive design. Client-side interactions and form validations are handled with Vanilla JavaScript. Styling incorporates custom CSS with ANETI branding colors and Bootstrap components. No frontend frameworks are used to maintain simplicity.

### Backend Architecture
The backend is built with pure PHP without frameworks, employing a traditional server-side rendering approach. PHP sessions are used for user authentication. The file structure is modular, separating different functionalities into distinct files.

### Data Storage
The project utilizes MySQL 8.0 for data storage, configured to run locally with a socket connection. The database schema includes tables for companies, users, members, administrators, categories, coupons, and banner slides. It is populated with demo data, and statistical views (e.g., `vw_empresa_stats`, `vw_usuario_stats`) are implemented for analytics.

### Key Components
-   **Public Area**: Includes a homepage featuring promoted companies, company listings with card-based layouts, individual company details pages, and a multi-criteria search system.
-   **Member Area**: Provides an email-based login system, a member dashboard displaying user details and plan levels, a coupon history tracker, and functionality for generating new coupons.
-   **Coupon System**: Generates random/UUID-based coupon codes, displaying information such as company name, coupon code, member name, and timestamp. Coupons can be viewed on-screen or generated as printable HTML.
-   **Partner Company Registration**: Offers a form-based self-registration system for companies, currently open without requiring prior authentication.

### Data Flow
Users can browse companies and view details without authentication. Members authenticate via an email-based login to access member features and generate coupons for specific companies. Companies can self-register through web forms.

### Design Decisions
The choice of pure PHP and Bootstrap prioritizes simplicity and avoids framework complexity. Server-side rendering is used for better SEO and simpler deployment. Session-based authentication is employed for a straightforward authentication process. A modular file structure separates concerns while maintaining overall simplicity. The UI/UX emphasizes ANETI's brand identity, incorporating a blue-to-green gradient (ANETI colors) across the system, consistent visual elements, and responsive layouts across all pages, including the administrative panel. Key features like a professional tab system on company detail pages and interactive charts in the admin dashboard enhance user experience and data visualization.

## External Dependencies

### Frontend Dependencies
-   **Bootstrap**: Used for responsive UI components and layout.
-   **Google Fonts**: Likely used for typography.

### Backend Dependencies
-   **PHP**: Core server-side scripting language (pure PHP).
-   **MySQL**: Relational Database Management System (MySQL 8.0).

### API Integrations
-   **WordPress API**: Integrated for member login authentication against ANETI's WordPress platform, including plan-based access control.

### Development Dependencies
-   **Web Server**: Apache/Nginx with PHP support.
-   **Chart.js**: Integrated for interactive analytics charts on the administrative dashboard.