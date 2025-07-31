<?php
// Determine the correct path based on current directory
$is_subdirectory = strpos($_SERVER['PHP_SELF'], '/public/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/empresa/') !== false;
$base_path = $is_subdirectory ? '../' : '';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_nome'] : '';
?>
<!-- Header ANETI - Idêntico à Homepage -->
<header class="main-header fixed-top" style="background: linear-gradient(to right, #012d6a, #25a244); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <!-- Linha 1: Info Principal -->
    <div style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <!-- Nome do Clube (À esquerda) -->
                <div class="col-md-4">
                    <h1 style="color: white; font-size: 22px; font-weight: 700; margin: 0; text-decoration: none;">
                        <a href="<?= $base_path ?>index.php" style="color: white; text-decoration: none;">
                            Clube de Benefícios ANETI
                        </a>
                    </h1>
                </div>
                
                <!-- Campo de Busca (Ao centro) -->
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
                
                <!-- Botões (À direita) -->
                <div class="col-md-4">
                    <div class="header-actions text-end">
                        <?php if ($is_logged_in): ?>
                            <div class="dropdown me-2 d-inline-block">
                                <button class="btn login-button dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 20px; padding: 8px 16px;">
                                    <i class="fas fa-user me-2"></i>
                                    <span><?= htmlspecialchars($user_name) ?></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg">
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
    
    <!-- Linha 2: Menu de Categorias -->
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
</header>

<!-- Bootstrap JavaScript for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing dropdowns...');
    
    // Force initialize all dropdowns
    var dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdowns.forEach(function(dropdown) {
        console.log('Found dropdown:', dropdown);
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Dropdown(dropdown);
        }
    });
    
    // Manual click handler as backup
    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Manual dropdown click');
            var menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                // Close other open menus
                document.querySelectorAll('.dropdown-menu.show').forEach(function(openMenu) {
                    if (openMenu !== menu) {
                        openMenu.classList.remove('show');
                    }
                });
                // Toggle current menu
                menu.classList.toggle('show');
            }
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });
});
</script>