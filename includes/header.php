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
    <!-- Linha 1: Info Principal -->
    <div class="main-header-line" style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <!-- Nome do Clube + Botão Menu Mobile -->
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h1 class="header-title" style="color: white; font-size: 22px; font-weight: 700; margin: 0;">
                            <a href="<?= $base_path ?>index.php" style="color: white; text-decoration: none;">
                                Clube de Benefícios ANETI
                            </a>
                        </h1>
                        <!-- Botão Menu Mobile (só aparece em mobile) -->
                        <button class="mobile-menu-toggle d-md-none" type="button" onclick="toggleMobileMenu()" 
                                style="background: none; border: none; color: white; font-size: 1.5rem; padding: 5px;">
                            <i class="fas fa-bars" id="mobile-menu-icon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Campo de Busca (Desktop - Ao centro) -->
                <div class="col-md-4 d-none d-md-block">
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
                
                <!-- Botões (Desktop - À direita) -->
                <div class="col-md-4 d-none d-md-block">
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
    
    <!-- Linha 2: Menu de Categorias Desktop -->
    <div class="categories-bar d-none d-md-block">
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
    
    <!-- Menu Mobile Expansível -->
    <div class="mobile-menu d-md-none" id="mobile-menu" style="display: none;">
        <!-- Campo de Busca Mobile -->
        <div class="mobile-search" style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
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
        
        <!-- Botões de Ação Mobile -->
        <div class="mobile-actions" style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <?php if ($is_logged_in): ?>
                <div class="mobile-user-info" style="margin-bottom: 15px;">
                    <div style="color: white; font-weight: 600; margin-bottom: 10px;">
                        <i class="fas fa-user me-2"></i><?= htmlspecialchars($user_name) ?>
                    </div>
                    <div class="mobile-user-actions">
                        <a href="<?= $base_path ?>public/dashboard.php" class="btn btn-sm btn-outline-light me-2" style="border-radius: 15px;">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                        <a href="<?= $base_path ?>public/logout.php" class="btn btn-sm btn-outline-danger" style="border-radius: 15px;">
                            <i class="fas fa-sign-out-alt me-1"></i>Sair
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= $base_path ?>public/login.php" class="btn btn-outline-light me-2 mb-2" style="border-radius: 15px;">
                    <i class="fas fa-sign-in-alt me-1"></i>Entrar
                </a>
            <?php endif; ?>
            <a href="<?= $base_path ?>empresa/cadastro.php" class="btn btn-light" style="border-radius: 15px; color: #012d6a; font-weight: 600;">
                <i class="fas fa-handshake me-1"></i>Seja um Parceiro
            </a>
        </div>
        
        <!-- Categorias Mobile em Grid -->
        <div class="mobile-categories" style="padding: 20px;">
            <div class="row g-3">
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
                        <span>Cultura e Educação</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= $base_path ?>public/categorias.php?cat=lazer-diversao" class="mobile-category-item">
                        <i class="fas fa-gamepad"></i>
                        <span>Lazer e Diversão</span>
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
                        <span>Saúde e Bem-estar</span>
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
                        <span>Viagem e Turismo</span>
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
/* Header Mobile Responsive Styles */
@media (max-width: 767.98px) {
    .main-header {
        height: auto;
        min-height: 60px;
    }
    
    .header-title {
        font-size: 16px !important;
    }
    
    .mobile-menu {
        background: rgba(0,0,0,0.1);
        border-top: 1px solid rgba(255,255,255,0.1);
        animation: slideDown 0.3s ease-out;
    }
    
    .mobile-category-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        background: rgba(255,255,255,0.1);
        padding: 15px 10px;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-align: center;
        min-height: 80px;
    }
    
    .mobile-category-item:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        transform: translateY(-2px);
    }
    
    .mobile-category-item i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    
    .mobile-category-item span {
        font-size: 0.8rem;
        font-weight: 500;
        line-height: 1.2;
    }
    
    /* Adjust body padding for mobile */
    body {
        padding-top: 60px !important;
    }
    
    .main-header.mobile-menu-open {
        height: auto;
    }
    
    .mobile-menu-toggle {
        transition: transform 0.3s ease;
    }
    
    .mobile-menu-toggle.active {
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
// Mobile Menu and Dropdown Functions
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('mobile-menu-icon');
    const header = document.querySelector('.main-header');
    
    if (mobileMenu.style.display === 'none' || mobileMenu.style.display === '') {
        mobileMenu.style.display = 'block';
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-times');
        header.classList.add('mobile-menu-open');
        document.querySelector('.mobile-menu-toggle').classList.add('active');
    } else {
        mobileMenu.style.display = 'none';
        menuIcon.classList.remove('fa-times');
        menuIcon.classList.add('fa-bars');
        header.classList.remove('mobile-menu-open');
        document.querySelector('.mobile-menu-toggle').classList.remove('active');
    }
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    dropdown.classList.toggle('show');
}

// Close mobile menu when clicking on category
document.addEventListener('DOMContentLoaded', function() {
    // Close mobile menu when clicking on a category
    document.querySelectorAll('.mobile-category-item').forEach(function(item) {
        item.addEventListener('click', function() {
            toggleMobileMenu();
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.main-header') && document.getElementById('mobile-menu').style.display === 'block') {
            toggleMobileMenu();
        }
    });
});
</script>