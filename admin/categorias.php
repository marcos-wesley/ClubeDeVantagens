<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add') {
            $nome = sanitizeInput($_POST['nome']);
            $descricao = sanitizeInput($_POST['descricao']);
            
            if (empty($nome)) {
                $error = 'Nome da categoria é obrigatório.';
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO categorias (nome, descricao, created_at) VALUES (?, ?, NOW())");
                    $stmt->execute([$nome, $descricao]);
                    $message = 'Categoria adicionada com sucesso!';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'Esta categoria já existe.';
                    } else {
                        $error = 'Erro ao adicionar categoria.';
                    }
                }
            }
        } elseif ($action == 'edit') {
            $id = (int)$_POST['id'];
            $nome = sanitizeInput($_POST['nome']);
            $descricao = sanitizeInput($_POST['descricao']);
            
            if (empty($nome)) {
                $error = 'Nome da categoria é obrigatório.';
            } else {
                try {
                    $stmt = $conn->prepare("UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?");
                    $stmt->execute([$nome, $descricao, $id]);
                    $message = 'Categoria atualizada com sucesso!';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'Esta categoria já existe.';
                    } else {
                        $error = 'Erro ao atualizar categoria.';
                    }
                }
            }
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if category is being used
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM empresas WHERE categoria = (SELECT nome FROM categorias WHERE id = ?)");
            $stmt->execute([$id]);
            $usage = $stmt->fetch()['total'];
            
            if ($usage > 0) {
                $error = "Não é possível excluir esta categoria pois ela está sendo usada por {$usage} empresa(s).";
            } else {
                $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Categoria excluída com sucesso!';
            }
        }
    }
}

// Get all categories
$categories = $conn->query("SELECT * FROM categorias ORDER BY nome ASC")->fetchAll();

// Get category usage statistics
$category_stats = [];
foreach ($categories as $category) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM empresas WHERE categoria = ?");
    $stmt->execute([$category['nome']]);
    $category_stats[$category['id']] = $stmt->fetch()['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Admin</title>
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
                <a class="nav-link active" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tags"></i> Gerenciar Categorias</h2>
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

                <!-- Categories List -->
                <div class="card">
                    <div class="card-header">
                        <h5>Lista de Categorias (<?php echo count($categories); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <h5>Nenhuma categoria cadastrada</h5>
                                <p class="text-muted">Adicione a primeira categoria usando o formulário ao lado.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Empresas</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-<?php echo getCategoryIcon($category['nome']); ?> me-2 text-primary"></i>
                                                        <strong><?php echo htmlspecialchars($category['nome']); ?></strong>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($category['descricao']); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo $category_stats[$category['id']]; ?> empresa(s)</span>
                                                </td>
                                                <td><?php echo formatDate($category['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['nome']); ?>', '<?php echo htmlspecialchars($category['descricao']); ?>')" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <?php if ($category_stats[$category['id']] == 0): ?>
                                                            <button type="button" class="btn btn-outline-danger" onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['nome']); ?>')" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-secondary" disabled title="Não pode ser excluída (em uso)">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                        <?php endif; ?>
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

            <!-- Add/Edit Category Form -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 id="formTitle"><i class="fas fa-plus"></i> Adicionar Categoria</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="categoryForm">
                            <input type="hidden" name="action" id="formAction" value="add">
                            <input type="hidden" name="id" id="categoryId">
                            
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Categoria *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="50">
                            </div>
                            
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3" maxlength="255"></textarea>
                                <div class="form-text">Breve descrição da categoria (opcional)</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-plus"></i> Adicionar Categoria
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="cancelBtn" onclick="resetForm()" style="display: none;">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Popular Categories Suggestions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6><i class="fas fa-lightbulb"></i> Sugestões de Categorias</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $suggestions = ['Alimentação', 'Tecnologia', 'Educação', 'Saúde', 'Beleza', 'Viagem', 'Esporte', 'Entretenimento', 'Compras', 'Serviços', 'Automotivo', 'Casa e Decoração'];
                            foreach ($suggestions as $suggestion): 
                                $exists = false;
                                foreach ($categories as $cat) {
                                    if ($cat['nome'] == $suggestion) {
                                        $exists = true;
                                        break;
                                    }
                                }
                                if (!$exists):
                            ?>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="quickAdd('<?php echo $suggestion; ?>')">
                                    <?php echo $suggestion; ?>
                                </button>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <small class="text-muted mt-2 d-block">Clique para adicionar rapidamente</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a categoria <strong id="deleteCategoria"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCategory(id, nome, descricao) {
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Editar Categoria';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('categoryId').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('descricao').value = descricao;
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Salvar Alterações';
            document.getElementById('cancelBtn').style.display = 'block';
        }
        
        function resetForm() {
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus"></i> Adicionar Categoria';
            document.getElementById('formAction').value = 'add';
            document.getElementById('categoryId').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('descricao').value = '';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Adicionar Categoria';
            document.getElementById('cancelBtn').style.display = 'none';
        }
        
        function deleteCategory(id, nome) {
            document.getElementById('deleteCategoria').textContent = nome;
            document.getElementById('deleteId').value = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        function quickAdd(nome) {
            resetForm();
            document.getElementById('nome').value = nome;
            document.getElementById('nome').focus();
        }
    </script>
</body>
</html>
