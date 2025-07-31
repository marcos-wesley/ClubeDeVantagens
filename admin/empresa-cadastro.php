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
    $cnpj = sanitizeInput($_POST['cnpj']);
    $endereco = sanitizeInput($_POST['endereco']);
    $cidade = sanitizeInput($_POST['cidade']);
    $telefone = sanitizeInput($_POST['telefone']);
    $email = sanitizeInput($_POST['email']);
    $website = sanitizeInput($_POST['website']);
    $regras_beneficio = sanitizeInput($_POST['regras_beneficio']);
    $desconto = (int)sanitizeInput($_POST['desconto']);
    $status = sanitizeInput($_POST['status']);
    $destaque = isset($_POST['destaque']) ? true : false;
    
    // Handle file uploads
    $logo_filename = $empresa['logo'] ?? null;
    $imagem_detalhes_filename = $empresa['imagem_detalhes'] ?? null;
    
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['logo']['type'], $allowed_types) && $_FILES['logo']['size'] <= 5 * 1024 * 1024) {
            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $logo_filename = uniqid() . '.' . $extension;
            $upload_path = $upload_dir . $logo_filename;
            
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                $error = 'Erro ao fazer upload da logo.';
                $logo_filename = $empresa['logo'] ?? null;
            }
        } else {
            $error = 'Arquivo de logo inválido. Use JPG, PNG, WebP ou GIF até 5MB.';
        }
    }
    
    // Handle detail image upload
    if (isset($_FILES['imagem_detalhes']) && $_FILES['imagem_detalhes']['size'] > 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['imagem_detalhes']['type'], $allowed_types) && $_FILES['imagem_detalhes']['size'] <= 5 * 1024 * 1024) {
            $extension = pathinfo($_FILES['imagem_detalhes']['name'], PATHINFO_EXTENSION);
            $imagem_detalhes_filename = uniqid() . '.' . $extension;
            $upload_path = $upload_dir . $imagem_detalhes_filename;
            
            if (!move_uploaded_file($_FILES['imagem_detalhes']['tmp_name'], $upload_path)) {
                $error = 'Erro ao fazer upload da imagem de detalhes.';
                $imagem_detalhes_filename = $empresa['imagem_detalhes'] ?? null;
            }
        } else {
            $error = 'Arquivo de imagem de detalhes inválido. Use JPG, PNG, WebP ou GIF até 5MB.';
        }
    }
    
    if (empty($nome) || empty($categoria) || empty($descricao)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            if ($edit_mode) {
                // Update empresa
                $estado = sanitizeInput($_POST['estado'] ?? '');
                $stmt = $conn->prepare("
                    UPDATE empresas SET 
                    nome = ?, cnpj = ?, categoria = ?, descricao = ?, endereco = ?, 
                    cidade = ?, estado = ?, telefone = ?, email = ?, website = ?, logo = ?, imagem_detalhes = ?,
                    regras = ?, desconto = ?, status = ?, destaque = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $nome, $cnpj, $categoria, $descricao, $endereco, 
                    $cidade, $estado, $telefone, $email, $website, $logo_filename, $imagem_detalhes_filename,
                    $regras_beneficio, $desconto, $status, $destaque, $empresa_id
                ]);
                $message = 'Empresa atualizada com sucesso!';
                
                // Refresh empresa data
                $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
                $stmt->execute([$empresa_id]);
                $empresa = $stmt->fetch();
                
            } else {
                // Insert new empresa
                $estado = sanitizeInput($_POST['estado'] ?? '');
                $stmt = $conn->prepare("
                    INSERT INTO empresas (
                        nome, cnpj, categoria, descricao, endereco, cidade, estado,
                        telefone, email, website, logo, imagem_detalhes, regras, desconto,
                        status, destaque, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([
                    $nome, $cnpj, $categoria, $descricao, $endereco, $cidade, $estado,
                    $telefone, $email, $website, $logo_filename, $imagem_detalhes_filename, $regras_beneficio, 
                    $desconto, $status, $destaque
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
                                        <label class="form-label">CNPJ</label>
                                        <input type="text" class="form-control" name="cnpj" 
                                               value="<?php echo htmlspecialchars($empresa['cnpj'] ?? ''); ?>" 
                                               placeholder="00.000.000/0000-00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Desconto Oferecido (%)</label>
                                        <input type="number" class="form-control" name="desconto" min="0" max="100"
                                               value="<?php echo htmlspecialchars($empresa['desconto'] ?? ''); ?>" 
                                               placeholder="Ex: 20">
                                        <div class="form-text">Percentual de desconto para membros ANETI</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Logo da Empresa</label>
                                <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
                                <?php if (!empty($empresa['logo'])): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/<?php echo htmlspecialchars($empresa['logo']); ?>" alt="Logo atual" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                                        <br><small class="text-muted">Logo atual: <?php echo htmlspecialchars($empresa['logo']); ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Formatos aceitos: JPG, PNG, WebP, GIF. Tamanho máximo: 5MB</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Imagem de Detalhes</label>
                                <input type="file" class="form-control" name="imagem_detalhes" accept="image/jpeg,image/png,image/gif,image/webp">
                                <?php if (!empty($empresa['imagem_detalhes'])): ?>
                                    <div class="mt-2">
                                        <img src="../uploads/<?php echo htmlspecialchars($empresa['imagem_detalhes']); ?>" alt="Imagem de detalhes" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                        <br><small class="text-muted">Imagem atual: <?php echo htmlspecialchars($empresa['imagem_detalhes']); ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Imagem que aparece na página de detalhes. Formatos aceitos: JPG, PNG, WebP, GIF. Tamanho máximo: 5MB</div>
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