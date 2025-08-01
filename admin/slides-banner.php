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
    
    if ($action === 'add' && isset($_FILES['imagem'])) {
        // Debug: verificar dados do arquivo
        $file_error = $_FILES['imagem']['error'] ?? 4;
        $file_size = $_FILES['imagem']['size'] ?? 0;
        $file_type = $_FILES['imagem']['type'] ?? '';
        
        if ($file_error !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'Arquivo muito grande (limite do servidor)',
                UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande (limite do formulário)',
                UPLOAD_ERR_PARTIAL => 'Upload incompleto',
                UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo',
                UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensão'
            ];
            $error = $upload_errors[$file_error] ?? "Erro desconhecido no upload: $file_error";
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($file_type, $allowed_types) && $file_size <= 10 * 1024 * 1024 && $file_size > 0) {
                $upload_dir = '../uploads/slides/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['imagem']['name']);
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $file_path)) {
                    $ordem = (int)$_POST['ordem'];
                    $status = $_POST['status'];
                    $mobile_only = isset($_POST['mobile_only']) ? true : false;
                    
                    if (addBannerSlide($conn, $file_name, $ordem, $status, $mobile_only)) {
                        $success = "Slide adicionado com sucesso!";
                    } else {
                        $error = "Erro ao salvar slide no banco de dados.";
                        // Remover arquivo se falhou salvar no banco
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                } else {
                    $error = "Erro ao fazer upload da imagem. Verifique as permissões da pasta.";
                }
            } else {
                $error = "Arquivo inválido. Use JPG, PNG, WebP ou GIF até 10MB. Tipo: $file_type, Tamanho: " . round($file_size/1024/1024, 2) . "MB";
            }
        }
    }
    
    if ($action === 'toggle' && isset($_POST['id'])) {
        toggleSlideStatus($conn, (int)$_POST['id']);
        $success = "Status do slide alterado com sucesso!";
    }
    
    if ($action === 'delete' && isset($_POST['id'])) {
        // Buscar nome do arquivo para deletar
        $stmt = $conn->prepare("SELECT imagem FROM slides_banner WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);
        $slide = $stmt->fetch();
        
        if ($slide && deleteBannerSlide($conn, (int)$_POST['id'])) {
            // Deletar arquivo físico
            $file_path = '../uploads/slides/' . $slide['imagem'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $success = "Slide deletado com sucesso!";
        } else {
            $error = "Erro ao deletar slide.";
        }
    }
}

// Buscar todos os slides
$slides = getAllBannerSlides($conn);

$page_title = "Slides do Banner";
include 'includes/admin-header.php';
?>
<style>
    .slide-preview {
        width: 150px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
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

        <!-- Formulário Adicionar Slide -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Adicionar Novo Slide</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imagem" class="form-label">Imagem do Slide *</label>
                                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg,image/png,image/gif,image/webp" required>
                                <div class="form-text">Tamanho recomendado: 1920x500px. Formatos aceitos: JPG, PNG, WebP, GIF. Tamanho máximo: 10MB</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="ordem" class="form-label">Ordem</label>
                                <input type="number" class="form-control" id="ordem" name="ordem" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mobile_only" name="mobile_only" value="1">
                                    <label class="form-check-label" for="mobile_only">
                                        <i class="fas fa-mobile-alt me-1"></i>Somente para dispositivos móveis
                                    </label>
                                    <div class="form-text">Se marcado, este slide será exibido apenas em smartphones e tablets</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Adicionar Slide
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Slides -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Slides Cadastrados</h5>
            </div>
            <div class="card-body">
                <?php if (empty($slides)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum slide cadastrado ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Ordem</th>
                                    <th>Status</th>
                                    <th>Tipo</th>
                                    <th>Data Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($slides as $slide): ?>
                                    <tr>
                                        <td>
                                            <img src="../uploads/slides/<?php echo htmlspecialchars($slide['imagem']); ?>" 
                                                 alt="Slide" class="slide-preview">
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $slide['ordem']; ?></span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $slide['status']; ?>">
                                                <?php echo ucfirst($slide['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($slide['mobile_only']) && $slide['mobile_only']): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-mobile-alt me-1"></i>Mobile
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-desktop me-1"></i>Desktop
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($slide['data_criacao'])); ?>
                                        </td>
                                        <td>
                                            <!-- Toggle Status -->
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" 
                                                        title="<?php echo $slide['status'] === 'ativo' ? 'Desativar' : 'Ativar'; ?>">
                                                    <i class="fas fa-<?php echo $slide['status'] === 'ativo' ? 'eye-slash' : 'eye'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete -->
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja deletar este slide?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>