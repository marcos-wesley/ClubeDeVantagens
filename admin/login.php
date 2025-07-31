<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_POST) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (!validateEmail($email)) {
        $error = 'E-mail inválido.';
    } else {
        if (loginAdmin($conn, $email, $password)) {
            redirect('index.php');
        } else {
            $error = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h4><i class="fas fa-user-shield"></i> Admin Login</h4>
                        <p class="mb-0">Clube de Vantagens ANETI</p>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark">
                                    <i class="fas fa-sign-in-alt"></i> Entrar
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <a href="../index.php" class="text-muted">
                                <i class="fas fa-arrow-left"></i> Voltar ao site
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Demo Admin -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6>Admin de Demo</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            Para testar o sistema administrativo:<br>
                            <strong>E-mail:</strong> admin@aneti.net.br<br>
                            <strong>Senha:</strong> admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
