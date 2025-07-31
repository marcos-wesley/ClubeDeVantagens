<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';
$date_filter = isset($_GET['data']) ? sanitizeInput($_GET['data']) : '';
$empresa_filter = isset($_GET['empresa']) ? (int)$_GET['empresa'] : 0;

// Get coupons with filters
$sql = "
    SELECT c.*, e.nome as empresa_nome, e.logo as empresa_logo, u.nome as usuario_nome, u.plano as usuario_plano 
    FROM cupons c 
    JOIN empresas e ON c.empresa_id = e.id 
    JOIN usuarios u ON c.usuario_id = u.id
";

$params = [];
$where_conditions = [];

if ($date_filter == 'hoje') {
    $where_conditions[] = "DATE(c.created_at) = CURRENT_DATE";
} elseif ($date_filter == 'semana') {
    $where_conditions[] = "c.created_at >= CURRENT_DATE - INTERVAL '7 days'";
} elseif ($date_filter == 'mes') {
    $where_conditions[] = "c.created_at >= CURRENT_DATE - INTERVAL '30 days'";
}

if ($empresa_filter) {
    $where_conditions[] = "c.empresa_id = ?";
    $params[] = $empresa_filter;
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$coupons = $stmt->fetchAll();

// Get companies for filter
$companies = $conn->query("SELECT id, nome FROM empresas WHERE status = 'aprovada' ORDER BY nome")->fetchAll();

// Statistics
$stats = [
    'total' => count($coupons),
    'hoje' => $conn->query("SELECT COUNT(*) as total FROM cupons WHERE DATE(created_at) = CURRENT_DATE")->fetch()['total'],
    'semana' => $conn->query("SELECT COUNT(*) as total FROM cupons WHERE created_at >= CURRENT_DATE - INTERVAL '7 days'")->fetch()['total'],
    'mes' => $conn->query("SELECT COUNT(*) as total FROM cupons WHERE created_at >= CURRENT_DATE - INTERVAL '30 days'")->fetch()['total']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cupons - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cog"></i> Admin ANETI
            </a>
            
            <div class="navbar-nav">
                <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" href="empresas.php"><i class="fas fa-store"></i> Empresas</a>
                <a class="nav-link active" href="cupons.php"><i class="fas fa-ticket-alt"></i> Cupons</a>
                <a class="nav-link" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-ticket-alt"></i> Gerenciar Cupons</h2>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total</h6>
                                        <h3><?php echo $stats['total']; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Hoje</h6>
                                        <h3><?php echo $stats['hoje']; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Esta Semana</h6>
                                        <h3><?php echo $stats['semana']; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-week fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Este Mês</h6>
                                        <h3><?php echo $stats['mes']; ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-4">
                                <label class="form-label">Período:</label>
                                <div class="btn-group w-100">
                                    <a href="cupons.php" class="btn btn-outline-secondary <?php echo empty($date_filter) ? 'active' : ''; ?>">
                                        Todos
                                    </a>
                                    <a href="cupons.php?data=hoje" class="btn btn-outline-info <?php echo $date_filter == 'hoje' ? 'active' : ''; ?>">
                                        Hoje
                                    </a>
                                    <a href="cupons.php?data=semana" class="btn btn-outline-success <?php echo $date_filter == 'semana' ? 'active' : ''; ?>">
                                        7 dias
                                    </a>
                                    <a href="cupons.php?data=mes" class="btn btn-outline-warning <?php echo $date_filter == 'mes' ? 'active' : ''; ?>">
                                        30 dias
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="empresa" class="form-label">Filtrar por Empresa:</label>
                                <select class="form-select" id="empresa" name="empresa" onchange="this.form.submit()">
                                    <option value="">Todas as empresas</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?php echo $company['id']; ?>" <?php echo $empresa_filter == $company['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($company['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="data" value="<?php echo htmlspecialchars($date_filter); ?>">
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" onclick="exportCoupons()">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Coupons List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Lista de Cupons (<?php echo count($coupons); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($coupons)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5>Nenhum cupom encontrado</h5>
                                <p class="text-muted">
                                    <?php if ($date_filter || $empresa_filter): ?>
                                        Não há cupons para os filtros selecionados.
                                    <?php else: ?>
                                        Ainda não há cupons gerados.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Empresa</th>
                                            <th>Membro</th>
                                            <th>Plano</th>
                                            <th>Data/Hora</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <tr>
                                                <td>
                                                    <code class="coupon-code-small"><?php echo htmlspecialchars($coupon['codigo']); ?></code>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($coupon['empresa_logo']): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($coupon['empresa_logo']); ?>" alt="Logo" class="admin-company-logo-small me-2">
                                                        <?php else: ?>
                                                            <div class="admin-company-logo-placeholder-small me-2">
                                                                <?php echo strtoupper(substr($coupon['empresa_nome'], 0, 2)); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <strong><?php echo htmlspecialchars($coupon['empresa_nome']); ?></strong>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($coupon['usuario_nome']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $coupon['usuario_plano'] == 'senior' ? 'success' : ($coupon['usuario_plano'] == 'pleno' ? 'warning' : 'info'); ?>">
                                                        <?php echo USER_PLANS[$coupon['usuario_plano']]; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($coupon['created_at']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewCoupon('<?php echo htmlspecialchars($coupon['codigo']); ?>', '<?php echo htmlspecialchars($coupon['empresa_nome']); ?>', '<?php echo htmlspecialchars($coupon['usuario_nome']); ?>', '<?php echo formatDate($coupon['created_at']); ?>')" title="Ver cupom">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
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

    <!-- Coupon View Modal -->
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="coupon-info text-center">
                                    <strong>CÓDIGO DO CUPOM</strong>
                                    <div class="coupon-code" id="modalCouponCodigo"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coupon-info text-center">
                                    <strong>MEMBRO ANETI</strong>
                                    <div id="modalUsuarioNome"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small><strong>Gerado em:</strong> <span id="modalCouponData"></span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewCoupon(codigo, empresaNome, usuarioNome, data) {
            document.getElementById('modalCouponCodigo').textContent = codigo;
            document.getElementById('modalEmpresaNome').textContent = empresaNome;
            document.getElementById('modalUsuarioNome').textContent = usuarioNome;
            document.getElementById('modalCouponData').textContent = data;
            
            const modal = new bootstrap.Modal(document.getElementById('couponModal'));
            modal.show();
        }
        
        function exportCoupons() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            
            // In a real implementation, create a CSV export endpoint
            alert('Funcionalidade de exportação será implementada em breve.');
        }
    </script>
</body>
</html>
