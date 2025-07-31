<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$empresa_id = isset($_GET['empresa']) ? (int)$_GET['empresa'] : 0;

if (!$empresa_id) {
    redirect('../index.php');
}

$company = getCompanyById($conn, $empresa_id);

if (!$company) {
    redirect('../index.php');
}

$error = '';
$success = '';

if ($_POST && isset($_POST['confirm'])) {
    $coupon = generateCoupon($conn, $_SESSION['user_id'], $empresa_id);
    
    if ($coupon) {
        $success = 'Cupom gerado com sucesso!';
        $coupon_data = [
            'codigo' => $coupon['codigo'],
            'empresa_nome' => $company['nome'],
            'empresa_logo' => $company['logo'],
            'usuario_nome' => $_SESSION['user_nome'],
            'usuario_plano' => $_SESSION['user_plano'],
            'data_geracao' => date('d/m/Y H:i:s')
        ];
    } else {
        $error = 'Erro ao gerar cupom. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Cupom - <?php echo htmlspecialchars($company['nome']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!isset($coupon_data)): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4><i class="fas fa-ticket-alt"></i> Gerar Cupom de Desconto</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

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
                                    <h5><?php echo htmlspecialchars($company['nome']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($company['categoria']); ?> • <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?></p>
                                    <p><?php echo htmlspecialchars(substr($company['descricao'], 0, 200)); ?>...</p>
                                </div>
                            </div>

                            <hr>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Confirmação</h6>
                                <p class="mb-2">Você está prestes a gerar um cupom de desconto para <strong><?php echo htmlspecialchars($company['nome']); ?></strong>.</p>
                                <p class="mb-0"><strong>Membro:</strong> <?php echo htmlspecialchars($_SESSION['user_nome']); ?> (<?php echo USER_PLANS[$_SESSION['user_plano']]; ?>)</p>
                            </div>

                            <form method="POST">
                                <div class="d-flex justify-content-between">
                                    <a href="empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Voltar
                                    </a>
                                    <button type="submit" name="confirm" class="btn btn-success btn-lg">
                                        <i class="fas fa-magic"></i> Confirmar e Gerar Cupom
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-success text-white text-center">
                            <h4><i class="fas fa-check-circle"></i> Cupom Gerado com Sucesso!</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success text-center">
                                <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                                <h5>Seu cupom está pronto!</h5>
                                <p>Apresente este cupom na empresa parceira para aproveitar seu desconto.</p>
                            </div>

                            <!-- Coupon Display -->
                            <div class="coupon-display" id="couponDisplay">
                                <div class="coupon-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center">
                                            <?php if ($coupon_data['empresa_logo']): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($coupon_data['empresa_logo']); ?>" alt="Logo" class="coupon-logo">
                                            <?php else: ?>
                                                <div class="coupon-logo-placeholder">
                                                    <?php echo strtoupper(substr($coupon_data['empresa_nome'], 0, 2)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-9">
                                            <h4 class="coupon-company-name"><?php echo htmlspecialchars($coupon_data['empresa_nome']); ?></h4>
                                            <p class="coupon-description"><?php echo htmlspecialchars($company['descricao']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="coupon-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="coupon-info">
                                                <strong>CÓDIGO DO CUPOM</strong>
                                                <div class="coupon-code"><?php echo htmlspecialchars($coupon_data['codigo']); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="coupon-info">
                                                <strong>MEMBRO ANETI</strong>
                                                <div><?php echo htmlspecialchars($coupon_data['usuario_nome']); ?></div>
                                                <div class="text-muted">Plano: <?php echo USER_PLANS[$coupon_data['usuario_plano']]; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="coupon-footer">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <small><strong>Regras:</strong> <?php echo htmlspecialchars($company['regras']); ?></small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small><strong>Gerado em:</strong><br><?php echo $coupon_data['data_geracao']; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" onclick="window.print()">
                                        <i class="fas fa-print"></i> Imprimir Cupom
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="downloadCoupon()">
                                        <i class="fas fa-download"></i> Baixar HTML
                                    </button>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-tachometer-alt"></i> Meu Dashboard
                                </a>
                                <a href="../index.php" class="btn btn-secondary">
                                    <i class="fas fa-home"></i> Página Inicial
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function downloadCoupon() {
            const couponHtml = document.getElementById('couponDisplay').outerHTML;
            const fullHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Cupom - <?php echo htmlspecialchars($company['nome']); ?></title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link href="../assets/css/style.css" rel="stylesheet">
                    <style>
                        body { padding: 20px; }
                        @media print { 
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${couponHtml}
                </body>
                </html>
            `;
            
            const blob = new Blob([fullHtml], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'cupom-<?php echo strtolower(str_replace(' ', '-', $company['nome'])); ?>-<?php echo date('Y-m-d'); ?>.html';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    </script>
</body>
</html>
