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
            <div class="benefits-grid">
                <?php foreach ($featured_companies as $company): ?>
                <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="benefit-item">
                    <div class="benefit-logo">
                        <?php if ($company['logo']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                        <?php else: ?>
                            <div class="benefit-placeholder">
                                <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h6 class="benefit-name"><?php echo htmlspecialchars($company['nome']); ?></h6>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Recent Companies -->
    <section class="recent-section">
        <div class="container">
            <h2 class="section-title">Adicionados recentemente</h2>
            <div class="recent-grid">
                <?php foreach ($recent_companies as $company): ?>
                <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="recent-card">
                    <div class="recent-card-image">
                        <div class="recent-card-overlay">
                            <?php if ($company['logo']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                            <?php else: ?>
                                <div class="recent-card-placeholder">
                                    <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="recent-card-content">
                        <h3 class="recent-card-title"><?php echo htmlspecialchars($company['nome']); ?></h3>
                        <div class="recent-card-category"><?php echo htmlspecialchars($company['categoria']); ?></div>
                        <p class="recent-card-description"><?php echo htmlspecialchars(substr($company['descricao'], 0, 120)); ?>...</p>
                        <div class="recent-card-meta">
                            <div class="recent-card-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($company['cidade']); ?>
                            </div>
                            <div class="favorite-btn">
                                <i class="far fa-heart"></i>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
