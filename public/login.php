<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$error = '';
$success = '';

if ($_POST) {
    $email = sanitizeInput($_POST['email']);
    
    if (empty($email)) {
        $error = 'Por favor, informe seu e-mail.';
    } elseif (!validateEmail($email)) {
        $error = 'E-mail inválido.';
    } else {
        if (loginUser($conn, $email)) {
            redirect('dashboard.php');
        } else {
            $error = 'E-mail não encontrado ou usuário inativo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: calc(100vh - 180px);
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
        }
        .aneti-btn {
            background: #012d6a;
            border-color: #012d6a;
            color: white;
            transition: all 0.3s ease;
        }
        .aneti-btn:hover {
            background: #25a244;
            border-color: #25a244;
            color: white;
        }
        .aneti-header {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow login-card mx-auto">
                    <div class="card-header text-white text-center aneti-header">
                        <h4 class="mb-0"><i class="fas fa-user-circle"></i> Login de Membro</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="form-text">Use seu e-mail cadastrado na ANETI</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn aneti-btn">
                                    <i class="fas fa-sign-in-alt"></i> Entrar
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <p class="mb-0">Ainda não é membro da ANETI?</p>
                            <small class="text-muted">Entre em contato conosco para se associar</small>
                        </div>
                    </div>
                </div>
                
                <!-- Demo Users -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6>Usuários de Demo</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            Para testar o sistema, use um dos e-mails abaixo:<br>
                            • joao.silva@email.com (Plano Júnior)<br>
                            • maria.santos@email.com (Plano Pleno)<br>
                            • carlos.oliveira@email.com (Plano Sênior)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
