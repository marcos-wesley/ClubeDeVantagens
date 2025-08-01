<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';

// Get API members with access statistics (members who logged in via API)
$sql = "
    SELECT 
        user_id,
        nome,
        email,
        plano,
        primeiro_acesso,
        ultimo_acesso,
        total_acessos,
        COALESCE(cupons_gerados, 0) as cupons_gerados,
        COALESCE(cupons_30_dias, 0) as cupons_30_dias
    FROM membros_api_access m
    LEFT JOIN (
        SELECT 
            usuario_id, 
            COUNT(*) as cupons_gerados,
            SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as cupons_30_dias
        FROM cupons 
        GROUP BY usuario_id
    ) c ON m.user_id = c.usuario_id
    ORDER BY m.ultimo_acesso DESC
";

try {
    $members = $conn->query($sql)->fetchAll();
} catch (Exception $e) {
    $members = [];
    $message = 'Erro ao carregar membros: ' . $e->getMessage();
}

// Statistics
try {
    $stats = [
        'total_membros_api' => $conn->query("SELECT COUNT(*) FROM membros_api_access")->fetchColumn(),
        'membros_ativos_hoje' => $conn->query("SELECT COUNT(*) FROM membros_api_access WHERE DATE(ultimo_acesso) = CURDATE()")->fetchColumn(),
        'total_cupons_gerados' => $conn->query("SELECT COUNT(*) FROM cupons")->fetchColumn(),
        'cupons_mes' => $conn->query("SELECT COUNT(*) FROM cupons WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn()
    ];
} catch (Exception $e) {
    $stats = [
        'total_membros_api' => 0,
        'membros_ativos_hoje' => 0,
        'total_cupons_gerados' => 0,
        'cupons_mes' => 0
    ];
}

$page_title = "Gerenciar Membros";
include 'includes/admin-header.php';
?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users"></i> Membros da API WordPress</h2>
                    <small class="text-muted">Controle de membros que acessaram o sistema via integração API</small>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Membros API</h5>
                                        <h2><?php echo $stats['total_membros_api']; ?></h2>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Acessos Hoje</h5>
                                        <h2><?php echo $stats['membros_ativos_hoje']; ?></h2>
                                    </div>
                                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Total Cupons</h5>
                                        <h2><?php echo $stats['total_cupons_gerados']; ?></h2>
                                    </div>
                                    <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Cupons (30 dias)</h5>
                                        <h2><?php echo $stats['cupons_mes']; ?></h2>
                                    </div>
                                    <i class="fas fa-calendar fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Members Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-api"></i> Membros que Acessaram via API WordPress</h5>
                        <small class="text-muted">Membros que fizeram login através da integração com a API do WordPress da ANETI</small>
                    </div>
                    <div class="card-body">
                        <?php if (empty($members)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>Nenhum membro acessou ainda</h5>
                                <p class="text-muted">Os membros que fizerem login via API WordPress aparecerão aqui automaticamente.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID WordPress</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Plano</th>
                                            <th>Primeiro Acesso</th>
                                            <th>Último Acesso</th>
                                            <th>Total Acessos</th>
                                            <th>Cupons Gerados</th>
                                            <th>Cupons (30 dias)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?php echo $member['user_id']; ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($member['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                                            <td>
                                                <?php 
                                                $plano_colors = [
                                                    'Júnior' => 'primary',
                                                    'Pleno' => 'warning', 
                                                    'Sênior' => 'success',
                                                    'Honra' => 'info',
                                                    'Diretivo' => 'dark'
                                                ];
                                                $color = $plano_colors[$member['plano']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $color; ?>">
                                                    <?php echo htmlspecialchars($member['plano']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?php echo date('d/m/Y H:i', strtotime($member['primeiro_acesso'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php 
                                                $ultimo_acesso = strtotime($member['ultimo_acesso']);
                                                $agora = time();
                                                $diff = $agora - $ultimo_acesso;
                                                
                                                if ($diff < 3600) { // menos de 1 hora
                                                    $status_class = 'success';
                                                    $status_text = 'Agora há pouco';
                                                } elseif ($diff < 86400) { // menos de 1 dia
                                                    $status_class = 'warning';
                                                    $status_text = 'Hoje';
                                                } elseif ($diff < 604800) { // menos de 1 semana
                                                    $status_class = 'info';
                                                    $status_text = 'Esta semana';
                                                } else {
                                                    $status_class = 'secondary';
                                                    $status_text = 'Há mais tempo';
                                                }
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?> mb-1">
                                                    <?php echo $status_text; ?>
                                                </span><br>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', $ultimo_acesso); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $member['total_acessos']; ?></span>
                                            </td>
                                            <td><?php echo $member['cupons_gerados']; ?></td>
                                            <td><?php echo $member['cupons_30_dias']; ?></td>
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>