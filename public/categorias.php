<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Get category from URL parameters
$categoria_param = $_GET['categoria'] ?? $_GET['cat'] ?? '';
$search_param = $_GET['search'] ?? '';

// Map category slugs to database names
$category_map = [
    'destaque' => 'Destaque',
    'comer-beber' => 'Alimentação',
    'compras' => 'Compras',
    'conveniencia' => 'Conveniência',
    'cultura-educacao' => 'Educação',
    'lazer-diversao' => 'Lazer',
    'mundo-pet' => 'Pet',
    'saude-bem-estar' => 'Saúde',
    'servicos' => 'Serviços',
    'viagem-turismo' => 'Turismo',
    'Alimentação' => 'Alimentação',
    'Saúde' => 'Saúde',
    'Educação' => 'Educação',
    'Lazer' => 'Lazer',
    'Moda' => 'Moda',
    'Tecnologia' => 'Tecnologia',
    'Serviços' => 'Serviços',
    'Turismo' => 'Turismo',
    'Beleza' => 'Beleza'
];

$categoria_nome = $category_map[$categoria_param] ?? $categoria_param;
$page_title = $categoria_nome ?: ($search_param ? "Busca: $search_param" : 'Todas as Empresas');

// Build query based on filters
$query = "SELECT * FROM empresas WHERE status = 'aprovada'";
$params = [];

if ($categoria_nome && $categoria_nome !== 'Todas') {
    if ($categoria_nome === 'Destaque') {
        $query .= " AND destaque = true";
    } else {
        $query .= " AND categoria = ?";
        $params[] = $categoria_nome;
    }
}

if ($search_param) {
    $query .= " AND (nome LIKE ? OR descricao LIKE ? OR cidade LIKE ?)";
    $search_term = "%$search_param%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
}

$query .= " ORDER BY nome ASC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $empresas = $stmt->fetchAll();
} catch (Exception $e) {
    $empresas = [];
}

// Get total count
$total_empresas = count($empresas);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Clube de Benefícios ANETI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Spacer para Header Fixed -->
    <div style="height: 140px;"></div>
    
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); padding: 40px 0; color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="fas <?php 
                            echo $categoria_nome === 'Destaque' ? 'fa-star' : 
                                ($categoria_nome === 'Alimentação' ? 'fa-utensils' : 
                                ($categoria_nome === 'Saúde' ? 'fa-heartbeat' : 
                                ($categoria_nome === 'Educação' ? 'fa-graduation-cap' : 
                                ($categoria_nome === 'Lazer' ? 'fa-gamepad' : 
                                ($categoria_nome === 'Moda' ? 'fa-tshirt' : 
                                ($categoria_nome === 'Tecnologia' ? 'fa-laptop' : 
                                ($categoria_nome === 'Serviços' ? 'fa-cogs' : 
                                ($categoria_nome === 'Turismo' ? 'fa-plane' : 
                                ($categoria_nome === 'Beleza' ? 'fa-spa' : 'fa-th-large')))))))));
                        ?>" style="margin-right: 15px;"></i>
                        <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <p class="mb-0" style="font-size: 1.1rem; opacity: 0.9;">
                        <?php echo $total_empresas; ?> benefício<?php echo $total_empresas !== 1 ? 's' : ''; ?> encontrado<?php echo $total_empresas !== 1 ? 's' : ''; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($search_param || $categoria_nome): ?>
                        <a href="categorias.php" class="btn btn-outline-light" style="border-radius: 25px;">
                            <i class="fas fa-times"></i> Limpar Filtros
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Companies Grid -->
    <section class="companies-grid" style="padding: 60px 0;">
        <div class="container">
            <?php if (!empty($empresas)): ?>
                <div class="row">
                    <?php foreach ($empresas as $empresa): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="benefit-card modern-card" style="height: 100%; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; background: white; border: none;">
                                <!-- Card Image -->
                                <div class="card-image-container" style="position: relative; height: 200px; overflow: hidden;">
                                    <?php if ($empresa['imagem_detalhes']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($empresa['imagem_detalhes']); ?>" 
                                             alt="<?php echo htmlspecialchars($empresa['nome']); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-store" style="font-size: 3rem; color: white; opacity: 0.7;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Logo Overlay -->
                                    <?php if ($empresa['logo']): ?>
                                        <div style="position: absolute; top: 15px; left: 15px; width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                            <img src="../uploads/<?php echo htmlspecialchars($empresa['logo']); ?>" 
                                                 alt="Logo <?php echo htmlspecialchars($empresa['nome']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: contain; border-radius: 50%;">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Favorite Button -->
                                    <button class="favorite-btn" onclick="toggleFavorite(<?php echo $empresa['id']; ?>)" 
                                            style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="far fa-heart" style="color: #012d6a; font-size: 1.1rem;"></i>
                                    </button>
                                </div>
                                
                                <!-- Card Content -->
                                <div class="card-body" style="padding: 20px;">
                                    <!-- Company Name -->
                                    <h5 style="color: #012d6a; font-weight: 700; margin-bottom: 8px; font-size: 1.2rem;">
                                        <?php echo htmlspecialchars($empresa['nome']); ?>
                                    </h5>
                                    
                                    <!-- Category Badge -->
                                    <span class="category-badge" style="background: #012d6a; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: 500; margin-bottom: 10px; display: inline-block;">
                                        <?php echo htmlspecialchars($empresa['categoria']); ?>
                                    </span>
                                    
                                    <!-- Description -->
                                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px; line-height: 1.4;">
                                        <?php echo htmlspecialchars(substr($empresa['descricao'], 0, 80)) . (strlen($empresa['descricao']) > 80 ? '...' : ''); ?>
                                    </p>
                                    
                                    <!-- Location -->
                                    <div style="display: flex; align-items: center; margin-bottom: 15px; color: #888; font-size: 0.85rem;">
                                        <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #012d6a;"></i>
                                        <?php echo htmlspecialchars($empresa['cidade'] . ', ' . $empresa['estado']); ?>
                                    </div>
                                    
                                    <!-- Rating -->
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                                        <div class="rating-stars">
                                            <?php
                                            $rating = $empresa['avaliacao_media'] ?? 4.5;
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo '<span style="color: ' . ($i <= $rating ? '#FFD700' : '#E0E0E0') . '; font-size: 0.9rem;">★</span>';
                                            }
                                            ?>
                                            <span style="color: #666; font-size: 0.85rem; margin-left: 8px;">
                                                <?php echo number_format($rating, 1); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <a href="empresa-detalhes.php?id=<?php echo $empresa['id']; ?>" 
                                       class="btn w-100" 
                                       style="background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); color: white; border: none; border-radius: 25px; padding: 10px; font-weight: 600; transition: all 0.3s ease;">
                                        <i class="fas fa-eye" style="margin-right: 8px;"></i>
                                        Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center" style="padding: 60px 20px;">
                    <div style="max-width: 400px; margin: 0 auto;">
                        <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                            <i class="fas fa-search" style="font-size: 3rem; color: white; opacity: 0.8;"></i>
                        </div>
                        <h3 style="color: #012d6a; font-weight: 700; margin-bottom: 15px;">
                            Nenhum benefício encontrado
                        </h3>
                        <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                            <?php if ($search_param): ?>
                                Não encontramos resultados para "<?php echo htmlspecialchars($search_param); ?>". 
                                Tente usar termos diferentes ou navegue pelas categorias.
                            <?php else: ?>
                                Não há benefícios disponíveis nesta categoria no momento.
                            <?php endif; ?>
                        </p>
                        <a href="../index.php" class="btn" style="background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); color: white; border: none; border-radius: 25px; padding: 12px 30px; font-weight: 600;">
                            <i class="fas fa-home" style="margin-right: 8px;"></i>
                            Voltar ao Início
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer Spacer -->
    <div style="height: 40px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Add hover effects to cards
        document.querySelectorAll('.benefit-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(1, 45, 106, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
            });
        });

        // Favorite functionality
        function toggleFavorite(empresaId) {
            const btn = event.target.closest('.favorite-btn');
            const icon = btn.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = '#e74c3c';
                
                // Save to localStorage
                let favorites = JSON.parse(localStorage.getItem('aneti_favorites') || '[]');
                if (!favorites.includes(empresaId)) {
                    favorites.push(empresaId);
                    localStorage.setItem('aneti_favorites', JSON.stringify(favorites));
                }
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                icon.style.color = '#012d6a';
                
                // Remove from localStorage
                let favorites = JSON.parse(localStorage.getItem('aneti_favorites') || '[]');
                favorites = favorites.filter(id => id !== empresaId);
                localStorage.setItem('aneti_favorites', JSON.stringify(favorites));
            }
        }

        // Load favorites on page load
        document.addEventListener('DOMContentLoaded', function() {
            const favorites = JSON.parse(localStorage.getItem('aneti_favorites') || '[]');
            favorites.forEach(empresaId => {
                const btn = document.querySelector(`button[onclick="toggleFavorite(${empresaId})"]`);
                if (btn) {
                    const icon = btn.querySelector('i');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.style.color = '#e74c3c';
                }
            });
        });
    </script>
</body>
</html>