<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';
$error = '';
$empresa = null;
$edit_mode = false;

// Check if editing
if (isset($_GET['id'])) {
    $edit_mode = true;
    $empresa_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->execute([$empresa_id]);
    $empresa = $stmt->fetch();
    
    if (!$empresa) {
        header('Location: empresas.php');
        exit();
    }
}

// Handle form submission
if ($_POST) {
    $nome = sanitizeInput($_POST['nome']);
    $categoria = sanitizeInput($_POST['categoria']);
    $descricao = sanitizeInput($_POST['descricao']);
    $endereco = sanitizeInput($_POST['endereco']);
    $cidade = sanitizeInput($_POST['cidade']);
    $telefone = sanitizeInput($_POST['telefone']);
    $email = sanitizeInput($_POST['email']);
    $website = sanitizeInput($_POST['website']);
    $regras_beneficio = sanitizeInput($_POST['regras_beneficio']);
    $desconto = sanitizeInput($_POST['desconto']);
    $avaliacao = sanitizeInput($_POST['avaliacao']);
    $status = sanitizeInput($_POST['status']);
    $destaque = isset($_POST['destaque']) ? true : false;
    
    // Handle logo upload
    $logo_filename = $empresa['logo'] ?? null;
    if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
        $upload_result = uploadFile($_FILES['logo']);
        if ($upload_result['success']) {
            $logo_filename = $upload_result['filename'];
        } else {
            $error = $upload_result['message'];
        }
    }
    
    if (empty($nome) || empty($categoria) || empty($descricao)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            if ($edit_mode) {
                // Update empresa
                $stmt = $conn->prepare("
                    UPDATE empresas SET 
                    nome = ?, categoria = ?, descricao = ?, endereco = ?, 
                    cidade = ?, estado = ?, telefone = ?, email = ?, website = ?, logo = ?,
                    regras = ?, desconto = ?, avaliacao_media = ?, status = ?, destaque = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $estado = sanitizeInput($_POST['estado']);
                $stmt->execute([
                    $nome, $categoria, $descricao, $endereco, 
                    $cidade, $estado, $telefone, $email, $website, $logo_filename,
                    $regras_beneficio, $desconto, $avaliacao, $status, $destaque, $empresa_id
                ]);
                $message = 'Empresa atualizada com sucesso!';
                
                // Refresh empresa data
                $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
                $stmt->execute([$empresa_id]);
                $empresa = $stmt->fetch();
                
            } else {
                // Insert new empresa
                $stmt = $conn->prepare("
                    INSERT INTO empresas (
                        nome, categoria, descricao, endereco, cidade, estado,
                        telefone, email, website, logo, regras, desconto, avaliacao_media,
                        status, destaque, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $estado = sanitizeInput($_POST['estado']);
                $stmt->execute([
                    $nome, $categoria, $descricao, $endereco, $cidade, $estado,
                    $telefone, $email, $website, $logo_filename, $regras_beneficio, 
                    $desconto, $avaliacao, $status, $destaque
                ]);
                $message = 'Empresa cadastrada com sucesso!';
            }
        } catch (Exception $e) {
            $error = 'Erro ao salvar empresa: ' . $e->getMessage();
        }
    }
}

// Get categories for dropdown
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Editar' : 'Cadastrar'; ?> Empresa - Admin</title>
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
                <a class="nav-link" href="membros.php"><i class="fas fa-users"></i> Membros</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-store"></i> <?php echo $edit_mode ? 'Editar' : 'Cadastrar'; ?> Empresa</h2>
                    <a href="empresas.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

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

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nome da Empresa *</label>
                                        <input type="text" class="form-control" name="nome" 
                                               value="<?php echo htmlspecialchars($empresa['nome'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Categoria *</label>
                                        <select class="form-select" name="categoria" required>
                                            <option value="">Selecione uma categoria</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo htmlspecialchars($category['nome']); ?>"
                                                        <?php echo ($empresa['categoria'] ?? '') == $category['nome'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição *</label>
                                <textarea class="form-control" name="descricao" rows="3" required><?php echo htmlspecialchars($empresa['descricao'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Endereço</label>
                                        <input type="text" class="form-control" name="endereco" 
                                               value="<?php echo htmlspecialchars($empresa['endereco'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Cidade</label>
                                        <input type="text" class="form-control" name="cidade" 
                                               value="<?php echo htmlspecialchars($empresa['cidade'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" name="estado" maxlength="2" placeholder="SP"
                                               value="<?php echo htmlspecialchars($empresa['estado'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control" name="telefone" 
                                               value="<?php echo htmlspecialchars($empresa['telefone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($empresa['email'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" class="form-control" name="website" 
                                               value="<?php echo htmlspecialchars($empresa['website'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Desconto Oferecido (%)</label>
                                        <input type="number" class="form-control" name="desconto" min="1" max="100"
                                               value="<?php echo htmlspecialchars($empresa['desconto'] ?? ''); ?>" 
                                               placeholder="Ex: 20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Avaliação Média</label>
                                        <select class="form-select" name="avaliacao">
                                            <option value="">Sem avaliação</option>
                                            <option value="1" <?php echo ($empresa['avaliacao'] ?? '') == '1' ? 'selected' : ''; ?>>⭐ (1.0)</option>
                                            <option value="2" <?php echo ($empresa['avaliacao'] ?? '') == '2' ? 'selected' : ''; ?>>⭐⭐ (2.0)</option>
                                            <option value="3" <?php echo ($empresa['avaliacao'] ?? '') == '3' ? 'selected' : ''; ?>>⭐⭐⭐ (3.0)</option>
                                            <option value="4" <?php echo ($empresa['avaliacao'] ?? '') == '4' ? 'selected' : ''; ?>>⭐⭐⭐⭐ (4.0)</option>
                                            <option value="5" <?php echo ($empresa['avaliacao'] ?? '') == '5' ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ (5.0)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Logo da Empresa</label>
                                <input type="file" class="form-control" name="logo" accept="image/*">
                                <?php if (!empty($empresa['logo'])): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Logo atual: <?php echo htmlspecialchars($empresa['logo']); ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 5MB</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Regras do Benefício</label>
                                <textarea class="form-control" name="regras_beneficio" rows="3"><?php echo htmlspecialchars($empresa['regras'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="pendente" <?php echo ($empresa['status'] ?? 'pendente') == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                            <option value="aprovada" <?php echo ($empresa['status'] ?? '') == 'aprovada' ? 'selected' : ''; ?>>Aprovada</option>
                                            <option value="rejeitada" <?php echo ($empresa['status'] ?? '') == 'rejeitada' ? 'selected' : ''; ?>>Rejeitada</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="destaque" id="destaque"
                                                   <?php echo ($empresa['destaque'] ?? false) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="destaque">
                                                <i class="fas fa-star text-warning"></i> Empresa em Destaque
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="empresas.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo $edit_mode ? 'Atualizar' : 'Cadastrar'; ?>
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