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
    <div class="benefit-actions">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="action-buttons">
                        <button class="btn-action-icon">
                            <i class="fas fa-route"></i>
                            <span>Traçar Rota</span>
                        </button>
                        <button class="btn-action-icon">
                            <i class="fas fa-heart"></i>
                            <span>Salvar este benefício</span>
                        </button>
                        <button class="btn-action-icon">
                            <i class="fas fa-share"></i>
                            <span>Compartilhar</span>
                        </button>
                        <button class="btn-action-icon">
                            <i class="fas fa-star"></i>
                            <span>Avaliar este parceiro</span>
                        </button>
                        <button class="btn-action-icon">
                            <i class="fas fa-flag"></i>
                            <span>Reportar um problema</span>
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
                        <!-- Main Image -->
                        <div class="benefit-main-image mb-4">
                            <?php if ($company['imagem_detalhes']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($company['imagem_detalhes']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="img-fluid">
                            <?php elseif ($company['logo']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="img-fluid">
                            <?php else: ?>
                                <div class="benefit-placeholder-image">
                                    <i class="fas fa-image fa-3x mb-3"></i>
                                    <p>Imagem do <?php echo htmlspecialchars($company['nome']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Como Funciona -->
                        <div class="benefit-section mb-4">
                            <h3 class="benefit-section-title">Como funciona:</h3>
                            <div class="how-it-works">
                                <div class="step">
                                    <span class="step-number">1)</span>
                                    <span class="step-text">Clique no botão USAR</span>
                                </div>
                                <div class="step">
                                    <span class="step-number">2)</span>
                                    <span class="step-text">Faça seu login e gere seu cupom</span>
                                </div>
                                <div class="step">
                                    <span class="step-number">3)</span>
                                    <span class="step-text">Apresente o cupom à empresa parceira</span>
                                </div>
                                <div class="step">
                                    <span class="step-number">4)</span>
                                    <span class="step-text">Aproveite seu desconto exclusivo</span>
                                </div>
                            </div>
                        </div>

                        <!-- Regulamento -->
                        <div class="benefit-section mb-4">
                            <h3 class="benefit-section-title">
                                <i class="fas fa-file-contract"></i> Regulamento
                            </h3>
                            <div class="regulation-content">
                                <?php if ($company['regras']): ?>
                                    <p><?php echo nl2br(htmlspecialchars($company['regras'])); ?></p>
                                <?php else: ?>
                                    <div class="regulation-item">
                                        <span class="regulation-number">1)</span>
                                        <span class="regulation-text">Desconto válido conforme período determinado.</span>
                                    </div>
                                    <div class="regulation-item">
                                        <span class="regulation-number">2)</span>
                                        <span class="regulation-text">Os descontos podem variar a cada mês.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Localização -->
                        <div class="benefit-section mb-4">
                            <h3 class="benefit-section-title">
                                <i class="fas fa-map-marker-alt"></i> Localização
                            </h3>
                            <div class="location-section">
                                <div class="location-map">
                                    <div class="map-placeholder">
                                        <i class="fas fa-map fa-2x mb-2"></i>
                                        <p>Mapa interativo</p>
                                        <div class="map-controls">
                                            <button class="map-control">+</button>
                                            <button class="map-control">-</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="location-info mt-3">
                                    <p><strong>Endereço:</strong></p>
                                    <p><?php echo htmlspecialchars($company['endereco'] ?? 'Endereço não informado'); ?></p>
                                    <p><?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Avaliações -->
                    <div class="tab-pane fade" id="avaliacoes">
                        <div class="benefit-section">
                            <h3 class="benefit-section-title">
                                <i class="fas fa-star"></i> Avaliações dos Usuários
                            </h3>
                            
                            <!-- Resumo das Avaliações -->
                            <div class="rating-summary mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <div class="overall-rating">
                                            <span class="rating-number"><?php echo $avg_rating; ?></span>
                                            <div class="rating-stars">
                                                <span class="stars">
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $avg_rating) {
                                                            echo '★';
                                                        } else {
                                                            echo '☆';
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <p class="rating-text"><?php echo $rating_summary['total']; ?> avaliações</p>
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
                                            <div class="rating-bar">
                                                <span class="bar-label"><?php echo $star; ?> estrelas</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                                <span class="bar-count"><?php echo $count; ?></span>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Avaliações -->
                            <div class="reviews-list">
                                <?php if (count($reviews) > 0): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <div class="review-header">
                                                <div class="reviewer-info">
                                                    <div class="reviewer-avatar">
                                                        <?php echo strtoupper(substr($review['usuario_nome'], 0, 1)); ?>
                                                    </div>
                                                    <div class="reviewer-details">
                                                        <h6 class="reviewer-name"><?php echo htmlspecialchars($review['usuario_nome']); ?></h6>
                                                        <div class="review-rating">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                                            </div>
                                            <?php if ($review['comentario']): ?>
                                                <p class="review-comment"><?php echo htmlspecialchars($review['comentario']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-reviews">
                                        <p class="text-muted">Ainda não há avaliações para esta empresa. Seja o primeiro a avaliar!</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Formulário para Nova Avaliação -->
                            <div class="add-review-form mt-4">
                                <h5>Deixe sua avaliação</h5>
                                <?php if (isset($_GET['success'])): ?>
                                    <div class="alert alert-success">Avaliação adicionada com sucesso!</div>
                                <?php endif; ?>
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                <form method="POST" class="review-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="usuario_nome" class="form-label">Seu nome:</label>
                                            <input type="text" class="form-control" id="usuario_nome" name="usuario_nome" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="usuario_email" class="form-label">Seu email (opcional):</label>
                                            <input type="email" class="form-control" id="usuario_email" name="usuario_email">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sua avaliação:</label>
                                        <div class="rating-input">
                                            <input type="radio" name="rating" value="5" id="star5" required>
                                            <label for="star5">★</label>
                                            <input type="radio" name="rating" value="4" id="star4" required>
                                            <label for="star4">★</label>
                                            <input type="radio" name="rating" value="3" id="star3" required>
                                            <label for="star3">★</label>
                                            <input type="radio" name="rating" value="2" id="star2" required>
                                            <label for="star2">★</label>
                                            <input type="radio" name="rating" value="1" id="star1" required>
                                            <label for="star1">★</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comentario" class="form-label">Comentário:</label>
                                        <textarea class="form-control" name="comentario" rows="3" placeholder="Conte como foi sua experiência..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="benefit-sidebar">
                    <div class="use-button-container">
                        <?php if (isLoggedIn()): ?>
                            <a href="gerar-cupom.php?empresa=<?php echo $company['id']; ?>" class="btn-use">
                                USAR
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn-use">
                                FAZER LOGIN
                            </a>
                        <?php endif; ?>
                        
                        <div class="company-mini-logo">
                            <?php if ($company['logo']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>">
                            <?php else: ?>
                                <div class="logo-placeholder-mini">
                                    <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="company-description">
                        <?php if ($company['descricao']): ?>
                            <p><?php echo nl2br(htmlspecialchars($company['descricao'])); ?></p>
                        <?php else: ?>
                            <p>Aproveite os benefícios exclusivos do <?php echo htmlspecialchars($company['nome']); ?>! Uma experiência única que oferece descontos especiais para membros do Clube de Vantagens da ANETI.</p>
                        <?php endif; ?>
                        
                        <?php if ($company['website'] || $company['telefone'] || $company['email']): ?>
                            <div class="contact-info mt-3">
                                <?php if ($company['website']): ?>
                                    <p><i class="fas fa-globe"></i> <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"><?php echo htmlspecialchars($company['website']); ?></a></p>
                                <?php endif; ?>
                                <?php if ($company['telefone']): ?>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($company['telefone']); ?></p>
                                <?php endif; ?>
                                <?php if ($company['email']): ?>
                                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>"><?php echo htmlspecialchars($company['email']); ?></a></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>