<?php
require_once '../config/database.php';
require_once '../config/constants.php';
session_start();

// Funções auxiliares
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Verificar se ID da empresa foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$id = (int)$_GET['id'];

// Dados simulados da empresa para teste
$company = [
    'id' => $id,
    'nome' => 'Empresa Parceira ANETI',
    'descricao' => 'Descrição da empresa parceira com benefícios exclusivos para membros ANETI.',
    'categoria' => 'Alimentação',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'endereco' => 'Rua das Empresas, 123',
    'telefone' => '(11) 99999-9999',
    'email' => 'contato@empresa.com.br',
    'website' => 'https://www.empresa.com.br',
    'logo' => null,
    'imagem_detalhes' => null,
    'regras' => 'Apresentar carteirinha ANETI para obter desconto de 15% em todos os produtos.',
    'status' => 'ativa'
];

$reviews = [];
$rating_summary = ['media' => 4.5, 'total' => 12, 'star5' => 6, 'star4' => 4, 'star3' => 2, 'star2' => 0, 'star1' => 0];

try {
    if (isset($pdo) && $pdo) {
        // Buscar dados da empresa se banco estiver disponível
        $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ? AND status = 'ativa'");
        $stmt->execute([$id]);
        $company_db = $stmt->fetch();
        
        if ($company_db) {
            $company = $company_db;
            
            // Buscar avaliações da empresa
            $stmt = $pdo->prepare("
                SELECT 
                    id, 
                    usuario_nome, 
                    usuario_email, 
                    rating, 
                    comentario, 
                    created_at 
                FROM avaliacoes 
                WHERE empresa_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$id]);
            $reviews = $stmt->fetchAll() ?: [];

            // Calcular estatísticas das avaliações
            $stmt = $pdo->prepare("
                SELECT 
                    AVG(rating) as media,
                    COUNT(*) as total,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as star5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as star4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as star3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as star2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as star1
                FROM avaliacoes WHERE empresa_id = ?
            ");
            $stmt->execute([$id]);
            $rating_summary = $stmt->fetch() ?: $rating_summary;
        }
    }
} catch (Exception $e) {
    // Em caso de erro, usar dados simulados
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company['nome']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Spacer para Header Fixed -->
    <div style="height: 140px;"></div>

    <!-- Company Header Section -->
    <div class="company-header-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 40px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <?php if ($company['logo']): ?>
                        <div class="company-logo-container" style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div class="company-logo-placeholder" style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; background: #012d6a; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <h1 class="company-title" style="color: #012d6a; font-size: 2.5rem; font-weight: 700; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($company['nome']); ?>
                    </h1>
                    <div class="company-rating mb-3">
                        <?php
                        $avg_rating = $rating_summary['media'] ? round($rating_summary['media'], 1) : 0;
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $avg_rating ? 'text-warning' : 'text-muted'; ?>" style="font-size: 1.2rem;"></i>
                        <?php endfor; ?>
                        <span class="ms-2" style="font-size: 1.1rem; font-weight: 600;"><?php echo $avg_rating; ?>/5</span>
                        <span class="text-muted ms-1">(<?php echo $rating_summary['total']; ?> avaliações)</span>
                    </div>
                    <div class="company-meta mb-3">
                        <span class="badge bg-primary me-2" style="font-size: 0.9rem; padding: 8px 12px;">
                            <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($company['categoria']); ?>
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?>
                        </span>
                    </div>
                    <p class="company-description" style="font-size: 1.1rem; line-height: 1.6; color: #6c757d;">
                        <?php echo htmlspecialchars($company['descricao']); ?>
                    </p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="action-buttons">
                        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                            <button class="btn btn-success btn-lg mb-2 w-100" style="border-radius: 25px; font-weight: 600; padding: 12px 0;">
                                <i class="fas fa-ticket-alt me-2"></i>USAR BENEFÍCIO
                            </button>
                        <?php else: ?>
                            <a href="../public/login.php" class="btn btn-success btn-lg mb-2 w-100" style="border-radius: 25px; font-weight: 600; padding: 12px 0;">
                                <i class="fas fa-sign-in-alt me-2"></i>FAZER LOGIN
                            </a>
                        <?php endif; ?>
                        <div class="company-actions mt-3">
                            <button class="btn btn-outline-secondary btn-sm me-2" style="border-radius: 20px;">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm me-2" style="border-radius: 20px;">
                                <i class="fas fa-share"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" style="border-radius: 20px;">
                                <i class="fas fa-flag"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="navigation-tabs" style="background: white; border-bottom: 1px solid #dee2e6;">
        <div class="container">
            <ul class="nav nav-tabs" style="border: none;">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#detalhes" style="border: none; color: #012d6a; font-weight: 600;">
                        Detalhes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#avaliacoes" style="border: none; color: #6c757d; font-weight: 600;">
                        Avaliações 
                        <span class="badge bg-primary ms-1"><?php echo $rating_summary['total']; ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Action Buttons Row -->
    <div class="action-buttons-section" style="background: #f8f9fa; padding: 20px 0; border-bottom: 1px solid #dee2e6;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <button class="btn btn-outline-primary btn-sm" style="border-radius: 20px; padding: 8px 16px;">
                            <i class="fas fa-route me-1"></i>Traçar Rota
                        </button>
                        <button class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 8px 16px;">
                            <i class="fas fa-heart me-1"></i>Salvar este benefício
                        </button>
                        <button class="btn btn-outline-info btn-sm" style="border-radius: 20px; padding: 8px 16px;">
                            <i class="fas fa-share me-1"></i>Compartilhar
                        </button>
                        <button class="btn btn-outline-warning btn-sm" style="border-radius: 20px; padding: 8px 16px;">
                            <i class="fas fa-star me-1"></i>Avaliar este parceiro
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" style="border-radius: 20px; padding: 8px 16px;">
                            <i class="fas fa-flag me-1"></i>Reportar um problema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="padding: 40px 0;">
        <div class="row">
            <div class="col-lg-8">
                <div class="tab-content">
                    <!-- Tab Detalhes -->
                    <div class="tab-pane fade show active" id="detalhes">
                        
                        <!-- Main Image Card -->
                        <?php if ($company['imagem_detalhes'] || $company['logo']): ?>
                        <div class="card mb-4" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px; overflow: hidden;">
                            <div class="card-body p-0">
                                <?php if ($company['imagem_detalhes']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($company['imagem_detalhes']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" 
                                         style="width: 100%; height: 400px; object-fit: cover;">
                                <?php elseif ($company['logo']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" 
                                         style="width: 100%; height: 400px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Como Funciona Card -->
                        <div class="card mb-4" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px;">
                            <div class="card-body" style="padding: 30px;">
                                <h3 class="card-title" style="color: #012d6a; font-weight: 700; margin-bottom: 25px; font-size: 1.5rem;">
                                    Como funciona:
                                </h3>
                                <div class="how-it-works">
                                    <div class="step-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                        <span class="step-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">1</span>
                                        <span class="step-text" style="font-size: 1rem; line-height: 1.6;">Clique no botão "USAR BENEFÍCIO" ou faça login primeiro</span>
                                    </div>
                                    <div class="step-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                        <span class="step-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">2</span>
                                        <span class="step-text" style="font-size: 1rem; line-height: 1.6;">Gere seu cupom de desconto exclusivo ANETI</span>
                                    </div>
                                    <div class="step-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                        <span class="step-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">3</span>
                                        <span class="step-text" style="font-size: 1rem; line-height: 1.6;">Apresente o cupom à empresa parceira</span>
                                    </div>
                                    <div class="step-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                        <span class="step-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">4</span>
                                        <span class="step-text" style="font-size: 1rem; line-height: 1.6;">Aproveite seu desconto exclusivo para membros ANETI</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Regulamento Card -->
                        <div class="card mb-4" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px;">
                            <div class="card-body" style="padding: 30px;">
                                <h3 class="card-title" style="color: #012d6a; font-weight: 700; margin-bottom: 25px; font-size: 1.5rem;">
                                    <i class="fas fa-file-contract me-2"></i>Regulamento
                                </h3>
                                <div class="regulation-content" style="font-size: 1rem; line-height: 1.7; color: #495057;">
                                    <?php if ($company['regras']): ?>
                                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #012d6a;">
                                            <?php echo nl2br(htmlspecialchars($company['regras'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="regulation-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                            <span class="regulation-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">1</span>
                                            <span class="regulation-text">Desconto válido conforme período determinado.</span>
                                        </div>
                                        <div class="regulation-item d-flex align-items-start mb-3" style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                                            <span class="regulation-number" style="background: #012d6a; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; flex-shrink: 0;">2</span>
                                            <span class="regulation-text">Os descontos podem variar a cada mês.</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tab Avaliações -->
                    <div class="tab-pane fade" id="avaliacoes">
                        <!-- Reviews Content Here -->
                        <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px;">
                            <div class="card-body" style="padding: 30px;">
                                <h3 class="card-title" style="color: #012d6a; font-weight: 700; margin-bottom: 25px; font-size: 1.5rem;">
                                    <i class="fas fa-star me-2"></i>Avaliações dos Usuários
                                </h3>
                                <?php if (!empty($reviews)): ?>
                                    <!-- Reviews will be displayed here -->
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Ainda não há avaliações</h5>
                                        <p class="text-muted">Seja o primeiro a avaliar esta empresa!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Company Info Card -->
                <div class="card mb-4" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5 class="card-title" style="color: #012d6a; font-weight: 700; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2"></i>Informações
                        </h5>
                        
                        <div class="company-info-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Localização</strong>
                            </div>
                            <p class="text-muted mb-0" style="margin-left: 24px;">
                                <?php if ($company['endereco'] && trim($company['endereco'])): ?>
                                    <?php echo htmlspecialchars($company['endereco']); ?><br>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?>
                            </p>
                        </div>

                        <?php if ($company['telefone']): ?>
                        <div class="company-info-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-success me-2"></i>
                                <strong>Telefone</strong>
                            </div>
                            <p class="text-muted mb-0" style="margin-left: 24px;">
                                <a href="tel:<?php echo htmlspecialchars($company['telefone']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($company['telefone']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if ($company['email']): ?>
                        <div class="company-info-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-info me-2"></i>
                                <strong>E-mail</strong>
                            </div>
                            <p class="text-muted mb-0" style="margin-left: 24px;">
                                <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($company['email']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if ($company['website']): ?>
                        <div class="company-info-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-globe text-warning me-2"></i>
                                <strong>Website</strong>
                            </div>
                            <p class="text-muted mb-0" style="margin-left: 24px;">
                                <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="text-decoration-none">
                                    Visitar site <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Rating Summary Card -->
                <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 15px;">
                    <div class="card-body" style="padding: 30px;">
                        <h5 class="card-title" style="color: #012d6a; font-weight: 700; margin-bottom: 20px;">
                            <i class="fas fa-chart-bar me-2"></i>Avaliação Geral
                        </h5>
                        
                        <div class="text-center mb-4">
                            <div style="font-size: 3rem; font-weight: 700; color: #012d6a; line-height: 1;">
                                <?php echo $avg_rating; ?>
                            </div>
                            <div class="rating-stars mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $avg_rating ? 'text-warning' : 'text-muted'; ?>" style="font-size: 1.5rem;"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted mb-0"><?php echo $rating_summary['total']; ?> avaliações</p>
                        </div>

                        <?php if ($rating_summary['total'] > 0): ?>
                        <div class="rating-breakdown">
                            <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                                <?php $count = $rating_summary["star{$stars}"] ?: 0; ?>
                                <?php $percentage = $rating_summary['total'] > 0 ? round(($count / $rating_summary['total']) * 100) : 0; ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2" style="width: 20px; font-size: 0.9rem;"><?php echo $stars; ?>★</span>
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.85rem; width: 40px;"><?php echo $count; ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Tab Active State Update
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
            
            tabLinks.forEach(function(tabLink) {
                tabLink.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabLinks.forEach(function(link) {
                        link.style.color = '#6c757d';
                    });
                    
                    // Add active class to clicked tab
                    this.style.color = '#012d6a';
                });
            });
        });

        // Action Buttons Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Traçar Rota
            const routeBtn = document.querySelector('.btn-outline-primary');
            if (routeBtn) {
                routeBtn.addEventListener('click', function() {
                    const endereco = "<?php echo htmlspecialchars($company['endereco'] ?? ''); ?>";
                    const cidade = "<?php echo htmlspecialchars($company['cidade'] ?? ''); ?>";
                    const estado = "<?php echo htmlspecialchars($company['estado'] ?? ''); ?>";
                    
                    const address = endereco ? `${endereco}, ${cidade}, ${estado}` : `${cidade}, ${estado}`;
                    const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
                    
                    window.open(mapsUrl, '_blank');
                });
            }

            // Salvar Benefício
            const saveBtn = document.querySelector('.btn-outline-danger');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    const benefitId = '<?php echo $company['id']; ?>';
                    let savedBenefits = JSON.parse(localStorage.getItem('savedBenefits') || '[]');
                    
                    if (savedBenefits.includes(benefitId)) {
                        // Remove from saved
                        savedBenefits = savedBenefits.filter(id => id !== benefitId);
                        this.innerHTML = '<i class="fas fa-heart me-1"></i>Salvar este benefício';
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                    } else {
                        // Add to saved
                        savedBenefits.push(benefitId);
                        this.innerHTML = '<i class="fas fa-heart me-1"></i>Benefício salvo';
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                    }
                    
                    localStorage.setItem('savedBenefits', JSON.stringify(savedBenefits));
                });
            }

            // Compartilhar
            const shareBtn = document.querySelector('.btn-outline-info');
            if (shareBtn) {
                shareBtn.addEventListener('click', function() {
                    if (navigator.share) {
                        navigator.share({
                            title: "<?php echo htmlspecialchars($company['nome']); ?>",
                            text: "Confira este benefício no Clube de Vantagens ANETI!",
                            url: window.location.href
                        });
                    } else {
                        // Fallback para navegadores que não suportam Web Share API
                        const url = window.location.href;
                        navigator.clipboard.writeText(url).then(() => {
                            alert('Link copiado para a área de transferência!');
                        });
                    }
                });
            }

            // Avaliar Parceiro
            const reviewBtn = document.querySelector('.btn-outline-warning');
            if (reviewBtn) {
                reviewBtn.addEventListener('click', function() {
                    const reviewsTab = document.querySelector('a[href="#avaliacoes"]');
                    reviewsTab.click();
                    
                    setTimeout(() => {
                        reviewsTab.scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                });
            }

            // Reportar Problema
            const reportBtn = document.querySelector('.btn-outline-secondary');
            if (reportBtn) {
                reportBtn.addEventListener('click', function() {
                    const subject = `Problema reportado - ${encodeURIComponent("<?php echo htmlspecialchars($company['nome']); ?>")}`;
                    const body = `Gostaria de reportar um problema com a empresa: ${encodeURIComponent("<?php echo htmlspecialchars($company['nome']); ?>")}\n\nDescreva o problema:\n\n`;
                    
                    window.location.href = `mailto:suporte@aneti.org.br?subject=${subject}&body=${body}`;
                });
            }

            // Initialize saved state
            const benefitId = '<?php echo $company['id']; ?>';
            const savedBenefits = JSON.parse(localStorage.getItem('savedBenefits') || '[]');
            
            if (savedBenefits.includes(benefitId)) {
                const saveBtn = document.querySelector('.btn-outline-danger');
                if (saveBtn) {
                    saveBtn.innerHTML = '<i class="fas fa-heart me-1"></i>Benefício salvo';
                    saveBtn.classList.remove('btn-outline-danger');
                    saveBtn.classList.add('btn-danger');
                }
            }
        });
    </script>

</body>
</html>