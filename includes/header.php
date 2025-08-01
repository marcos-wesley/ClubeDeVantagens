<?php
// Suppress non-critical warnings for production-like experience
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the correct path based on current directory
$is_subdirectory = strpos($_SERVER['PHP_SELF'], '/public/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/empresa/') !== false;
$base_path = $is_subdirectory ? '../' : '';

// Check if user is logged in with proper session validation
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && 
                isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Ensure user_name always has a valid string value
if ($is_logged_in && isset($_SESSION['user_nome']) && !empty($_SESSION['user_nome'])) {
    $user_name = $_SESSION['user_nome'];
} else {
    $user_name = 'Usuário';
}
?>
<!-- Header ANETI - Responsivo com Menu Mobile -->
<header class="main-header fixed-top" style="background: linear-gradient(to right, #012d6a, #25a244); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <!-- Desktop: Linha completa -->
    <div class="desktop-header d-none d-md-block" style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <!-- Nome do Clube -->
                <div class="col-md-4">
                    <h1 style="color: white; font-size: 22px; font-weight: 700; margin: 0;">
                        <a href="<?= $base_path ?>index.php" style="color: white; text-decoration: none;">
                            Clube de Benefícios ANETI
                        </a>
                    </h1>
                </div>
                
                <!-- Campo de Busca -->
                <div class="col-md-4">
                    <form method="GET" action="<?= $base_path ?>public/categorias.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Buscar empresas..." 
                                   style="border-radius: 20px 0 0 20px; border: none; padding: 10px 15px;">
                            <button class="btn btn-light" type="submit" style="border-radius: 0 20px 20px 0; border: none; padding: 10px 15px;">
                                <i class="fas fa-search" style="color: #012d6a;"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Botões -->
                <div class="col-md-4">
                    <div class="header-actions text-end">
                        <?php if ($is_logged_in): ?>
                            <div class="dropdown me-2 d-inline-block">
                                <button class="btn login-button dropdown-toggle d-flex align-items-center" type="button" onclick="toggleUserDropdown()" 
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 20px; padding: 8px 16px;">
                                    <i class="fas fa-user me-2"></i>
                                    <span><?= htmlspecialchars($user_name) ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg" id="user-dropdown">
                                    <li><a class="dropdown-item" href="<?= $base_path ?>public/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?= $base_path ?>public/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="<?= $base_path ?>public/login.php" class="login-button me-2">Entrar</a>
                        <?php endif; ?>
                        <a href="<?= $base_path ?>empresa/cadastro.php" class="partner-button">Seja um Parceiro</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile: Header compacto -->
    <div class="mobile-header d-md-none" style="padding: 10px 0;">
        <div class="container">
            <div class="row align-items-center">
                <!-- Menu Hambúrguer -->
                <div class="col-2">
                    <button class="mobile-menu-toggle" type="button" onclick="toggleMobileMenu()" 
                            style="background: none; border: none; color: white; font-size: 1.4rem; padding: 8px;">
                        <i class="fas fa-bars" id="mobile-menu-icon"></i>
                    </button>
                </div>
                
                <!-- Nome Centralizado -->
                <div class="col-8 text-center">
                    <h1 style="color: white; font-size: 16px; font-weight: 700; margin: 0;">
                        <a href="<?= $base_path ?>index.php" style="color: white; text-decoration: none;">
                            Clube de Benefícios ANETI
                        </a>
                    </h1>
                </div>
                
                <!-- Login/User -->
                <div class="col-2 text-end">
                    <?php if ($is_logged_in): ?>
                        <button class="btn p-0" onclick="toggleUserDropdown()" style="background: none; border: none; color: white; font-size: 1.2rem;">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg position-absolute" id="user-dropdown" style="right: 15px; top: 50px; z-index: 1050;">
                            <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars($user_name) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $base_path ?>public/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= $base_path ?>public/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    <?php else: ?>
                        <a href="<?= $base_path ?>public/login.php" style="color: white; font-size: 1.2rem; text-decoration: none;">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Linha 2: Menu de Categorias (Desktop Only) -->
    <div class="categories-bar">
        <div class="container-fluid">
            <div class="categories-menu">
                <a href="<?= $base_path ?>public/categorias.php?cat=destaque" class="category-item">
                    <i class="fas fa-star"></i>
                    <span>Destaque</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=comer-beber" class="category-item">
                    <i class="fas fa-utensils"></i>
                    <span>Comer e Beber</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=compras" class="category-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Compras</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=conveniencia" class="category-item">
                    <i class="fas fa-store"></i>
                    <span>Conveniência</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=cultura-educacao" class="category-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Cultura e Educação</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=lazer-diversao" class="category-item">
                    <i class="fas fa-gamepad"></i>
                    <span>Lazer e Diversão</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=mundo-pet" class="category-item">
                    <i class="fas fa-paw"></i>
                    <span>Mundo Pet</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=saude-bem-estar" class="category-item">
                    <i class="fas fa-heartbeat"></i>
                    <span>Saúde e Bem-estar</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=servicos" class="category-item">
                    <i class="fas fa-tools"></i>
                    <span>Serviços</span>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?cat=viagem-turismo" class="category-item">
                    <i class="fas fa-plane"></i>
                    <span>Viagem e Turismo</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Menu Mobile Slidedown -->
    <div class="mobile-menu d-md-none" id="mobile-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: linear-gradient(to right, #012d6a, #25a244); z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
        
        <!-- Campo de Busca -->
        <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <form method="GET" action="<?= $base_path ?>public/categorias.php">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Buscar empresas..." 
                           style="border-radius: 25px 0 0 25px; border: none; padding: 12px 20px; font-size: 14px;">
                    <button class="btn btn-light" type="submit" style="border-radius: 0 25px 25px 0; border: none; padding: 12px 20px;">
                        <i class="fas fa-search" style="color: #012d6a;"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Botão Seja Parceiro -->
        <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <a href="<?= $base_path ?>empresa/cadastro.php" class="btn btn-light w-100" style="border-radius: 25px; color: #012d6a; font-weight: 600; padding: 12px;">
                <i class="fas fa-handshake me-2"></i>Seja um Parceiro
            </a>
        </div>
        
        <!-- Categorias em Grid Organizado -->
        <div style="padding: 20px;">
            <h6 style="color: white; margin-bottom: 15px; font-weight: 600; text-align: center;">
                <i class="fas fa-list me-2"></i>Categorias
            </h6>
            <div class="row g-2">
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=destaque" class="mobile-category-item">
                        <i class="fas fa-star"></i>
                        <span>Destaque</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=comer-beber" class="mobile-category-item">
                        <i class="fas fa-utensils"></i>
                        <span>Comer e Beber</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=compras" class="mobile-category-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Compras</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=conveniencia" class="mobile-category-item">
                        <i class="fas fa-store"></i>
                        <span>Conveniência</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=cultura-educacao" class="mobile-category-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Cultura</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=lazer-diversao" class="mobile-category-item">
                        <i class="fas fa-gamepad"></i>
                        <span>Lazer</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=mundo-pet" class="mobile-category-item">
                        <i class="fas fa-paw"></i>
                        <span>Mundo Pet</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=saude-bem-estar" class="mobile-category-item">
                        <i class="fas fa-heartbeat"></i>
                        <span>Saúde</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=servicos" class="mobile-category-item">
                        <i class="fas fa-tools"></i>
                        <span>Serviços</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=viagem-turismo" class="mobile-category-item">
                        <i class="fas fa-plane"></i>
                        <span>Viagem</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Bootstrap CSS e JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Mobile Header Styles */
@media (max-width: 767.98px) {
    .main-header {
        height: 60px !important;
    }
    
    .mobile-menu {
        animation: slideDown 0.3s ease;
    }
    
    .mobile-category-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        background: rgba(255,255,255,0.15);
        padding: 12px 8px;
        border-radius: 10px;
        transition: all 0.2s ease;
        text-align: center;
        min-height: 70px;
        margin-bottom: 8px;
    }
    
    .mobile-category-item:hover {
        background: rgba(255,255,255,0.25);
        color: white;
        transform: scale(1.05);
    }
    
    .mobile-category-item i {
        font-size: 1.3rem;
        margin-bottom: 6px;
        opacity: 0.9;
    }
    
    .mobile-category-item span {
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1.1;
        opacity: 0.95;
    }
    
    .mobile-menu-toggle {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 6px;
    }
    
    .mobile-menu-toggle:hover {
        background: rgba(255,255,255,0.1) !important;
    }
    
    .mobile-menu-toggle.active #mobile-menu-icon {
        transform: rotate(90deg);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 500px;
    }
}

/* Desktop adjustments */
@media (min-width: 768px) {
    body {
        padding-top: 140px !important;
    }
}
</style>

<script>
// Mobile Menu Functions
let mobileMenuOpen = false;

function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('mobile-menu-icon');
    
    if (!mobileMenuOpen) {
        // Open menu
        mobileMenu.style.display = 'block';
        menuIcon.className = 'fas fa-times';
        mobileMenuOpen = true;
        
        // Add click listener to close menu when clicking outside
        setTimeout(() => {
            document.addEventListener('click', closeMobileMenuOutside);
        }, 100);
    } else {
        // Close menu
        closeMobileMenu();
    }
}

function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('mobile-menu-icon');
    
    mobileMenu.style.display = 'none';
    menuIcon.className = 'fas fa-bars';
    mobileMenuOpen = false;
    
    document.removeEventListener('click', closeMobileMenuOutside);
}

function closeMobileMenuOutside(event) {
    const mobileMenu = document.getElementById('mobile-menu');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
        closeMobileMenu();
    }
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Initialize when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Close mobile menu when clicking on category links
    document.querySelectorAll('.mobile-category-item').forEach(function(item) {
        item.addEventListener('click', function() {
            closeMobileMenu();
        });
    });
    
    // Close user dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown && !e.target.closest('.dropdown') && !e.target.closest('button[onclick*="toggleUserDropdown"]')) {
            dropdown.classList.remove('show');
        }
    });
});
</script>