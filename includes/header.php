<?php
// Determine the correct path based on current directory
$is_subdirectory = strpos($_SERVER['PHP_SELF'], '/public/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/empresa/') !== false;
$base_path = $is_subdirectory ? '../' : '';
?>
<!-- Header ANETI Padrão -->
<header class="main-header" style="background: linear-gradient(to right, #012d6a, #25a244);">
    <!-- Primeira Linha - Info Principal -->
    <div class="header-top" style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h1 class="brand-title" style="color: white; font-size: 24px; font-weight: 700; margin: 0;">
                        Clube de Benefícios ANETI
                    </h1>
                </div>
                <div class="col-md-4">
                    <?php if (strpos($_SERVER['PHP_SELF'], '/empresa/') === false): ?>
                    <form class="search-form" method="GET" action="<?= $base_path ?>public/categorias.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Buscar empresas..." 
                                   style="border-radius: 20px 0 0 20px; border: none;">
                            <button class="btn btn-light" type="submit" style="border-radius: 0 20px 20px 0; border: none;">
                                <i class="fas fa-search" style="color: #012d6a;"></i>
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-end">
                    <div class="user-menu">
                        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                            <div class="dropdown d-inline">
                                <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="border-radius: 20px;">
                                    <i class="fas fa-user"></i> <?php echo getMemberName(); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= $base_path ?>public/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item" href="<?= $base_path ?>public/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="btn btn-outline-light me-2" href="<?= $base_path ?>public/login.php" style="border-radius: 20px;">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                            <a class="btn btn-light" href="<?= $base_path ?>empresa/cadastro.php" style="border-radius: 20px; color: #012d6a; font-weight: 600;">
                                <i class="fas fa-handshake"></i> Seja um Parceiro
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Segunda Linha - Menu de Categorias -->
    <?php if (strpos($_SERVER['PHP_SELF'], '/empresa/') === false): ?>
    <nav class="categories-nav-horizontal" style="padding: 8px 0;">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 2rem;">
                <a href="<?= $base_path ?>index.php" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-home" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Início</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Alimentação" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-utensils" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Alimentação</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Saúde" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-heartbeat" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Saúde</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Educação" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-graduation-cap" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Educação</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Lazer" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-gamepad" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Lazer</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Moda" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-tshirt" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Moda</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Tecnologia" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-laptop" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Tecnologia</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Serviços" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-cogs" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Serviços</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Turismo" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-plane" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Turismo</span>
                    </div>
                </a>
                <a href="<?= $base_path ?>public/categorias.php?categoria=Beleza" class="category-item text-decoration-none">
                    <div class="text-center">
                        <div class="category-icon" style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">
                            <i class="fas fa-spa" style="color: white; font-size: 1.4rem;"></i>
                        </div>
                        <span class="category-name" style="color: white; font-size: 0.8rem; font-weight: 500;">Beleza</span>
                    </div>
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</header>