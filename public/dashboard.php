<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$user_coupons = getUserCoupons($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            padding-top: 160px; /* Ajuste para compensar o header fixo */
        }
        .dashboard-header {
            margin-bottom: 2rem;
        }
        .coupon-table-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
        }
        .coupon-table-logo-placeholder {
            width: 40px;
            height: 40px;
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-sm i {
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 140px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="dashboard-header">
                    <h2><i class="fas fa-tachometer-alt"></i> Meu Dashboard</h2>
                    <p class="text-muted">Bem-vindo de volta, <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Meu Plano</h6>
                                <h4><?php echo USER_PLANS[$_SESSION['user_plano']]; ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-id-card fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Cupons Gerados</h6>
                                <h4><?php echo count($user_coupons); ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Empresas Parceiras</h6>
                                <h4>
                                    <?php
                                    $stmt = $conn->query("SELECT COUNT(*) as total FROM empresas WHERE status = 'aprovada'");
                                    echo $stmt->fetch()['total'];
                                    ?>
                                </h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-store fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Histórico de Cupons</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user_coupons)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5>Nenhum cupom gerado ainda</h5>
                                <p class="text-muted">Explore nossas empresas parceiras e comece a economizar!</p>
                                <a href="../index.php" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Descobrir Benefícios
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Código do Cupom</th>
                                            <th>Data de Geração</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($user_coupons as $coupon): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($coupon['empresa_logo']): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($coupon['empresa_logo']); ?>" alt="Logo" class="coupon-table-logo me-2">
                                                        <?php else: ?>
                                                            <div class="coupon-table-logo-placeholder me-2">
                                                                <?php echo strtoupper(substr($coupon['empresa_nome'], 0, 2)); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <strong><?php echo htmlspecialchars($coupon['empresa_nome']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="coupon-code-small"><?php echo htmlspecialchars($coupon['codigo']); ?></code>
                                                </td>
                                                <td><?php echo formatDate($coupon['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="empresa-detalhes.php?id=<?php echo $coupon['empresa_id']; ?>" class="btn btn-outline-primary" title="Ver empresa">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-success" onclick="showCouponModal('<?php echo htmlspecialchars($coupon['codigo']); ?>', '<?php echo htmlspecialchars($coupon['empresa_nome']); ?>', '<?php echo formatDate($coupon['created_at']); ?>')" title="Ver cupom">
                                                            <i class="fas fa-ticket-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coupon Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ticket-alt"></i> Detalhes do Cupom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="coupon-display-modal">
                        <div class="text-center mb-3">
                            <h4 id="modalEmpresaNome"></h4>
                        </div>
                        <div class="coupon-info text-center">
                            <strong>CÓDIGO DO CUPOM</strong>
                            <div class="coupon-code" id="modalCouponCodigo"></div>
                        </div>
                        <div class="text-center mt-3">
                            <small><strong>Gerado em:</strong> <span id="modalCouponData"></span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showCouponModal(codigo, empresaNome, data) {
            document.getElementById('modalCouponCodigo').textContent = codigo;
            document.getElementById('modalEmpresaNome').textContent = empresaNome;
            document.getElementById('modalCouponData').textContent = data;
            
            const modal = new bootstrap.Modal(document.getElementById('couponModal'));
            modal.show();
        }
    </script>
</body>
</html>
