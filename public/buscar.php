<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$query = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';
$categoria = isset($_GET['categoria']) ? sanitizeInput($_GET['categoria']) : '';
$cidade = isset($_GET['cidade']) ? sanitizeInput($_GET['cidade']) : '';

$companies = searchCompanies($conn, $query, $categoria, $cidade);
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Empresas - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-filter"></i> Filtros</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="mb-3">
                                <label for="q" class="form-label">Buscar por palavra-chave</label>
                                <input type="text" class="form-control" id="q" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Nome, descrição...">
                            </div>
                            
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Todas as categorias</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['nome']); ?>" <?php echo $categoria === $cat['nome'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo htmlspecialchars($cidade); ?>" placeholder="Digite a cidade">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            
                            <?php if ($query || $categoria || $cidade): ?>
                                <div class="d-grid mt-2">
                                    <a href="buscar.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Limpar Filtros
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <?php if ($query || $categoria || $cidade): ?>
                            Resultados da Busca
                        <?php else: ?>
                            Todas as Empresas
                        <?php endif; ?>
                    </h2>
                    <span class="badge bg-secondary"><?php echo count($companies); ?> resultado(s)</span>
                </div>
                
                <?php if ($query || $categoria || $cidade): ?>
                    <div class="mb-3">
                        <small class="text-muted">
                            Filtros ativos:
                            <?php if ($query): ?>
                                <span class="badge bg-light text-dark me-1">Termo: "<?php echo htmlspecialchars($query); ?>"</span>
                            <?php endif; ?>
                            <?php if ($categoria): ?>
                                <span class="badge bg-light text-dark me-1">Categoria: <?php echo htmlspecialchars($categoria); ?></span>
                            <?php endif; ?>
                            <?php if ($cidade): ?>
                                <span class="badge bg-light text-dark me-1">Cidade: <?php echo htmlspecialchars($cidade); ?></span>
                            <?php endif; ?>
                        </small>
                    </div>
                <?php endif; ?>

                <?php if (empty($companies)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Nenhuma empresa encontrada</h4>
                        <p class="text-muted">Tente ajustar os filtros de busca ou navegue por todas as empresas.</p>
                        <a href="buscar.php" class="btn btn-primary">Ver Todas as Empresas</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($companies as $company): ?>
                            <div class="col-lg-6 col-md-6 mb-4">
                                <div class="company-card h-100">
                                    <div class="company-card-header">
                                        <?php if ($company['logo']): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['nome']); ?>" class="company-logo-img">
                                        <?php else: ?>
                                            <div class="company-logo-placeholder">
                                                <?php echo strtoupper(substr($company['nome'], 0, 2)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="company-card-body">
                                        <h5 class="company-card-title"><?php echo htmlspecialchars($company['nome']); ?></h5>
                                        <p class="company-card-category">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($company['categoria']); ?></span>
                                        </p>
                                        <p class="company-card-description"><?php echo htmlspecialchars(substr($company['descricao'], 0, 120)); ?>...</p>
                                        <div class="company-card-footer">
                                            <span class="company-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($company['cidade']); ?>, <?php echo htmlspecialchars($company['estado']); ?>
                                            </span>
                                            <a href="empresa-detalhes.php?id=<?php echo $company['id']; ?>" class="btn btn-primary btn-sm">
                                                Ver detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
