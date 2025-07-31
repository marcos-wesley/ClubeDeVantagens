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

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <?php if ($company['logo']): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="company-detail-logo">
                                <?php else: ?>
                                    <div class="company-detail-logo-placeholder">
                                        <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <h1 class="company-detail-title"><?php echo htmlspecialchars($company['nome']); ?></h1>
                                <div class="company-detail-meta">
                                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($company['categoria']); ?></span>
                                    <span class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Detalhes</h5>
                    </div>
                    <div class="card-body">
                        <h6>Descrição do Benefício</h6>
                        <p><?php echo nl2br(htmlspecialchars($company['descricao'])); ?></p>
                        
                        <h6>Como funciona:</h6>
                        <ol>
                            <li>Clique no botão "Gerar Cupom"</li>
                            <li>Apresente o cupom na empresa parceira</li>
                            <li>Aproveite seu desconto exclusivo</li>
                        </ol>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-contract"></i> Regulamento</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($company['regras'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h5><i class="fas fa-ticket-alt"></i> Gerar Cupom</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if (isLoggedIn()): ?>
                            <p>Clique no botão abaixo para gerar seu cupom de desconto exclusivo.</p>
                            <a href="gerar-cupom.php?empresa=<?php echo $company['id']; ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-magic"></i> Gerar Cupom
                            </a>
                        <?php else: ?>
                            <p>Faça login para gerar seu cupom de desconto.</p>
                            <a href="login.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Fazer Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6><i class="fas fa-building"></i> Informações da Empresa</h6>
                    </div>
                    <div class="card-body">
                        <div class="company-info">
                            <div class="info-item">
                                <strong>CNPJ:</strong><br>
                                <?php echo htmlspecialchars($company['cnpj']); ?>
                            </div>
                            <div class="info-item">
                                <strong>E-mail:</strong><br>
                                <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>">
                                    <?php echo htmlspecialchars($company['email']); ?>
                                </a>
                            </div>
                            <div class="info-item">
                                <strong>Telefone:</strong><br>
                                <a href="tel:<?php echo htmlspecialchars($company['telefone']); ?>">
                                    <?php echo htmlspecialchars($company['telefone']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
