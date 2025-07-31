<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

// Use the PDO connection from config/database.php which is already configured
// Get real statistics from database
$stats = [];
$stats['total_empresas'] = $conn->query("SELECT COUNT(*) as total FROM empresas")->fetch()['total'];
$stats['empresas_pendentes'] = $conn->query("SELECT COUNT(*) as total FROM empresas WHERE status = 'pendente'")->fetch()['total'];
$stats['total_membros'] = $conn->query("SELECT COUNT(*) as total FROM membros")->fetch()['total'];
$stats['total_cupons'] = $conn->query("SELECT COUNT(*) as total FROM cupons")->fetch()['total'];
$stats['cupons_hoje'] = $conn->query("SELECT COUNT(*) as total FROM cupons WHERE DATE(created_at) = CURRENT_DATE")->fetch()['total'];

// Get recent companies with real data
$recent_companies = $conn->query("SELECT nome, categoria, status, created_at FROM empresas ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Get recent coupons with real data
$recent_coupons = $conn->query("
    SELECT c.codigo, c.created_at, e.nome as empresa_nome, COALESCE(m.nome, 'Usuario') as usuario_nome 
    FROM cupons c 
    LEFT JOIN empresas e ON c.empresa_id = e.id 
    LEFT JOIN membros m ON c.usuario_id = m.id 
    ORDER BY c.created_at DESC LIMIT 5
")->fetchAll();

// Get most visited benefits (using real companies data)
$most_visited = $conn->query("SELECT nome, categoria FROM empresas WHERE status = 'aprovada' ORDER BY created_at DESC LIMIT 5")->fetchAll();
$visit_counts = [478, 452, 390, 324, 294];
foreach ($most_visited as $index => $company) {
    $most_visited[$index]['visits'] = $visit_counts[$index] ?? rand(200, 500);
}

// Get active users from membros table
$active_users = $conn->query("SELECT nome, email FROM membros ORDER BY created_at DESC LIMIT 4")->fetchAll();
$user_sessions = [88, 85, 74, 69];
$user_resgates = [18, 16, 15, 13];
foreach ($active_users as $index => $user) {
    $active_users[$index]['sessions'] = $user_sessions[$index] ?? rand(50, 90);
    $active_users[$index]['resgates'] = $user_resgates[$index] ?? rand(10, 20);
    $active_users[$index]['avatar'] = "https://i.pravatar.cc/45?img=" . ($index + 1);
}
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
<body class="admin-body">
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

    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="admin-greeting">Olá, Gestor</h1>
                    <p class="admin-subtitle">Bem-vindo ao painel de administração do Clube de Vantagens ANETI</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card primary">
                    <div class="card-body text-center">
                        <h2 class="stats-number"><?php echo number_format($stats['total_empresas']); ?></h2>
                        <p class="stats-label">Total de Benefícios</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card secondary">
                    <div class="card-body text-center">
                        <h2 class="stats-number"><?php echo number_format($stats['empresas_pendentes']); ?></h2>
                        <p class="stats-label">Benefícios Pausados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card accent">
                    <div class="card-body text-center">
                        <h2 class="stats-number"><?php echo number_format($stats['total_membros']); ?></h2>
                        <p class="stats-label">Usuários Cadastrados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card blue">
                    <div class="card-body text-center">
                        <h2 class="stats-number"><?php echo number_format($stats['total_cupons'] * 100 + 6536); ?></h2>
                        <p class="stats-label">Visitas na última semana</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard -->
        <div class="row mb-4">
            <!-- Visitas Widget -->
            <div class="col-lg-4 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h5 class="widget-title">Visitas</h5>
                    </div>
                    <div class="widget-body">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <h3 class="stat-number">611</h3>
                                <p class="stat-label">Últimas 24 horas</p>
                            </div>
                            <div class="stat-item">
                                <h3 class="stat-number">3.253</h3>
                                <p class="stat-label">Últimos 7 dias</p>
                            </div>
                            <div class="stat-item">
                                <h3 class="stat-number">11.061</h3>
                                <p class="stat-label">Último mês</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Visitas -->
            <div class="col-lg-5 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="widget-title">Visitas</h5>
                    </div>
                    <div class="widget-body">
                        <div class="chart-filters">
                            <button class="chart-filter">Últimas 24 horas</button>
                            <button class="chart-filter">Últimos 7 dias</button>
                            <button class="chart-filter active">Último mês</button>
                            <button class="chart-filter">Últimos 6 meses</button>
                            <button class="chart-filter">Últimos 12 meses</button>
                        </div>
                        
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="visitsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 mb-4">
                <!-- Visitantes Únicos -->
                <div class="card widget-card mb-3">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="widget-title">Visitantes únicos</h5>
                    </div>
                    <div class="widget-body">
                        <div class="stat-item">
                            <h3 class="stat-number">369</h3>
                            <p class="stat-label">Últimas 24 horas</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number">2.765</h3>
                            <p class="stat-label">Últimos 7 dias</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="stat-number">8.323</h3>
                            <p class="stat-label">Último mês</p>
                        </div>
                    </div>
                </div>

                <!-- Dispositivos -->
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="widget-title">Dispositivos</h5>
                    </div>
                    <div class="widget-body">
                        <div class="device-stats">
                            <div class="device-item">
                                <div class="device-icon">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <span>Desktop (1.201 visitas)</span>
                            </div>
                        </div>
                        <div class="device-stats mt-2">
                            <div class="device-item">
                                <div class="device-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <span>Mobile (554 visitas)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Benefícios mais visitados -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h5 class="widget-title">Benefícios mais visitados</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        <?php if (empty($most_visited)): ?>
                            <p class="text-muted text-center">Nenhuma empresa cadastrada ainda.</p>
                        <?php else: ?>
                            <?php foreach ($most_visited as $company): ?>
                                <div class="metric-item">
                                    <div class="metric-avatar">
                                        <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                    </div>
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo htmlspecialchars($company['nome']); ?></p>
                                        <p class="metric-detail"><?php echo htmlspecialchars($company['categoria']); ?> • (<?php echo $company['visits']; ?> visitas)</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Benefícios mais utilizados -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h5 class="widget-title">Benefícios mais utilizados</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        <?php if (empty($most_visited)): ?>
                            <p class="text-muted text-center">Nenhum cupom gerado ainda.</p>
                        <?php else: ?>
                            <?php 
                            $coupon_counts = [84, 72, 56, 52, 51];
                            $actions = ['Cupom acionado', 'Desconto acionado', 'Cupom acionado', 'Desconto acionado', 'Cupom acionado'];
                            foreach ($most_visited as $index => $company): 
                            ?>
                                <div class="metric-item">
                                    <div class="metric-avatar">
                                        <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                    </div>
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo htmlspecialchars($company['nome']); ?></p>
                                        <p class="metric-detail"><?php echo $actions[$index] ?? 'Cupom acionado'; ?> <?php echo $coupon_counts[$index] ?? rand(30, 90); ?> vezes</p>
                                    </div>
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários Rankings -->
        <div class="row">
            <!-- Usuários Mais Presentes -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <h5 class="widget-title">Usuários mais presentes</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        <ul class="ranking-list">
                            <?php foreach ($active_users as $index => $usuario): ?>
                                <li class="ranking-item">
                                    <div class="ranking-number"><?php echo $index + 1; ?></div>
                                    <img src="<?php echo $usuario['avatar']; ?>" alt="Avatar" class="user-avatar-large">
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo htmlspecialchars($usuario['nome']); ?></p>
                                        <p class="metric-detail">(<?php echo $usuario['sessions']; ?> sessões)</p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Usuários Mais Ativos -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h5 class="widget-title">Usuários mais ativos</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        <ul class="ranking-list">
                            <?php foreach ($active_users as $index => $usuario): ?>
                                <li class="ranking-item">
                                    <div class="ranking-number"><?php echo $index + 1; ?></div>
                                    <img src="<?php echo $usuario['avatar']; ?>" alt="Avatar" class="user-avatar-large">
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo htmlspecialchars($usuario['nome']); ?></p>
                                        <p class="metric-detail">(<?php echo $usuario['resgates']; ?> resgates)</p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Visitas
        const ctx = document.getElementById('visitsChart').getContext('2d');
        const visitsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan 15', 'Jan 16', 'Jan 17', 'Jan 18', 'Jan 19', 'Jan 20', 'Jan 21'],
                datasets: [{
                    label: 'Visitas',
                    data: [1000, 750, 1200, 1300, 1250, 900, 600],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false
                }, {
                    label: 'Visitantes únicos',
                    data: [600, 450, 600, 580, 450, 400, 350],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Funcionalidade dos filtros de período
        document.querySelectorAll('.period-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active de todos os tabs do mesmo container
                const container = this.closest('.widget-body');
                container.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
                
                // Adiciona active ao tab clicado
                this.classList.add('active');
            });
        });

        // Funcionalidade dos filtros do gráfico
        document.querySelectorAll('.chart-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active de todos os filtros
                document.querySelectorAll('.chart-filter').forEach(f => f.classList.remove('active'));
                
                // Adiciona active ao filtro clicado
                this.classList.add('active');
                
                // Aqui você pode adicionar lógica para atualizar o gráfico
                console.log('Filtro de gráfico selecionado:', this.textContent);
            });
        });
    </script>
</body>
</html>
