<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('../index.php');
}

// Handle review submission
if ($_POST && isset($_POST['rating'])) {
    $usuario_nome = sanitizeInput($_POST['usuario_nome']);
    $usuario_email = sanitizeInput($_POST['usuario_email']);
    $rating = intval($_POST['rating']);
    $comentario = sanitizeInput($_POST['comentario']);
    
    if ($usuario_nome && $rating >= 1 && $rating <= 5) {
        try {
            $stmt = $conn->prepare("INSERT INTO avaliacoes (empresa_id, usuario_nome, usuario_email, rating, comentario, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$id, $usuario_nome, $usuario_email, $rating, $comentario]);
            
            // Update company average rating
            $stmt = $conn->prepare("
                UPDATE empresas SET 
                    avaliacao_media = (SELECT AVG(rating) FROM avaliacoes WHERE empresa_id = ?),
                    total_avaliacoes = (SELECT COUNT(*) FROM avaliacoes WHERE empresa_id = ?)
                WHERE id = ?
            ");
            $stmt->execute([$id, $id, $id]);
            
            // Redirect to show the new review
            header("Location: empresa-detalhes.php?id=" . $id . "&tab=avaliacoes&success=1");
            exit;
        } catch (Exception $e) {
            $error_message = "Erro ao salvar avaliação.";
        }
    }
}

$company = getCompanyById($conn, $id);

if (!$company) {
    redirect('../index.php');
}

// Buscar avaliações reais da empresa
$reviews = [];
$rating_summary = ['media' => 0, 'total' => 0, 'star5' => 0, 'star4' => 0, 'star3' => 0, 'star2' => 0, 'star1' => 0];

try {
    $stmt = $conn->prepare("SELECT * FROM avaliacoes WHERE empresa_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$id]);
    $reviews = $stmt->fetchAll();
    
    $stmt = $conn->prepare("
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
    $rating_summary = $stmt->fetch() ?: ['media' => 0, 'total' => 0, 'star5' => 0, 'star4' => 0, 'star3' => 0, 'star2' => 0, 'star1' => 0];
} catch (Exception $e) {
    // Em caso de erro, manter arrays vazios
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

    <!-- Benefit Header -->
    <div class="benefit-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php if ($company['logo']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="benefit-logo">
                    <?php else: ?>
                        <div class="benefit-logo-placeholder">
                            <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-10">
                    <div class="benefit-header-content">
                        <h1 class="benefit-title"><?php echo htmlspecialchars($company['nome']); ?></h1>
                        <div class="benefit-rating mb-2">
                            <?php
                            $avg_rating = $rating_summary['media'] ? round($rating_summary['media'], 1) : 0;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg_rating) {
                                    echo '<span class="star filled">★</span>';
                                } else {
                                    echo '<span class="star">★</span>';
                                }
                            }
                            ?>
                            <span class="rating-text"><?php echo $avg_rating; ?></span>
                        </div>
                        <p class="benefit-category"><?php echo htmlspecialchars($company['categoria']); ?></p>
                        <p class="benefit-discount"><?php echo $company['desconto'] ? $company['desconto'] . '% de desconto' : 'Desconto especial para membros ANETI'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefit Tabs -->
    <div class="benefit-tabs">
        <div class="container">
            <ul class="nav nav-tabs benefit-nav">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#detalhes">Detalhes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#avaliacoes">Avaliações 
                        <span class="badge bg-secondary"><?php echo $rating_summary['total']; ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Benefit Actions -->
    <div class="bg-light py-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center" onclick="openRoute()">
                            <i class="fas fa-route me-2"></i>
                            <span class="d-none d-sm-inline">Traçar Rota</span>
                            <span class="d-sm-none">Rota</span>
                        </button>
                        <button class="btn btn-outline-danger btn-sm d-flex align-items-center" onclick="toggleSaveBenefit()" id="saveBenefitBtn">
                            <i class="fas fa-heart me-2" id="saveIcon"></i>
                            <span id="saveText" class="d-none d-sm-inline">Salvar benefício</span>
                            <span class="d-sm-none">Salvar</span>
                        </button>
                        <button class="btn btn-outline-info btn-sm d-flex align-items-center" onclick="shareCompany()">
                            <i class="fas fa-share me-2"></i>
                            <span class="d-none d-sm-inline">Compartilhar</span>
                            <span class="d-sm-none">Share</span>
                        </button>
                        <button class="btn btn-outline-warning btn-sm d-flex align-items-center" onclick="scrollToReviews()">
                            <i class="fas fa-star me-2"></i>
                            <span class="d-none d-sm-inline">Avaliar parceiro</span>
                            <span class="d-sm-none">Avaliar</span>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center" onclick="reportProblem()">
                            <i class="fas fa-flag me-2"></i>
                            <span class="d-none d-sm-inline">Reportar problema</span>
                            <span class="d-sm-none">Reportar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="tab-content">
                    <!-- Tab Detalhes -->
                    <div class="tab-pane fade show active" id="detalhes">
                        <!-- Main Image Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="benefit-main-image">
                                <?php if ($company['imagem_detalhes']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($company['imagem_detalhes']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="img-fluid card-img-top" style="height: 300px; object-fit: cover; border-radius: 0.375rem 0.375rem 0 0;">
                                <?php elseif ($company['logo']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="img-fluid card-img-top" style="height: 300px; object-fit: cover; border-radius: 0.375rem 0.375rem 0 0;">
                                <?php else: ?>
                                    <div class="benefit-placeholder-image d-flex align-items-center justify-content-center" style="height: 300px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 0.375rem 0.375rem 0 0;">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p class="mb-0">Imagem do <?php echo htmlspecialchars($company['nome']); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Como Funciona Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Como funciona:
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 bg-light rounded">
                                            <div class="step-number-badge me-3">1</div>
                                            <div>
                                                <h6 class="mb-1">Clique no botão USAR</h6>
                                                <small class="text-muted">Na página do parceiro, faça login ou cadastre-se</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 bg-light rounded">
                                            <div class="step-number-badge me-3">2</div>
                                            <div>
                                                <h6 class="mb-1">Gere seu cupom</h6>
                                                <small class="text-muted">Seu cupom de desconto será gerado automaticamente</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 bg-light rounded">
                                            <div class="step-number-badge me-3">3</div>
                                            <div>
                                                <h6 class="mb-1">Apresente o cupom</h6>
                                                <small class="text-muted">Mostre o cupom na empresa parceira</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start p-3 bg-light rounded">
                                            <div class="step-number-badge me-3">4</div>
                                            <div>
                                                <h6 class="mb-1">Aproveite o desconto</h6>
                                                <small class="text-muted">Desfrute do seu benefício exclusivo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Regulamento Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-contract text-primary me-2"></i>
                                    Regulamento
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($company['regras']): ?>
                                    <div class="regulation-content">
                                        <?php echo nl2br(htmlspecialchars($company['regras'])); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="regulation-content">
                                        <div class="d-flex mb-2">
                                            <span class="badge bg-primary me-2">1</span>
                                            <span>Desconto válido conforme período determinado.</span>
                                        </div>
                                        <div class="d-flex mb-0">
                                            <span class="badge bg-primary me-2">2</span>
                                            <span>Os descontos podem variar a cada mês.</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Localização Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    Localização
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="location-map">
                                            <div id="map" style="height: 350px; width: 100%; border-radius: 8px; background: #f8f9fa; margin-bottom: 15px;"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="location-info">
                                            <h6 class="text-primary mb-3">Informações de Contato</h6>
                                            
                                            <div class="contact-item mb-3">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                                    <div>
                                                        <strong>Endereço:</strong><br>
                                                        <?php if ($company['endereco'] && trim($company['endereco'])): ?>
                                                            <?php echo htmlspecialchars($company['endereco']); ?><br>
                                                        <?php endif; ?>
                                                        <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if ($company['telefone']): ?>
                                            <div class="contact-item mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-phone text-muted me-2"></i>
                                                    <span><?php echo htmlspecialchars($company['telefone']); ?></span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($company['email']): ?>
                                            <div class="contact-item mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-envelope text-muted me-2"></i>
                                                    <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>" class="text-decoration-none">
                                                        <?php echo htmlspecialchars($company['email']); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($company['website']): ?>
                                            <div class="contact-item mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-globe text-muted me-2"></i>
                                                    <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="text-decoration-none">
                                                        Site da empresa
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-outline-primary btn-sm w-100" onclick="openRoute()">
                                                <i class="fas fa-directions me-2"></i>Como chegar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Avaliações -->
                    <div class="tab-pane fade" id="avaliacoes">
                        <!-- Rating Summary Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-star text-primary me-2"></i>
                                    Avaliações dos Usuários
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <div class="overall-rating p-4">
                                            <div class="rating-circle bg-light rounded-circle d-flex flex-column align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                                                <span class="rating-number display-4 fw-bold text-primary"><?php echo $avg_rating; ?></span>
                                                <div class="rating-stars">
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $avg_rating) {
                                                            echo '<i class="fas fa-star text-warning"></i>';
                                                        } else {
                                                            echo '<i class="far fa-star text-muted"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0"><?php echo $rating_summary['total']; ?> avaliações</p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="rating-breakdown">
                                            <?php
                                            $total = $rating_summary['total'];
                                            for ($star = 5; $star >= 1; $star--) {
                                                $count = $rating_summary["star$star"];
                                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                            ?>
                                            <div class="rating-bar d-flex align-items-center mb-2">
                                                <span class="bar-label me-2" style="min-width: 80px;">
                                                    <?php echo $star; ?> estrelas
                                                </span>
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="bar-count badge bg-light text-dark"><?php echo $count; ?></span>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews List Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-comments text-primary me-2"></i>
                                    Avaliações Recentes
                                </h6>
                                <span class="badge bg-primary"><?php echo count($reviews); ?> avaliações</span>
                            </div>
                            <div class="card-body">
                                <div class="reviews-list">
                                    <?php if (count($reviews) > 0): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <div class="review-item border-bottom pb-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="reviewer-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                                            <?php echo strtoupper(substr($review['usuario_nome'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <h6 class="reviewer-name mb-1"><?php echo htmlspecialchars($review['usuario_nome']); ?></h6>
                                                            <div class="review-rating mb-1">
                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="review-date text-muted small"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                                                </div>
                                                <?php if ($review['comentario']): ?>
                                                    <p class="review-comment mb-0 ms-5 ps-2"><?php echo htmlspecialchars($review['comentario']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-reviews text-center py-4">
                                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Ainda não há avaliações para esta empresa.<br>Seja o primeiro a avaliar!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Add Review Form Card -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-plus-circle text-primary me-2"></i>
                                    Deixe sua avaliação
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_GET['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Avaliação adicionada com sucesso!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?php echo $error_message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="review-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="usuario_nome" class="form-label fw-semibold">
                                                <i class="fas fa-user me-1"></i>
                                                Seu nome *
                                            </label>
                                            <input type="text" class="form-control" id="usuario_nome" name="usuario_nome" required placeholder="Digite seu nome">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="usuario_email" class="form-label fw-semibold">
                                                <i class="fas fa-envelope me-1"></i>
                                                Seu email (opcional)
                                            </label>
                                            <input type="email" class="form-control" id="usuario_email" name="usuario_email" placeholder="seu@email.com">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold mb-2">
                                            <i class="fas fa-star me-1"></i>
                                            Sua avaliação *
                                        </label>
                                        <div class="rating-input d-flex gap-1">
                                            <input type="radio" name="rating" value="5" id="star5" required class="d-none">
                                            <label for="star5" class="rating-star fs-4" data-rating="5">★</label>
                                            <input type="radio" name="rating" value="4" id="star4" required class="d-none">
                                            <label for="star4" class="rating-star fs-4" data-rating="4">★</label>
                                            <input type="radio" name="rating" value="3" id="star3" required class="d-none">
                                            <label for="star3" class="rating-star fs-4" data-rating="3">★</label>
                                            <input type="radio" name="rating" value="2" id="star2" required class="d-none">
                                            <label for="star2" class="rating-star fs-4" data-rating="2">★</label>
                                            <input type="radio" name="rating" value="1" id="star1" required class="d-none">
                                            <label for="star1" class="rating-star fs-4" data-rating="1">★</label>
                                        </div>
                                        <small class="text-muted">Clique para avaliar</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="comentario" class="form-label fw-semibold">
                                            <i class="fas fa-comment me-1"></i>
                                            Comentário
                                        </label>
                                        <textarea class="form-control" name="comentario" rows="4" placeholder="Conte como foi sua experiência com esta empresa..."></textarea>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Enviar Avaliação
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Main Action Card -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 160px;">
                    <div class="card-body text-center p-4">
                        <div class="company-mini-logo mb-3">
                            <?php if ($company['logo']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e9ecef;">
                            <?php else: ?>
                                <div class="logo-placeholder-mini bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold; margin: 0 auto;">
                                    <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="text-primary mb-2"><?php echo htmlspecialchars($company['nome']); ?></h5>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($company['categoria']); ?></p>
                        
                        <?php if ($company['desconto']): ?>
                            <div class="discount-badge mb-3">
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-percentage me-1"></i>
                                    <?php echo $company['desconto']; ?>% de desconto
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                            <a href="gerar-cupom.php?empresa=<?php echo $company['id']; ?>" class="btn btn-primary btn-lg w-100 shadow-sm mb-3">
                                <i class="fas fa-ticket-alt me-2"></i>
                                USAR BENEFÍCIO
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary btn-lg w-100 shadow-sm mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                FAZER LOGIN
                            </a>
                        <?php endif; ?>
                        
                        <!-- Rating display -->
                        <div class="rating-display mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="stars me-2">
                                    <?php
                                    $avg_rating = $rating_summary['media'] ? round($rating_summary['media'], 1) : 0;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $avg_rating) {
                                            echo '<i class="fas fa-star text-warning"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-muted"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="text-muted small">
                                    <?php echo $avg_rating; ?> (<?php echo $rating_summary['total']; ?> avaliações)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Company Info Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Sobre a Empresa
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if ($company['descricao']): ?>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($company['descricao'])); ?></p>
                        <?php else: ?>
                            <p class="mb-0">Aproveite os benefícios exclusivos do <?php echo htmlspecialchars($company['nome']); ?>! Uma experiência única que oferece descontos especiais para membros do Clube de Vantagens da ANETI.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Contact Info Card -->
                <?php if ($company['website'] || $company['telefone'] || $company['email']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-address-book text-primary me-2"></i>
                            Contato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="contact-list">
                            <?php if ($company['website']): ?>
                                <div class="contact-item d-flex align-items-center mb-2">
                                    <i class="fas fa-globe text-muted me-3" style="width: 20px;"></i>
                                    <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" class="text-decoration-none flex-grow-1">
                                        Site da empresa
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($company['telefone']): ?>
                                <div class="contact-item d-flex align-items-center mb-2">
                                    <i class="fas fa-phone text-muted me-3" style="width: 20px;"></i>
                                    <a href="tel:<?php echo htmlspecialchars($company['telefone']); ?>" class="text-decoration-none flex-grow-1">
                                        <?php echo htmlspecialchars($company['telefone']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($company['email']): ?>
                                <div class="contact-item d-flex align-items-center mb-0">
                                    <i class="fas fa-envelope text-muted me-3" style="width: 20px;"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>" class="text-decoration-none flex-grow-1">
                                        <?php echo htmlspecialchars($company['email']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet Maps (OpenStreetMap) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dados da empresa do PHP
            const companyName = <?php echo json_encode($company['nome']); ?>;
            const companyCity = <?php echo json_encode($company['cidade']); ?>;
            const companyState = <?php echo json_encode($company['estado']); ?>;
            const companyAddress = <?php echo json_encode($company['endereco'] ?? ''); ?>;
            
            // Endereço completo para geocoding
            let fullAddress = companyCity + ', ' + companyState + ', Brasil';
            if (companyAddress && companyAddress.trim()) {
                fullAddress = companyAddress + ', ' + fullAddress;
            }
            
            // Inicializar mapa centrado no Brasil
            const map = L.map('map').setView([-15.7942, -47.8822], 5);
            
            // Adicionar tiles do OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Função para geocoding usando Nominatim (OpenStreetMap)
            function geocodeAddress(address) {
                const encodedAddress = encodeURIComponent(address);
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodedAddress}&limit=1`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lon = parseFloat(result.lon);
                            
                            // Centralizar mapa na localização encontrada
                            map.setView([lat, lon], 15);
                            
                            // Criar ícone personalizado ANETI
                            const customIcon = L.divIcon({
                                className: 'custom-marker',
                                html: `
                                    <div style="
                                        width: 30px; 
                                        height: 30px; 
                                        background: #012d6a; 
                                        border: 3px solid white; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center;
                                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                                    ">
                                        <div style="
                                            width: 12px; 
                                            height: 12px; 
                                            background: white; 
                                            border-radius: 50%;
                                        "></div>
                                    </div>
                                `,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                            
                            // Adicionar marcador
                            const marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);
                            
                            // Popup com informações da empresa
                            const popupContent = `
                                <div style="max-width: 250px;">
                                    <h6 style="margin: 0 0 8px 0; color: #012d6a; font-weight: bold;">${companyName}</h6>
                                    <p style="margin: 0; font-size: 14px;">${result.display_name}</p>
                                    ${companyAddress ? `<p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">${companyAddress}</p>` : ''}
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                            
                            // Abrir popup automaticamente após 1 segundo
                            setTimeout(() => {
                                marker.openPopup();
                            }, 1000);
                            
                        } else {
                            // Tentar geocoding apenas com cidade e estado
                            fallbackGeocode();
                        }
                    })
                    .catch(error => {
                        console.log('Erro no geocoding:', error);
                        fallbackGeocode();
                    });
            }
            
            function fallbackGeocode() {
                const fallbackAddress = companyCity + ', ' + companyState + ', Brasil';
                const encodedAddress = encodeURIComponent(fallbackAddress);
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodedAddress}&limit=1`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lon = parseFloat(result.lon);
                            
                            map.setView([lat, lon], 12);
                            
                            const marker = L.marker([lat, lon]).addTo(map);
                            const popupContent = `
                                <div style="max-width: 200px;">
                                    <h6 style="margin: 0 0 8px 0; color: #012d6a; font-weight: bold;">${companyName}</h6>
                                    <p style="margin: 0; font-size: 14px;">${companyCity}, ${companyState}</p>
                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Localização aproximada</p>
                                </div>
                            `;
                            marker.bindPopup(popupContent);
                        } else {
                            showMapError();
                        }
                    })
                    .catch(error => {
                        console.log('Erro no fallback geocoding:', error);
                        showMapError();
                    });
            }
            
            function showMapError() {
                document.getElementById('map').innerHTML = `
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                        <div style="text-align: center; color: #666;">
                            <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                            <p style="margin: 8px 0 4px 0;">Localização não encontrada</p>
                            <small>${companyCity}, ${companyState}</small>
                        </div>
                    </div>
                `;
            }
            
            // Iniciar geocoding
            geocodeAddress(fullAddress);
            
            // Inicializar funcionalidades dos botões de ação
            checkIfSaved();
        });
        
        // FUNCIONALIDADES DA PÁGINA DE DETALHES
        
        // Rating system for review form
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                const allStars = document.querySelectorAll('.rating-star');
                
                // Update visual feedback
                allStars.forEach((s, index) => {
                    if (index >= (5 - rating)) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
                
                // Set the radio button value
                document.getElementById('star' + rating).checked = true;
            });
            
            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                const allStars = document.querySelectorAll('.rating-star');
                
                allStars.forEach((s, index) => {
                    if (index >= (5 - rating)) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        // Reset hover effect
        document.querySelector('.rating-input').addEventListener('mouseleave', function() {
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            const allStars = document.querySelectorAll('.rating-star');
            
            if (checkedRating) {
                const rating = checkedRating.value;
                allStars.forEach((s, index) => {
                    if (index >= (5 - rating)) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            } else {
                allStars.forEach(s => s.style.color = '#ddd');
            }
        });
        
        // FUNCIONALIDADES DOS BOTÕES DE AÇÃO
        
        // 1. Traçar Rota - Abrir no Google Maps/Waze
        function openRoute() {
            const companyName = <?php echo json_encode($company['nome']); ?>;
            const companyCity = <?php echo json_encode($company['cidade']); ?>;
            const companyState = <?php echo json_encode($company['estado']); ?>;
            const companyAddress = <?php echo json_encode($company['endereco'] ?? ''); ?>;
            
            let destination = companyCity + ', ' + companyState + ', Brasil';
            if (companyAddress && companyAddress.trim()) {
                destination = companyAddress + ', ' + destination;
            }
            
            // Detectar se é mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // Mobile - tentar Waze primeiro, depois Google Maps
                const wazeUrl = `https://waze.com/ul?q=${encodeURIComponent(destination)}`;
                const googleMapsUrl = `https://maps.google.com/maps?daddr=${encodeURIComponent(destination)}`;
                
                // Mostrar opções para o usuário
                if (confirm('Escolha o aplicativo:\\n- OK para Google Maps\\n- Cancelar para Waze')) {
                    window.open(googleMapsUrl, '_blank');
                } else {
                    window.open(wazeUrl, '_blank');
                }
            } else {
                // Desktop - abrir Google Maps
                const googleMapsUrl = `https://maps.google.com/maps?daddr=${encodeURIComponent(destination)}`;
                window.open(googleMapsUrl, '_blank');
            }
            
            showNotification('Abrindo rota no mapa...', 'info');
        }
        
        // 2. Salvar Benefício - LocalStorage
        function toggleSaveBenefit() {
            const companyId = <?php echo $company['id']; ?>;
            const companyName = <?php echo json_encode($company['nome']); ?>;
            
            let saved = JSON.parse(localStorage.getItem('savedBenefits') || '[]');
            const isAlreadySaved = saved.some(item => item.id === companyId);
            
            const saveBtn = document.getElementById('saveBenefitBtn');
            const saveIcon = document.getElementById('saveIcon');
            const saveText = document.getElementById('saveText');
            
            if (isAlreadySaved) {
                // Remover dos salvos
                saved = saved.filter(item => item.id !== companyId);
                localStorage.setItem('savedBenefits', JSON.stringify(saved));
                
                saveIcon.className = 'fas fa-heart';
                saveText.textContent = 'Salvar este benefício';
                saveBtn.style.color = '#666';
                
                showNotification('Benefício removido dos salvos', 'info');
            } else {
                // Adicionar aos salvos
                saved.push({
                    id: companyId,
                    name: companyName,
                    savedAt: new Date().toISOString()
                });
                localStorage.setItem('savedBenefits', JSON.stringify(saved));
                
                saveIcon.className = 'fas fa-heart';
                saveText.textContent = 'Benefício salvo';
                saveBtn.style.color = '#e74c3c';
                
                showNotification('Benefício salvo com sucesso!', 'success');
            }
        }
        
        // Verificar se já está salvo ao carregar a página
        function checkIfSaved() {
            const companyId = <?php echo $company['id']; ?>;
            const saved = JSON.parse(localStorage.getItem('savedBenefits') || '[]');
            const isAlreadySaved = saved.some(item => item.id === companyId);
            
            if (isAlreadySaved) {
                const saveBtn = document.getElementById('saveBenefitBtn');
                const saveIcon = document.getElementById('saveIcon');
                const saveText = document.getElementById('saveText');
                
                saveIcon.className = 'fas fa-heart';
                saveText.textContent = 'Benefício salvo';
                saveBtn.style.color = '#e74c3c';
            }
        }
        
        // 3. Compartilhar
        function shareCompany() {
            const companyName = <?php echo json_encode($company['nome']); ?>;
            const currentUrl = window.location.href;
            const shareText = `Confira este benefício exclusivo: ${companyName} - Clube de Vantagens ANETI`;
            
            if (navigator.share) {
                // Web Share API (mobile)
                navigator.share({
                    title: companyName,
                    text: shareText,
                    url: currentUrl
                }).then(() => {
                    showNotification('Compartilhado com sucesso!', 'success');
                }).catch(err => {
                    fallbackShare(shareText, currentUrl);
                });
            } else {
                fallbackShare(shareText, currentUrl);
            }
        }
        
        function fallbackShare(text, url) {
            // Copiar link para clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Link copiado para área de transferência!', 'success');
                });
            } else {
                // Fallback para navegadores mais antigos
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showNotification('Link copiado para área de transferência!', 'success');
                } catch (err) {
                    showNotification('Não foi possível copiar o link', 'error');
                }
                document.body.removeChild(textArea);
            }
        }
        
        // 4. Avaliar - Scroll para seção de avaliações
        function scrollToReviews() {
            // Ativar a aba de avaliações
            const reviewTab = document.querySelector('a[href="#avaliacoes"]');
            const reviewTabPane = document.getElementById('avaliacoes');
            
            if (reviewTab) {
                // Ativar a aba
                const currentActiveTab = document.querySelector('.nav-link.active');
                const currentActivePane = document.querySelector('.tab-pane.show.active');
                
                if (currentActiveTab) currentActiveTab.classList.remove('active');
                if (currentActivePane) {
                    currentActivePane.classList.remove('show', 'active');
                }
                
                reviewTab.classList.add('active');
                reviewTabPane.classList.add('show', 'active');
                
                // Scroll suave para a seção
                setTimeout(() => {
                    const reviewForm = document.querySelector('.add-review-form');
                    if (reviewForm) {
                        reviewForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 200);
            }
        }
        
        // 5. Reportar Problema
        function reportProblem() {
            const companyName = <?php echo json_encode($company['nome']); ?>;
            const companyId = <?php echo $company['id']; ?>;
            
            const problems = [
                'Informações incorretas',
                'Empresa fechada/inexistente', 
                'Desconto não é válido',
                'Atendimento inadequado',
                'Outros problemas'
            ];
            
            let problemOptions = problems.map((problem, index) => 
                `${index + 1}. ${problem}`
            ).join('\\n');
            
            const selectedProblem = prompt(
                `Reportar problema com ${companyName}:\\n\\n${problemOptions}\\n\\nDigite o número da opção (1-5) ou descreva o problema:`
            );
            
            if (selectedProblem && selectedProblem.trim()) {
                // Simular envio do report
                showNotification('Problema reportado com sucesso! Obrigado pelo feedback.', 'success');
                
                // Salvar no localStorage para demonstração
                const reports = JSON.parse(localStorage.getItem('companyReports') || '[]');
                reports.push({
                    companyId: companyId,
                    companyName: companyName,
                    problem: selectedProblem,
                    reportedAt: new Date().toISOString()
                });
                localStorage.setItem('companyReports', JSON.stringify(reports));
            }
        }
        
        // Sistema de notificações
        function showNotification(message, type = 'info') {
            // Remover notificação existente
            const existingNotification = document.querySelector('.notification-toast');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Criar nova notificação
            const notification = document.createElement('div');
            notification.className = `notification-toast ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Estilos da notificação
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 10000;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                max-width: 300px;
                font-size: 14px;
            `;
            
            notification.querySelector('.notification-content').style.cssText = `
                display: flex;
                align-items: center;
                gap: 8px;
            `;
            
            document.body.appendChild(notification);
            
            // Animar entrada
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remover após 3 segundos
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>