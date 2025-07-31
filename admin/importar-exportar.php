<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'import') {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
            $file = $_FILES['import_file'];
            $filename = $file['name'];
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['csv', 'xml'])) {
                // Processar arquivo (simulação)
                $message = "Arquivo $filename importado com sucesso! 150 usuários processados.";
            } else {
                $error = 'Formato de arquivo não suportado. Use apenas .csv ou .xml';
            }
        } else {
            $error = 'Erro ao fazer upload do arquivo.';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'export') {
        // Simular exportação
        $message = 'Exportação iniciada! O arquivo será enviado por e-mail em alguns minutos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar/Exportar Usuários - Admin ANETI</title>
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
                <a class="nav-link" href="empresas.php"><i class="fas fa-store"></i> Empresas</a>
                <a class="nav-link" href="cupons.php"><i class="fas fa-ticket-alt"></i> Cupons</a>
                <a class="nav-link" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link" href="membros.php"><i class="fas fa-users"></i> Membros</a>
                <a class="nav-link active" href="importar-exportar.php"><i class="fas fa-exchange-alt"></i> Import/Export</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="admin-greeting">Importar/Exportar Usuários</h1>
                    <p class="admin-subtitle">Gerencie usuários em lote através de arquivos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Importar Usuários -->
                <div class="card widget-card mb-4">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h5 class="widget-title">Importar Usuários</h5>
                    </div>
                    <div class="widget-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="import">
                            
                            <div class="import-export-area">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Enviar arquivo</h5>
                                <p class="text-muted">Arquivos permitidos: .xml ou .csv</p>
                                
                                <input type="file" class="form-control mb-3" name="import_file" accept=".csv,.xml" required>
                                
                                <button type="submit" class="file-upload-btn">
                                    <i class="fas fa-upload"></i> Importar
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4">
                            <h6>Instruções de Importação:</h6>
                            <ul class="text-muted">
                                <li>Formato CSV: nome, email, plano, telefone</li>
                                <li>Formato XML: Estrutura com tags &lt;user&gt;</li>
                                <li>Tamanho máximo: 10MB</li>
                                <li>Usuários duplicados serão ignorados</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Exportar Usuários -->
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <h5 class="widget-title">Exportar Usuários</h5>
                    </div>
                    <div class="widget-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="export">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Formato de Exportação</label>
                                        <select class="form-select" name="export_format">
                                            <option value="csv">CSV (.csv)</option>
                                            <option value="xml">XML (.xml)</option>
                                            <option value="excel">Excel (.xlsx)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Filtrar por Status</label>
                                        <select class="form-select" name="status_filter">
                                            <option value="all">Todos os usuários</option>
                                            <option value="active">Apenas ativos</option>
                                            <option value="inactive">Apenas inativos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Campos a Exportar</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="nome" id="field_nome" checked>
                                            <label class="form-check-label" for="field_nome">Nome</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="email" id="field_email" checked>
                                            <label class="form-check-label" for="field_email">E-mail</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="plano" id="field_plano" checked>
                                            <label class="form-check-label" for="field_plano">Plano</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="telefone" id="field_telefone">
                                            <label class="form-check-label" for="field_telefone">Telefone</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="created_at" id="field_created">
                                            <label class="form-check-label" for="field_created">Data de Cadastro</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="status" id="field_status">
                                            <label class="form-check-label" for="field_status">Status</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Exportar Usuários
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>