<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/wordpress_api.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_POST) {
    $userInput = sanitizeInput($_POST['user_input'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    
    if (empty($userInput)) {
        $error = 'Por favor, informe seu usuário ou e-mail.';
    } elseif (empty($password)) {
        $error = 'Por favor, informe sua senha.';
    } else {
        // Use WordPress API for authentication (accepts both username and email)
        $loginResult = loginUserViaAPI($userInput, $password);
        
        if ($loginResult['success']) {
            redirect('dashboard.php');
        } else {
            $error = $loginResult['message'];
            $showMembershipLink = isset($loginResult['show_membership_link']) && $loginResult['show_membership_link'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php 
    require_once '../includes/seo.php';
    
    $seo_config = [
        'title' => 'Entrar | Clube de Vantagens ANETI',
        'description' => 'Acesse sua conta no Clube de Vantagens ANETI e aproveite todos os benefícios exclusivos para membros da associação.',
        'keywords' => 'login clube ANETI, entrar clube vantagens, acesso benefícios ANETI, login associado',
        'canonical' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . 
                      $_SERVER['HTTP_HOST'] . '/public/login.php',
        'type' => 'website'
    ];
    
    renderSEO($seo_config);
    ?>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-top: 150px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: calc(100vh - 150px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            border: none;
        }
        
        .login-header {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: white;
        }
        
        .login-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .login-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #012d6a;
            box-shadow: 0 0 0 3px rgba(1, 45, 106, 0.1);
            transform: translateY(-2px);
        }
        
        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .aneti-btn {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.875rem 2rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .aneti-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(1, 45, 106, 0.3);
            color: white;
        }
        
        .aneti-btn:active {
            transform: translateY(0);
        }
        
        .membership-link {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }
        
        .membership-link p {
            margin: 0 0 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .membership-link a {
            color: #012d6a;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .membership-link a:hover {
            color: #25a244;
            text-decoration: none;
            transform: translateX(5px);
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        /* Animações */
        .login-card {
            animation: slideInUp 0.6s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            body {
                padding-top: 130px;
            }
            
            .login-container {
                padding: 1rem;
                min-height: calc(100vh - 130px);
            }
            
            .login-header {
                padding: 2rem 1.5rem 1.5rem;
            }
            
            .login-header i {
                font-size: 2.5rem;
            }
            
            .login-header h3 {
                font-size: 1.3rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-shield"></i>
                <h3>Login de Membro</h3>
                <p>Acesse sua conta do Clube de Vantagens ANETI</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="user_input" name="user_input" 
                               placeholder="Usuário ou E-mail" 
                               value="<?php echo htmlspecialchars($_POST['user_input'] ?? ''); ?>" required>
                        <label for="user_input">
                            <i class="fas fa-user me-2"></i>Usuário ou E-mail
                        </label>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Senha" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Senha
                        </label>
                    </div>

                    <button type="submit" class="aneti-btn">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </form>

                <?php if (isset($showMembershipLink) && $showMembershipLink): ?>
                <div class="membership-link">
                    <p><strong>Anuidade não ativa?</strong></p>
                    <a href="http://aneti.org.br/" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        Ativar minha anuidade ANETI
                    </a>
                </div>
                <?php else: ?>
                <div class="membership-link">
                    <p>Ainda não é membro da ANETI?</p>
                    <a href="http://aneti.org.br/" target="_blank">
                        <i class="fas fa-user-plus"></i>
                        Clique aqui para se associar
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
