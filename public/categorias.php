<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Get category from URL parameter
$categoria_slug = $_GET['cat'] ?? '';

// Map category slugs to names
$category_map = [
    'destaque' => 'Destaque',
    'comer-beber' => 'Alimentação',
    'compras' => 'Compras',
    'conveniencia' => 'Conveniência',
    'cultura-educacao' => 'Educação',
    'lazer-diversao' => 'Entretenimento',
    'mundo-pet' => 'Pet',
    'saude-bem-estar' => 'Saúde',
    'servicos' => 'Serviços',
    'viagem-turismo' => 'Viagem'
];

$categoria_nome = $category_map[$categoria_slug] ?? 'Todas';

// Get companies by category
try {
    if ($categoria_slug === 'destaque') {
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE status = 'aprovada' AND destaque = true ORDER BY nome ASC");
        $stmt->execute();
    } else if ($categoria_nome !== 'Todas') {
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE status = 'aprovada' AND categoria = ? ORDER BY nome ASC");
        $stmt->execute([$categoria_nome]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM empresas WHERE status = 'aprovada' ORDER BY nome ASC");
        $stmt->execute();
    }
    $empresas = $stmt->fetchAll();
} catch (Exception $e) {
    $empresas = [];
}

// Get all categories for sidebar
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($categoria_nome); ?> - Clube de Benefícios ANETI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header com Degradê - Modelo Referência -->
    <header class="gradient-header fixed-top">
        <!-- Linha 1: Barra Superior -->
        <div class="header-top-bar">
            <div class="container-fluid">
                <div class="row align-items-center py-2">
                    <!-- Nome do Clube (À esquerda) -->
                    <div class="col-md-3">
                        <div class="brand-section d-flex align-items-center">
                            <a href="../index.php" class="brand-name text-decoration-none">Clube de Benefícios ANETI</a>
                        </div>
                    </div>
                    
                    <!-- Campo de Busca (Ao centro) -->
                    <div class="col-md-6">
                        <div class="search-container">
                            <form action="buscar.php" method="GET" class="header-search-form">
                                <div class="search-input-group">
                                    <input type="text" name="q" class="search-input" placeholder="Encontrar um benefício">
                                    <button type="submit" class="search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Botões (À direita) -->
                    <div class="col-md-3">
                        <div class="header-actions text-end">
                            <a href="login.php" class="login-button me-2">Entrar</a>
                            <a href="../empresa/cadastro.php" class="partner-button">Seja um Parceiro</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Linha 2: Menu de Categorias -->
        <div class="categories-bar">
            <div class="container-fluid">
                <div class="categories-menu">
                    <a href="categorias.php?cat=destaque" class="category-item <?php echo $categoria_slug === 'destaque' ? 'active' : ''; ?>">
                        <i class="fas fa-star"></i>
                        <span>Destaque</span>
                    </a>
                    <a href="categorias.php?cat=comer-beber" class="category-item <?php echo $categoria_slug === 'comer-beber' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i>
                        <span>Comer e Beber</span>
                    </a>
                    <a href="categorias.php?cat=compras" class="category-item <?php echo $categoria_slug === 'compras' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Compras</span>
                    </a>
                    <a href="categorias.php?cat=conveniencia" class="category-item <?php echo $categoria_slug === 'conveniencia' ? 'active' : ''; ?>">
                        <i class="fas fa-store"></i>
                        <span>Conveniência</span>
                    </a>
                    <a href="categorias.php?cat=cultura-educacao" class="category-item <?php echo $categoria_slug === 'cultura-educacao' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Cultura e Educação</span>
                    </a>
                    <a href="categorias.php?cat=lazer-diversao" class="category-item <?php echo $categoria_slug === 'lazer-diversao' ? 'active' : ''; ?>">
                        <i class="fas fa-gamepad"></i>
                        <span>Lazer e Diversão</span>
                    </a>
                    <a href="categorias.php?cat=mundo-pet" class="category-item <?php echo $categoria_slug === 'mundo-pet' ? 'active' : ''; ?>">
                        <i class="fas fa-paw"></i>
                        <span>Mundo Pet</span>
                    </a>
                    <a href="categorias.php?cat=saude-bem-estar" class="category-item <?php echo $categoria_slug === 'saude-bem-estar' ? 'active' : ''; ?>">
                        <i class="fas fa-heartbeat"></i>
                        <span>Saúde e Bem-estar</span>
                    </a>
                    <a href="categorias.php?cat=servicos" class="category-item <?php echo $categoria_slug === 'servicos' ? 'active' : ''; ?>">
                        <i class="fas fa-tools"></i>
                        <span>Serviços</span>
                    </a>
                    <a href="categorias.php?cat=viagem-turismo" class="category-item <?php echo $categoria_slug === 'viagem-turismo' ? 'active' : ''; ?>">
                        <i class="fas fa-plane"></i>
                        <span>Viagem e Turismo</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer para Header Fixed -->
    <div style="height: 140px;"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="page-header mb-4">
                    <h1 class="page-title">
                        <i class="fas fa-<?php echo getCategoryIcon($categoria_nome); ?> me-2"></i>
                        <?php echo ucfirst($categoria_nome); ?>
                    </h1>
                    <p class="page-subtitle">
                        <?php echo count($empresas); ?> benefício(s) encontrado(s)
                    </p>
                </div>
            </div>
        </div>

        <!-- Companies Grid -->
        <div class="row">
            <?php if (empty($empresas)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>Nenhum benefício encontrado</h5>
                        <p>Não há empresas cadastradas nesta categoria no momento.</p>
                        <a href="../index.php" class="btn btn-primary">Voltar ao Início</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($empresas as $empresa): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card company-card">
                            <div class="card-img-top-wrapper">
                                <img src="../uploads/<?php echo htmlspecialchars($empresa['imagem_capa'] ?? 'default-company.jpg'); ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($empresa['nome']); ?>">
                                <div class="company-logo-overlay">
                                    <img src="../uploads/<?php echo htmlspecialchars($empresa['logo'] ?? 'default-logo.png'); ?>" 
                                         class="company-logo-small" alt="Logo">
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($empresa['nome']); ?></h5>
                                <p class="card-text company-description">
                                    <?php echo htmlspecialchars(substr($empresa['descricao'], 0, 100)); ?>...
                                </p>
                                <div class="company-meta mb-3">
                                    <span class="badge badge-category">
                                        <i class="fas fa-<?php echo getCategoryIcon($empresa['categoria']); ?> me-1"></i>
                                        <?php echo htmlspecialchars($empresa['categoria']); ?>
                                    </span>
                                    <span class="company-location">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($empresa['cidade']); ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="empresa-detalhes.php?id=<?php echo $empresa['id']; ?>" 
                                       class="btn btn-primary btn-sm">Ver Detalhes</a>
                                    <button class="btn btn-outline-danger btn-sm favorite-btn" 
                                            data-id="<?php echo $empresa['id']; ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Clube de Benefícios ANETI</h5>
                    <p>Conectando membros da ANETI com benefícios exclusivos.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; 2025 ANETI. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>