<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

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
    <!-- Modern Header -->
    <header class="modern-header">
        <div class="container">
            <!-- Top Header -->
            <div class="header-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="brand-section">
                            <div class="brand-logo">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div class="brand-text">
                                <h1>Clube ANETI</h1>
                                <p>Encontrar um benefício</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-menu">
                            <a href="public/login.php"><i class="fas fa-user"></i> Entrar</a>
                            <a href="empresa/cadastro.php"><i class="fas fa-store"></i> Cadastre sua Empresa</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Categories Navigation -->
            <div class="categories-nav-horizontal">
                <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                <a href="public/buscar.php?categoria=<?php echo urlencode($category['nome']); ?>" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-<?php echo getCategoryIcon($category['nome']); ?>"></i>
                    </div>
                    <div class="category-name"><?php echo htmlspecialchars($category['nome']); ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title">Seja bem-vindo ao<br><strong>Clube Sua Marca</strong></h1>
                        <p class="hero-subtitle">Descubra benefícios exclusivos para membros da ANETI</p>
                        
                        <!-- Search Form -->
                        <div class="hero-search">
                            <form action="public/buscar.php" method="GET" class="search-form">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" placeholder="Encontrar um benefício" aria-label="Buscar">
                                    <button class="btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="hero-illustration">
                            <svg class="hero-people" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                                <!-- Background discount circle -->
                                <circle cx="200" cy="150" r="120" fill="rgba(255,255,255,0.1)" opacity="0.5"/>
                                <text x="200" y="165" text-anchor="middle" font-size="60" fill="rgba(255,255,255,0.3)" font-weight="bold">%</text>
                                
                                <!-- Person 1 -->
                                <circle cx="140" cy="120" r="25" fill="#A78BFA"/>
                                <rect x="115" y="145" width="50" height="70" rx="25" fill="#8B5CF6"/>
                                
                                <!-- Person 2 -->
                                <circle cx="200" cy="100" r="22" fill="#C4B5FD"/>
                                <rect x="178" y="122" width="44" height="65" rx="22" fill="#A855F7"/>
                                
                                <!-- Person 3 -->
                                <circle cx="260" cy="130" r="20" fill="#DDD6FE"/>
                                <rect x="240" y="150" width="40" height="60" rx="20" fill="#9333EA"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        
                        <!-- Logo circular sobreposto -->
                        <div class="recent-card-logo">
                            <?php if ($company['logo']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                            <?php else: ?>
                                <div class="recent-card-logo-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botão de favoritar -->
                        <button class="favorite-btn" onclick="toggleFavorite(<?php echo $company['id']; ?>, event)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <!-- Conteúdo do card -->
                    <div class="recent-card-content">
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
                        $rating_query = $pdo->prepare("
                            SELECT AVG(nota) as media, COUNT(*) as total 
                            FROM avaliacoes 
                            WHERE empresa_id = ? AND status = 'aprovada'
                        ");
                        $rating_query->execute([$company['id']]);
                        $rating = $rating_query->fetch();
                        $avg_rating = $rating['media'] ? round($rating['media'], 1) : 0;
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
        });
    </script>
</body>
</html>
