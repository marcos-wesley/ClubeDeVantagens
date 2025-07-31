<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';

// Handle actions
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_member') {
        $nome = sanitizeInput($_POST['nome']);
        $email = sanitizeInput($_POST['email']);
        $plano = sanitizeInput($_POST['plano']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO membros (nome, email, senha, plano, ativo, created_at) VALUES (?, ?, ?, ?, true, NOW())");
            $stmt->execute([$nome, $email, $senha, $plano]);
            $message = 'Membro cadastrado com sucesso!';
        } catch (Exception $e) {
            $message = 'Erro ao cadastrar membro: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'update_status' && isset($_POST['member_id'])) {
        $member_id = (int)$_POST['member_id'];
        $ativo = isset($_POST['ativo']) ? true : false;
        
        $stmt = $conn->prepare("UPDATE membros SET ativo = ? WHERE id = ?");
        $stmt->execute([$ativo, $member_id]);
        $message = 'Status do membro atualizado!';
    }
}

// Get members with usage statistics
$sql = "
    SELECT m.*, 
           COUNT(c.id) as total_cupons,
           COUNT(CASE WHEN DATE(c.created_at) >= CURRENT_DATE - INTERVAL '30 days' THEN 1 END) as cupons_mes
    FROM membros m
    LEFT JOIN cupons c ON m.id = c.membro_id
    GROUP BY m.id
    ORDER BY m.created_at DESC
";
$members = $conn->query($sql)->fetchAll();

// Statistics
$stats = [
    'total_membros' => $conn->query("SELECT COUNT(*) FROM membros")->fetchColumn(),
    'membros_ativos' => $conn->query("SELECT COUNT(*) FROM membros WHERE ativo = true")->fetchColumn(),
    'total_cupons_gerados' => $conn->query("SELECT COUNT(*) FROM cupons")->fetchColumn(),
    'cupons_mes' => $conn->query("SELECT COUNT(*) FROM cupons WHERE created_at >= CURRENT_DATE - INTERVAL '30 days'")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Membros - Admin</title>
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
                <a class="nav-link" href="cupons.php"><i class="fas fa-ticket-alt"></i> Cupons</a>
                <a class="nav-link" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link active" href="membros.php"><i class="fas fa-users"></i> Membros</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users"></i> Gerenciar Membros</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-plus"></i> Novo Membro
                    </button>
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
                                        <h5 class="card-title">Total Membros</h5>
                                        <h2><?php echo $stats['total_membros']; ?></h2>
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
                                        <h5 class="card-title">Membros Ativos</h5>
                                        <h2><?php echo $stats['membros_ativos']; ?></h2>
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

                <!-- Members Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Plano</th>
                                        <th>Status</th>
                                        <th>Cupons Gerados</th>
                                        <th>Cupons (30 dias)</th>
                                        <th>Data Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><?php echo $member['id']; ?></td>
                                        <td><?php echo htmlspecialchars($member['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $member['plano'] == 'Senior' ? 'success' : ($member['plano'] == 'Pleno' ? 'warning' : 'primary'); ?>">
                                                <?php echo $member['plano']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $member['ativo'] ? 'success' : 'danger'; ?>">
                                                <?php echo $member['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $member['total_cupons']; ?></td>
                                        <td><?php echo $member['cupons_mes']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($member['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                <?php if (!$member['ativo']): ?>
                                                    <input type="hidden" name="ativo" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Ativar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Desativar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Novo Membro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_member">
                        
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" class="form-control" name="senha" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Plano</label>
                            <select class="form-select" name="plano" required>
                                <option value="Junior">Junior</option>
                                <option value="Pleno">Pleno</option>
                                <option value="Senior">Senior</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>