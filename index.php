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
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Seja bem-vindo ao<br><span class="text-primary">Clube de Vantagens</span></h1>
                    <p class="hero-subtitle">Descubra benefícios exclusivos para membros da ANETI</p>
                    
                    <!-- Search Form -->
                    <form action="public/buscar.php" method="GET" class="search-form mt-4">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="q" placeholder="Procurar um benefício" aria-label="Buscar">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <svg width="400" height="300" viewBox="0 0 400 300" class="hero-svg">
                            <!-- Discount icon -->
                            <rect x="50" y="80" width="120" height="120" rx="20" fill="#012d6a" opacity="0.1"/>
                            <text x="110" y="150" text-anchor="middle" font-size="48" fill="#012d6a">%</text>
                            
                            <!-- People illustrations -->
                            <circle cx="250" cy="100" r="20" fill="#6c757d"/>
                            <rect x="230" y="120" width="40" height="60" rx="5" fill="#012d6a"/>
                            
                            <circle cx="320" cy="120" r="18" fill="#6c757d"/>
                            <rect x="302" y="138" width="36" height="55" rx="5" fill="#28a745"/>
                            
                            <circle cx="290" cy="180" r="15" fill="#6c757d"/>
                            <rect x="275" y="195" width="30" height="45" rx="4" fill="#ffc107"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Navigation -->
    <section class="categories-nav py-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <?php foreach ($categories as $category): ?>
                        <a href="public/buscar.php?categoria=<?php echo urlencode($category['nome']); ?>" class="category-link">
                            <i class="fas fa-<?php echo getCategoryIcon($category['nome']); ?>"></i>
                            <?php echo htmlspecialchars($category['nome']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Companies -->
    <section class="featured-companies py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title">Benefícios em Destaque</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($featured_companies as $company): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="company-card-featured">
                        <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>">
                            <div class="company-logo">
                                <?php if ($company['logo']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                                <?php else: ?>
                                    <div class="logo-placeholder">
                                        <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h6 class="company-name"><?php echo htmlspecialchars($company['nome']); ?></h6>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Recent Companies -->
    <section class="recent-companies py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title">Adicionados recentemente</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($recent_companies as $company): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="company-card">
                        <div class="company-card-header">
                            <?php if ($company['logo']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="company-logo-img">
                            <?php else: ?>
                                <div class="company-logo-placeholder">
                                    <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="company-card-body">
                            <h5 class="company-card-title"><?php echo htmlspecialchars($company['nome']); ?></h5>
                            <p class="company-card-category"><?php echo htmlspecialchars($company['categoria']); ?></p>
                            <p class="company-card-description"><?php echo htmlspecialchars(substr($company['descricao'], 0, 100)); ?>...</p>
                            <div class="company-card-footer">
                                <span class="company-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($company['cidade']); ?>
                                </span>
                                <a href="public/empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="btn btn-primary btn-sm">
                                    Ver detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
