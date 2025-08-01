<?php
// Suppress non-critical warnings for production-like experience
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING);

session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get banner slides
$banner_slides = getBannerSlides($conn);

// Get featured companies
$featured_companies = getFeaturedCompanies($conn);

// Get recent companies
$recent_companies = getRecentCompanies($conn, 8);

// Get categories for navigation
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clube de Vantagens ANETI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header ANETI - Modelo Padrão -->
    <header class="main-header fixed-top" style="background: linear-gradient(to right, #012d6a, #25a244); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <!-- Linha 1: Info Principal -->
        <div style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Nome do Clube (À esquerda) -->
                    <div class="col-md-4">
                        <h1 style="color: white; font-size: 22px; font-weight: 700; margin: 0; text-decoration: none;">
                            <a href="index.php" style="color: white; text-decoration: none;">
                                Clube de Benefícios ANETI
                            </a>
                        </h1>
                    </div>
                    
                    <!-- Campo de Busca (Ao centro) -->
                    <div class="col-md-4">
                        <form method="GET" action="public/categorias.php">
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
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="dropdown me-2 d-inline-block">
                                    <button class="btn login-button d-flex align-items-center" type="button" id="userDropdown" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 20px; padding: 8px 16px; cursor: pointer;">
                                        <i class="fas fa-user me-2"></i>
                                        <span><?= htmlspecialchars($_SESSION['user_nome']) ?></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="public/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="public/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="public/login.php" class="login-button me-2">Entrar</a>
                            <?php endif; ?>
                            <a href="empresa/cadastro.php" class="partner-button">Seja um Parceiro</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Linha 2: Menu de Categorias -->
        <div class="categories-bar">
            <div class="container-fluid">
                <div class="categories-menu">
                    <a href="public/categorias.php?cat=destaque" class="category-item">
                        <i class="fas fa-star"></i>
                        <span>Destaque</span>
                    </a>
                    <a href="public/categorias.php?cat=comer-beber" class="category-item">
                        <i class="fas fa-utensils"></i>
                        <span>Comer e Beber</span>
                    </a>
                    <a href="public/categorias.php?cat=compras" class="category-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Compras</span>
                    </a>
                    <a href="public/categorias.php?cat=conveniencia" class="category-item">
                        <i class="fas fa-store"></i>
                        <span>Conveniência</span>
                    </a>
                    <a href="public/categorias.php?cat=cultura-educacao" class="category-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Cultura e Educação</span>
                    </a>
                    <a href="public/categorias.php?cat=lazer-diversao" class="category-item">
                        <i class="fas fa-gamepad"></i>
                        <span>Lazer e Diversão</span>
                    </a>
                    <a href="public/categorias.php?cat=mundo-pet" class="category-item">
                        <i class="fas fa-paw"></i>
                        <span>Mundo Pet</span>
                    </a>
                    <a href="public/categorias.php?cat=saude-bem-estar" class="category-item">
                        <i class="fas fa-heartbeat"></i>
                        <span>Saúde e Bem-estar</span>
                    </a>
                    <a href="public/categorias.php?cat=servicos" class="category-item">
                        <i class="fas fa-tools"></i>
                        <span>Serviços</span>
                    </a>
                    <a href="public/categorias.php?cat=viagem-turismo" class="category-item">
                        <i class="fas fa-plane"></i>
                        <span>Viagem e Turismo</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer para Header Fixed -->
    <div style="height: 140px;"></div>
    
    <!-- Banner Slides -->
    <section class="banner-slides">
        <?php if (!empty($banner_slides)): ?>
            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <!-- Slides -->
                <div class="carousel-inner">
                    <?php foreach ($banner_slides as $index => $slide): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="banner-slide-image" style="background-image: url('uploads/slides/<?php echo htmlspecialchars($slide['imagem']); ?>');">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Navigation arrows -->
                <?php if (count($banner_slides) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                <?php endif; ?>
                
                <!-- Indicators -->
                <?php if (count($banner_slides) > 1): ?>
                    <div class="carousel-indicators">
                        <?php foreach ($banner_slides as $index => $slide): ?>
                            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                                    class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="true" aria-label="Slide <?php echo $index + 1; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Slide padrão ANETI -->
            <div class="banner-slide-default">
                <div class="banner-slide-image default-slide" style="background-image: url('assets/images/banner-default.jpg');">
                    <div class="default-slide-content">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h1 class="banner-title">Seja bem-vindo ao<br><strong>Clube ANETI</strong></h1>
                                    <p class="banner-subtitle">Descubra benefícios exclusivos para membros da ANETI</p>
                                </div>
                                <div class="col-lg-4">
                                    <div class="banner-illustration">
                                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                                            <!-- Background discount circle -->
                                            <circle cx="200" cy="150" r="120" fill="rgba(255,255,255,0.1)" opacity="0.5"/>
                                            <text x="200" y="165" text-anchor="middle" font-size="60" fill="rgba(255,255,255,0.6)" font-weight="bold">%</text>
                                            
                                            <!-- Person 1 -->
                                            <circle cx="140" cy="120" r="25" fill="rgba(255,255,255,0.8)"/>
                                            <rect x="115" y="145" width="50" height="70" rx="25" fill="rgba(255,255,255,0.6)"/>
                                            
                                            <!-- Person 2 -->
                                            <circle cx="200" cy="100" r="22" fill="rgba(255,255,255,0.7)"/>
                                            <rect x="178" y="122" width="44" height="65" rx="22" fill="rgba(255,255,255,0.5)"/>
                                            
                                            <!-- Person 3 -->
                                            <circle cx="260" cy="130" r="20" fill="rgba(255,255,255,0.6)"/>
                                            <rect x="240" y="150" width="40" height="60" rx="20" fill="rgba(255,255,255,0.4)"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <h2 class="section-title">Benefícios em Destaque</h2>
            <div class="benefits-carousel-container">
                <div class="benefits-carousel">
                    <?php foreach ($featured_companies as $company): ?>
                    <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="benefit-card">
                        <div class="benefit-logo">
                            <?php if ($company['logo']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                            <?php else: ?>
                                <div class="benefit-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="benefit-name"><?php echo htmlspecialchars($company['nome']); ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Companies -->
    <section class="recent-section">
        <div class="container">
            <h2 class="section-title">Adicionados recentemente</h2>
            <div class="recent-grid">
                <?php foreach ($recent_companies as $company): ?>
                <div class="recent-card">
                    <!-- Imagem de capa da empresa -->
                    <div class="recent-card-cover">
                        <?php if ($company['imagem_detalhes']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($company['imagem_detalhes']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                        <?php else: ?>
                            <div class="recent-card-cover-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Botão de favoritar -->
                        <button class="favorite-btn" onclick="toggleFavorite(<?php echo $company['id']; ?>, event)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <!-- Conteúdo do card -->
                    <div class="recent-card-content">
                        <!-- Logo circular no topo da área branca -->
                        <div class="recent-card-logo">
                            <?php if ($company['logo']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                            <?php else: ?>
                                <div class="recent-card-logo-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="recent-card-title"><?php echo htmlspecialchars($company['nome']); ?></h3>
                        
                        <div class="recent-card-category">
                            <span class="category-badge"><?php echo htmlspecialchars($company['categoria']); ?></span>
                        </div>
                        
                        <p class="recent-card-description">
                            <?php 
                            $description = $company['descricao'] ?: 'Aproveite os benefícios exclusivos para membros da ANETI com descontos especiais.';
                            echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : ''); 
                            ?>
                        </p>
                        
                        <!-- Avaliação com estrelas -->
                        <?php
                        // Buscar avaliação média da empresa
                        try {
                            $rating_query = $conn->prepare("
                                SELECT AVG(nota) as media, COUNT(*) as total 
                                FROM avaliacoes 
                                WHERE empresa_id = ? AND status = 'aprovada'
                            ");
                            $rating_query->execute([$company['id']]);
                            $rating = $rating_query->fetch();
                            $avg_rating = $rating['media'] ? round($rating['media'], 1) : 0;
                        } catch (Exception $e) {
                            $avg_rating = 0; // Fallback se não houver conexão com BD
                        }
                        ?>
                        
                        <div class="recent-card-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-value"><?php echo $avg_rating; ?></span>
                        </div>
                        
                        <!-- Localização -->
                        <div class="recent-card-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($company['cidade']); ?></span>
                        </div>
                        
                        <!-- Link para detalhes -->
                        <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="recent-card-link">
                            Ver detalhes
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Função para favoritar empresas
        function toggleFavorite(companyId, event) {
            event.preventDefault();
            event.stopPropagation();
            
            let favorites = JSON.parse(localStorage.getItem('favoriteCompanies') || '[]');
            const isFavorited = favorites.includes(companyId);
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            
            if (isFavorited) {
                // Remover dos favoritos
                favorites = favorites.filter(id => id !== companyId);
                icon.className = 'far fa-heart';
                btn.style.background = 'rgba(255, 255, 255, 0.9)';
            } else {
                // Adicionar aos favoritos
                favorites.push(companyId);
                icon.className = 'fas fa-heart';
                btn.style.background = 'rgba(220, 53, 69, 0.1)';
            }
            
            localStorage.setItem('favoriteCompanies', JSON.stringify(favorites));
        }
        
        // Verificar favoritos ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const favorites = JSON.parse(localStorage.getItem('favoriteCompanies') || '[]');
            
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const companyId = parseInt(btn.getAttribute('onclick').match(/\d+/)[0]);
                if (favorites.includes(companyId)) {
                    const icon = btn.querySelector('i');
                    icon.className = 'fas fa-heart';
                    btn.style.background = 'rgba(220, 53, 69, 0.1)';
                }
            });
            
            // Simple dropdown functionality - definitive solution
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                console.log('User dropdown found, adding event listener');
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const menu = document.querySelector('#userDropdown + .dropdown-menu');
                    if (menu) {
                        // Close all other dropdowns
                        document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                            if (m !== menu) m.classList.remove('show');
                        });
                        
                        // Toggle this dropdown
                        menu.classList.toggle('show');
                        console.log('Dropdown menu toggled. Visible:', menu.classList.contains('show'));
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        });
    </script>
</body>
</html>
