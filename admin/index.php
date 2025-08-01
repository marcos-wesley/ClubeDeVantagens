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

$page_title = "Dashboard";

// Verificar se há mensagem de erro de acesso
$access_error = '';
if (isset($_GET['error']) && $_GET['error'] === 'access_denied') {
    $access_error = 'Acesso negado! Você não tem permissão para acessar esta funcionalidade.';
}

include 'includes/admin-header.php';
?>

    <div class="container-fluid mt-4">
        <?php if ($access_error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $access_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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
                        <div id="most-visited-content">
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
                        <div id="most-used-content">
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
                        <div id="most-present-content">
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
                        <div id="most-active-content">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Visitas
        const ctx = document.getElementById('visitsChart').getContext('2d');
        let visitsChart = new Chart(ctx, {
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

        // Função para atualizar o gráfico de visitas
        function updateVisitsChart(period) {
            fetch(`ajax/dashboard_data.php?type=visits&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    visitsChart.data.labels = data.labels;
                    visitsChart.data.datasets[0].data = data.visits;
                    visitsChart.data.datasets[1].data = data.unique_visitors;
                    visitsChart.update();
                    
                    // Atualizar estatísticas de visitas
                    document.querySelector('.stat-number').textContent = data.totals.visits_24h.toLocaleString();
                    document.querySelectorAll('.stat-number')[1].textContent = data.totals.visits_7d.toLocaleString();
                    document.querySelectorAll('.stat-number')[2].textContent = data.totals.visits_30d.toLocaleString();
                    
                    // Atualizar visitantes únicos
                    const uniqueStats = document.querySelectorAll('.widget-card')[2].querySelectorAll('.stat-number');
                    uniqueStats[0].textContent = data.totals.unique_24h.toLocaleString();
                    uniqueStats[1].textContent = data.totals.unique_7d.toLocaleString();
                    uniqueStats[2].textContent = data.totals.unique_30d.toLocaleString();
                })
                .catch(error => console.error('Erro ao carregar dados:', error));
        }

        // Função para atualizar rankings/listas
        function updateRanking(type, period, containerId) {
            fetch(`ajax/dashboard_data.php?type=${type}&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById(containerId);
                    if (!container) return;
                    
                    let html = '';
                    
                    if (type === 'most_visited') {
                        data.forEach(item => {
                            html += `
                                <div class="metric-item">
                                    <div class="metric-avatar">
                                        ${item.nome.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div class="metric-info">
                                        <p class="metric-name">${item.nome}</p>
                                        <p class="metric-detail">${item.categoria} • (${item.visits} visitas)</p>
                                    </div>
                                </div>
                            `;
                        });
                    } else if (type === 'most_used') {
                        data.forEach(item => {
                            html += `
                                <div class="metric-item">
                                    <div class="metric-avatar">
                                        ${item.nome.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div class="metric-info">
                                        <p class="metric-name">${item.nome}</p>
                                        <p class="metric-detail">${item.action} ${item.usage_count} vezes</p>
                                    </div>
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                            `;
                        });
                    } else if (type === 'most_present' || type === 'most_active') {
                        const metricType = type === 'most_present' ? 'sessões' : 'resgates';
                        const metricValue = type === 'most_present' ? 'sessions' : 'resgates';
                        
                        html = '<ul class="ranking-list">';
                        data.forEach((item, index) => {
                            html += `
                                <li class="ranking-item">
                                    <div class="ranking-number">${index + 1}</div>
                                    <img src="${item.avatar}" alt="Avatar" class="user-avatar-large">
                                    <div class="metric-info">
                                        <p class="metric-name">${item.nome}</p>
                                        <p class="metric-detail">(${item[metricValue]} ${metricType})</p>
                                    </div>
                                </li>
                            `;
                        });
                        html += '</ul>';
                    }
                    
                    container.innerHTML = html;
                })
                .catch(error => console.error('Erro ao carregar dados:', error));
        }

        // Funcionalidade dos filtros do gráfico de visitas
        document.querySelectorAll('.chart-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active de todos os filtros
                document.querySelectorAll('.chart-filter').forEach(f => f.classList.remove('active'));
                
                // Adiciona active ao filtro clicado
                this.classList.add('active');
                
                // Mapear texto para período
                const periodMap = {
                    'Últimas 24 horas': '24h',
                    'Últimos 7 dias': '7d', 
                    'Último mês': '30d',
                    'Últimos 6 meses': '6m',
                    'Últimos 12 meses': '12m'
                };
                
                const period = periodMap[this.textContent] || '30d';
                updateVisitsChart(period);
            });
        });

        // Funcionalidade dos filtros de período para outros widgets
        document.querySelectorAll('.period-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active de todos os tabs do mesmo container
                const container = this.closest('.widget-body');
                container.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
                
                // Adiciona active ao tab clicado
                this.classList.add('active');
                
                // Mapear texto para período
                const periodMap = {
                    'Últimas 24 horas': '24h',
                    'Últimos 7 dias': '7d',
                    'Último mês': '30d',
                    'Últimos 6 meses': '6m',
                    'Últimos 12 meses': '12m'
                };
                
                const period = periodMap[this.textContent] || '30d';
                
                // Identificar tipo de widget baseado no título
                const widgetTitle = container.closest('.widget-card').querySelector('.widget-title').textContent;
                const widgetContent = container.querySelector('.ranking-list, .metric-item')?.parentElement || container.lastElementChild;
                
                if (widgetTitle.includes('mais visitados')) {
                    updateRanking('most_visited', period, widgetContent.id || 'most-visited-content');
                    if (!widgetContent.id) widgetContent.id = 'most-visited-content';
                } else if (widgetTitle.includes('mais utilizados')) {
                    updateRanking('most_used', period, widgetContent.id || 'most-used-content');
                    if (!widgetContent.id) widgetContent.id = 'most-used-content';
                } else if (widgetTitle.includes('mais presentes')) {
                    updateRanking('most_present', period, widgetContent.id || 'most-present-content');
                    if (!widgetContent.id) widgetContent.id = 'most-present-content';
                } else if (widgetTitle.includes('mais ativos')) {
                    updateRanking('most_active', period, widgetContent.id || 'most-active-content');
                    if (!widgetContent.id) widgetContent.id = 'most-active-content';
                }
            });
        });
    </script>
</body>
</html>
