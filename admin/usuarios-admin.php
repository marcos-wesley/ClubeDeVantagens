<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se é admin (implementar conforme sistema de autenticação)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

// Processar ações
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $nivel = $_POST['nivel'];
        
        if (!empty($nome) && !empty($email) && !empty($senha)) {
            // Verificar se email já existe
            $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Este email já está cadastrado.";
            } else {
                // Criar hash da senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                try {
                    $stmt = $conn->prepare("INSERT INTO admins (nome, email, nivel, status, senha) VALUES (?, ?, ?, 'ativo', ?)");
                    if ($stmt->execute([$nome, $email, $nivel, $senha_hash])) {
                        $success = "Usuário administrativo criado com sucesso!";
                    } else {
                        $error = "Erro ao criar usuário administrativo.";
                    }
                } catch (Exception $e) {
                    $error = "Erro ao criar usuário: " . $e->getMessage();
                }
            }
        } else {
            $error = "Todos os campos são obrigatórios.";
        }
    }
    
    if ($action === 'toggle' && isset($_POST['id'])) {
        try {
            $stmt = $conn->prepare("UPDATE admins SET status = CASE WHEN status = 'ativo' THEN 'inativo' ELSE 'ativo' END WHERE id = ?");
            if ($stmt->execute([(int)$_POST['id']])) {
                $success = "Status do usuário alterado com sucesso!";
            } else {
                $error = "Erro ao alterar status do usuário.";
            }
        } catch (Exception $e) {
            $error = "Erro ao alterar status: " . $e->getMessage();
        }
    }
    
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
            if ($stmt->execute([(int)$_POST['id']])) {
                $success = "Usuário administrativo deletado com sucesso!";
            } else {
                $error = "Erro ao deletar usuário.";
            }
        } catch (Exception $e) {
            $error = "Erro ao deletar usuário: " . $e->getMessage();
        }
    }
    
    if ($action === 'reset_password' && isset($_POST['id'])) {
        $nova_senha = $_POST['nova_senha'];
        
        if (!empty($nova_senha)) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            
            try {
                $stmt = $conn->prepare("UPDATE admins SET senha = ? WHERE id = ?");
                if ($stmt->execute([$senha_hash, (int)$_POST['id']])) {
                    $success = "Senha alterada com sucesso!";
                } else {
                    $error = "Erro ao alterar senha.";
                }
            } catch (Exception $e) {
                $error = "Erro ao alterar senha: " . $e->getMessage();
            }
        } else {
            $error = "Nova senha é obrigatória.";
        }
    }
}

// Buscar todos os usuários administrativos
try {
    $stmt = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
    $usuarios = $stmt->fetchAll();
} catch (Exception $e) {
    $usuarios = [];
    $error = "Erro ao buscar usuários: " . $e->getMessage();
}
$page_title = "Usuários Administrativos";
include 'includes/admin-header.php';
?>
<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-ativo {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inativo {
        background: #f8d7da;
        color: #721c24;
    }
    
    .nivel-badge {
        padding: 4px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .nivel-super {
        background: #e1ecf4;
        color: #0056b3;
    }
    
    .nivel-admin {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .nivel-editor {
        background: #d4edda;
        color: #155724;
    }
    
    .card-header {
        background: var(--aneti-primary);
        color: white;
    }
    
    .btn-primary {
        background: var(--aneti-primary);
        border-color: var(--aneti-primary);
    }
    
    .btn-primary:hover {
        background: #001f4d;
        border-color: #001f4d;
    }
</style>

    <div class="container-fluid mt-4">
        <!-- Mensagens -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulário Adicionar Usuário -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Adicionar Novo Usuário Administrativo</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nível de Acesso</label>
                                <select class="form-select" id="nivel" name="nivel">
                                    <option value="editor">Editor</option>
                                    <option value="admin">Administrador</option>
                                    <option value="super">Super Administrador</option>
                                </select>
                                <div class="form-text">
                                    <small>
                                        <strong>Editor:</strong> Gerencia conteúdo básico<br>
                                        <strong>Admin:</strong> Acesso completo exceto usuários<br>
                                        <strong>Super:</strong> Acesso total ao sistema
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Criar Usuário
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Usuários -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Usuários Cadastrados</h5>
            </div>
            <div class="card-body">
                <?php if (empty($usuarios)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum usuário administrativo cadastrado ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Nível</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($usuario['email']); ?>
                                        </td>
                                        <td>
                                            <span class="nivel-badge nivel-<?php echo $usuario['nivel']; ?>">
                                                <?php echo ucfirst($usuario['nivel']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $usuario['status']; ?>">
                                                <?php echo ucfirst($usuario['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($usuario['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Toggle Status -->
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-secondary" 
                                                            title="<?php echo $usuario['status'] === 'ativo' ? 'Desativar' : 'Ativar'; ?>">
                                                        <i class="fas fa-<?php echo $usuario['status'] === 'ativo' ? 'pause' : 'play'; ?>"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Reset Password -->
                                                <button type="button" class="btn btn-outline-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#resetPasswordModal<?php echo $usuario['id']; ?>"
                                                        title="Alterar Senha">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                
                                                <!-- Delete -->
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja deletar este usuário?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger" title="Deletar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal Reset Password -->
                                    <div class="modal fade" id="resetPasswordModal<?php echo $usuario['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Alterar Senha - <?php echo htmlspecialchars($usuario['nome']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="reset_password">
                                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="nova_senha<?php echo $usuario['id']; ?>" class="form-label">Nova Senha</label>
                                                            <input type="password" class="form-control" 
                                                                   id="nova_senha<?php echo $usuario['id']; ?>" 
                                                                   name="nova_senha" required minlength="6">
                                                            <div class="form-text">Mínimo 6 caracteres</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>