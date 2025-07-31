<?php
// Determine the correct path based on current directory
$is_subdirectory = strpos($_SERVER['PHP_SELF'], '/public/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/empresa/') !== false;
$base_path = $is_subdirectory ? '../' : '';
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
                        <a href="<?= $base_path ?>public/login.php" class="login-button me-2">Entrar</a>
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