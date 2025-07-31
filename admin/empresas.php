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
<body class="admin-body">
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
                <a class="nav-link" href="membros.php"><i class="fas fa-users"></i> Membros</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="admin-greeting">Gerenciar Empresas</h1>
                    <p class="admin-subtitle">Controle de empresas parceiras e benefícios</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="search-filters">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Pesquisar empresa">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option>Todas as categorias</option>
                                <option>Automotivo</option>
                                <option>Alimentação</option>
                                <option>Saúde</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option>Todos os status</option>
                                <option>Aprovada</option>
                                <option>Pendente</option>
                                <option>Rejeitada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Aplicar</button>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="empresa-cadastro.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nova Empresa
                            </a>
                        </div>
                    </div>
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
                                                <td><?php echo htmlspecialchars($company['cidade']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $company['status'] == 'aprovada' ? 'success' : ($company['status'] == 'pendente' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($company['status']); ?>
                                                    </span>
                                                    <?php if ($company['destaque']): ?>
                                                        <span class="badge bg-primary">Destaque</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="empresa-cadastro.php?id=<?php echo $company['id']; ?>" class="btn btn-outline-primary btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="viewCompany(<?php echo $company['id']; ?>)" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <?php if ($company['status'] == 'pendente'): ?>
                                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="approveCompany(<?php echo $company['id']; ?>)" title="Aprovar">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="rejectCompany(<?php echo $company['id']; ?>)" title="Rejeitar">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCompany(<?php echo $company['id']; ?>)" title="Excluir">
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
            fetch(`empresa-detalhes-api.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const company = data.company;
                        document.getElementById('companyDetails').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-store"></i> Informações Básicas</h6>
                                    <p><strong>Nome:</strong> ${company.nome}</p>
                                    <p><strong>Categoria:</strong> ${company.categoria}</p>
                                    <p><strong>Email:</strong> ${company.email || 'Não informado'}</p>
                                    <p><strong>Telefone:</strong> ${company.telefone || 'Não informado'}</p>
                                    <p><strong>Website:</strong> ${company.website || 'Não informado'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-map-marker-alt"></i> Localização</h6>
                                    <p><strong>Cidade:</strong> ${company.cidade || 'Não informado'}</p>
                                    <p><strong>Endereço:</strong> ${company.endereco || 'Não informado'}</p>
                                    <h6><i class="fas fa-info-circle"></i> Status</h6>
                                    <p><strong>Status:</strong> <span class="badge bg-${company.status == 'aprovada' ? 'success' : (company.status == 'pendente' ? 'warning' : 'danger')}">${company.status}</span></p>
                                    <p><strong>Destaque:</strong> ${company.destaque ? 'Sim' : 'Não'}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6><i class="fas fa-align-left"></i> Descrição</h6>
                                <p>${company.descricao}</p>
                                <h6><i class="fas fa-gift"></i> Regras do Benefício</h6>
                                <p>${company.regras_beneficio || 'Não informado'}</p>
                            </div>
                        `;
                        new bootstrap.Modal(document.getElementById('companyModal')).show();
                    } else {
                        alert('Erro ao carregar detalhes da empresa');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao carregar detalhes da empresa');
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
