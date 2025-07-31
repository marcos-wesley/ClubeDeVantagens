<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

// Get statistics
$stats = [
    'empresas_pendentes' => $conn->query("SELECT COUNT(*) as total FROM empresas WHERE status = 'pendente'")->fetch()['total'],
    'empresas_aprovadas' => $conn->query("SELECT COUNT(*) as total FROM empresas WHERE status = 'aprovada'")->fetch()['total'],
    'total_cupons' => $conn->query("SELECT COUNT(*) as total FROM cupons")->fetch()['total'],
    'cupons_hoje' => $conn->query("SELECT COUNT(*) as total FROM cupons WHERE DATE(created_at) = CURRENT_DATE")->fetch()['total']
];

// Recent activity
$recent_companies = $conn->query("SELECT * FROM empresas ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_coupons = $conn->query("
    SELECT c.*, e.nome as empresa_nome, u.nome as usuario_nome 
    FROM cupons c 
    JOIN empresas e ON c.empresa_id = e.id 
    JOIN usuarios u ON c.usuario_id = u.id 
    ORDER BY c.created_at DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
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
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="empresas.php">
                            <i class="fas fa-store"></i> Empresas
                            <?php if ($stats['empresas_pendentes'] > 0): ?>
                                <span class="badge bg-warning"><?php echo $stats['empresas_pendentes']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cupons.php">
                            <i class="fas fa-ticket-alt"></i> Cupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categorias.php">
                            <i class="fas fa-tags"></i> Categorias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membros.php">
                            <i class="fas fa-users"></i> Membros
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['admin_nome']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Ver Site
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Sair
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard Administrativo</h2>
                <p class="text-muted">Bem-vindo ao painel de administração do Clube de Vantagens ANETI</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Empresas Pendentes</h6>
                                <h3><?php echo $stats['empresas_pendentes']; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <a href="empresas.php?status=pendente" class="text-white">
                            <small><i class="fas fa-eye"></i> Ver todas</small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Empresas Aprovadas</h6>
                                <h3><?php echo $stats['empresas_aprovadas']; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-store fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <a href="empresas.php?status=aprovada" class="text-white">
                            <small><i class="fas fa-eye"></i> Ver todas</small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total de Cupons</h6>
                                <h3><?php echo $stats['total_cupons']; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <a href="cupons.php" class="text-white">
                            <small><i class="fas fa-eye"></i> Ver todos</small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Cupons Hoje</h6>
                                <h3><?php echo $stats['cupons_hoje']; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                            </div>
                        </div>
                        <a href="cupons.php?data=hoje" class="text-white">
                            <small><i class="fas fa-eye"></i> Ver todos</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Companies -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-store"></i> Empresas Recentes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_companies)): ?>
                            <p class="text-muted">Nenhuma empresa cadastrada ainda.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_companies as $company): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold"><?php echo htmlspecialchars($company['nome']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($company['categoria']); ?> • <?php echo formatDate($company['created_at']); ?></small>
                                        </div>
                                        <span class="badge bg-<?php echo $company['status'] == 'aprovada' ? 'success' : ($company['status'] == 'pendente' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($company['status']); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Coupons -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-ticket-alt"></i> Cupons Recentes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_coupons)): ?>
                            <p class="text-muted">Nenhum cupom gerado ainda.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_coupons as $coupon): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($coupon['empresa_nome']); ?></h6>
                                            <small><?php echo formatDate($coupon['created_at']); ?></small>
                                        </div>
                                        <p class="mb-1">
                                            <strong>Membro:</strong> <?php echo htmlspecialchars($coupon['usuario_nome']); ?>
                                        </p>
                                        <small>Código: <code><?php echo htmlspecialchars($coupon['codigo']); ?></code></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
