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
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .admin-login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .admin-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(1, 45, 106, 0.15);
            background: white;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .aneti-header {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
        }
        .aneti-header::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
        .aneti-header h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: white;
        }
        .aneti-header p {
            margin: 0;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }
        .card-body {
            padding: 2.5rem 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .form-control:focus {
            border-color: #012d6a;
            box-shadow: 0 0 0 0.2rem rgba(1, 45, 106, 0.15);
            background: white;
            transform: translateY(-1px);
        }
        .aneti-btn {
            background: #012d6a;
            border-color: #012d6a;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.875rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(1, 45, 106, 0.2);
        }
        .aneti-btn:hover {
            background: #25a244;
            border-color: #25a244;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 162, 68, 0.3);
        }
        .aneti-btn:active {
            transform: translateY(0);
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        .back-link a {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .back-link a:hover {
            color: #012d6a;
            transform: translateX(-2px);
        }
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .mb-4 {
            margin-bottom: 2rem !important;
        }
        /* Responsivo */
        @media (max-width: 768px) {
            .admin-login-container {
                padding: 1rem;
            }
            .admin-card {
                max-width: 100%;
            }
            .aneti-header {
                padding: 2rem 1.5rem 1.5rem;
            }
            .card-body {
                padding: 2rem 1.5rem;
            }
            .aneti-header h4 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="card admin-card">
            <div class="aneti-header">
                <h4><i class="fas fa-user-shield me-2"></i>Admin Login</h4>
                <p>Clube de Vantagens ANETI</p>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>E-mail Administrativo
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="Digite seu e-mail de administrador">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Senha
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required
                               placeholder="Digite sua senha">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn aneti-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Acessar Painel Administrativo
                        </button>
                    </div>
                </form>
                
                <div class="back-link">
                    <a href="../index.php">
                        <i class="fas fa-arrow-left"></i>
                        <span>Voltar ao site</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
