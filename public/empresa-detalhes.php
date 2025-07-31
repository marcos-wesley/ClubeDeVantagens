<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('../index.php');
}

$company = getCompanyById($conn, $id);

if (!$company) {
    redirect('../index.php');
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
                            <span class="stars">★★★★★</span>
                            <span class="rating-text">4.8</span>
                        </div>
                        <p class="benefit-category"><?php echo htmlspecialchars($company['categoria']); ?></p>
                        <p class="benefit-discount">20% de desconto em todos os serviços</p>
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
                    <a class="nav-link active" href="#detalhes">Detalhes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#avaliacoes">Avaliações 
                        <span class="badge bg-secondary">7</span>
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
                <!-- Main Image -->
                <div class="benefit-main-image mb-4">
                    <?php if ($company['logo']): ?>
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