<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Handle actions
if ($_POST && isset($_POST['action']) && isset($_POST['empresa_id'])) {
    $empresa_id = (int)$_POST['empresa_id'];
    $action = $_POST['action'];
    
    if ($action == 'aprovar') {
        $stmt = $conn->prepare("UPDATE empresas SET status = 'aprovada', destaque = ? WHERE id = ?");
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        $stmt->execute([$destaque, $empresa_id]);
        $message = 'Empresa aprovada com sucesso!';
    } elseif ($action == 'rejeitar') {
        $stmt = $conn->prepare("UPDATE empresas SET status = 'rejeitada' WHERE id = ?");
        $stmt->execute([$empresa_id]);
        $message = 'Empresa rejeitada.';
    } elseif ($action == 'excluir') {
        $stmt = $conn->prepare("DELETE FROM empresas WHERE id = ?");
        $stmt->execute([$empresa_id]);
        $message = 'Empresa excluída.';
    }
}

// Get companies
$sql = "SELECT * FROM empresas";
$params = [];

if ($status_filter) {
    $sql .= " WHERE status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$companies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Empresas - Admin</title>
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
                <a class="nav-link active" href="empresas.php"><i class="fas fa-store"></i> Empresas</a>
                <a class="nav-link" href="cupons.php"><i class="fas fa-ticket-alt"></i> Cupons</a>
                <a class="nav-link" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-store"></i> Gerenciar Empresas</h2>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Filtrar por Status:</label>
                                <div class="btn-group w-100">
                                    <a href="empresas.php" class="btn btn-outline-secondary <?php echo empty($status_filter) ? 'active' : ''; ?>">
                                        Todas
                                    </a>
                                    <a href="empresas.php?status=pendente" class="btn btn-outline-warning <?php echo $status_filter == 'pendente' ? 'active' : ''; ?>">
                                        Pendentes
                                    </a>
                                    <a href="empresas.php?status=aprovada" class="btn btn-outline-success <?php echo $status_filter == 'aprovada' ? 'active' : ''; ?>">
                                        Aprovadas
                                    </a>
                                    <a href="empresas.php?status=rejeitada" class="btn btn-outline-danger <?php echo $status_filter == 'rejeitada' ? 'active' : ''; ?>">
                                        Rejeitadas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Companies List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Lista de Empresas (<?php echo count($companies); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($companies)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                <h5>Nenhuma empresa encontrada</h5>
                                <p class="text-muted">
                                    <?php if ($status_filter): ?>
                                        Não há empresas com o status "<?php echo $status_filter; ?>".
                                    <?php else: ?>
                                        Ainda não há empresas cadastradas.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Logo</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Localização</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($companies as $company): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($company['logo']): ?>
                                                        <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="Logo" class="admin-company-logo">
                                                    <?php else: ?>
                                                        <div class="admin-company-logo-placeholder">
                                                            <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($company['nome']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($company['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($company['categoria']); ?></td>
                                                <td><?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $company['status'] == 'aprovada' ? 'success' : ($company['status'] == 'pendente' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($company['status']); ?>
                                                    </span>
                                                    <?php if ($company['destaque']): ?>
                                                        <span class="badge bg-primary">Destaque</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo formatDate($company['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-info" onclick="viewCompany(<?php echo $company['id']; ?>)" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <?php if ($company['status'] == 'pendente'): ?>
                                                            <button type="button" class="btn btn-outline-success" onclick="approveCompany(<?php echo $company['id']; ?>)" title="Aprovar">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" onclick="rejectCompany(<?php echo $company['id']; ?>)" title="Rejeitar">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteCompany(<?php echo $company['id']; ?>)" title="Excluir">
                                                            <i class="fas fa-trash"></i>
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

    <!-- Company Details Modal -->
    <div class="modal fade" id="companyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="companyDetails">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aprovar Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="aprovar">
                        <input type="hidden" name="empresa_id" id="approvalEmpresaId">
                        
                        <p>Tem certeza que deseja aprovar esta empresa?</p>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="destaque" id="destaque">
                            <label class="form-check-label" for="destaque">
                                Marcar como destaque na página inicial
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Aprovar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewCompany(id) {
            // Fetch company details via AJAX (simplified for demo)
            fetch(`../public/empresa-detalhes.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    // Extract content (in production, create a proper API endpoint)
                    document.getElementById('companyDetails').innerHTML = '<p>Detalhes da empresa ID: ' + id + '</p>';
                    new bootstrap.Modal(document.getElementById('companyModal')).show();
                });
        }
        
        function approveCompany(id) {
            document.getElementById('approvalEmpresaId').value = id;
            new bootstrap.Modal(document.getElementById('approvalModal')).show();
        }
        
        function rejectCompany(id) {
            if (confirm('Tem certeza que deseja rejeitar esta empresa?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="rejeitar">
                    <input type="hidden" name="empresa_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCompany(id) {
            if (confirm('Tem certeza que deseja excluir esta empresa? Esta ação não pode ser desfeita.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="excluir">
                    <input type="hidden" name="empresa_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
