<?php
// Verificar se sessão admin está ativa (implementar conforme sistema de autenticação)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

// Definir página atual para menu ativo
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin ANETI'; ?> - Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --aneti-primary: #012d6a;
            --aneti-secondary: #25a244;
            --aneti-dark: #001f4d;
        }
        
        .admin-navbar {
            background: linear-gradient(to right, var(--aneti-primary), var(--aneti-secondary));
            padding: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(1, 45, 106, 0.3);
        }
        
        .admin-navbar .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .admin-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            margin: 0 0.2rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .nav-link:hover,
        .admin-navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }
        
        .admin-navbar .dropdown-menu {
            background: white;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .admin-navbar .badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.5rem;
        }
        
        .admin-user-menu {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 0.4rem 1rem;
        }
        
        .admin-user-menu:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        @media (max-width: 991px) {
            .admin-navbar .navbar-collapse {
                background: rgba(1, 45, 106, 0.95);
                margin-top: 1rem;
                padding: 1rem;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cog me-2"></i>Admin ANETI
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" 
                           href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'empresas.php' ? 'active' : ''; ?>" 
                           href="empresas.php">
                            <i class="fas fa-store me-1"></i>Empresas
                            <?php if (isset($stats['empresas_pendentes']) && $stats['empresas_pendentes'] > 0): ?>
                                <span class="badge bg-warning text-dark"><?php echo $stats['empresas_pendentes']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'cupons.php' ? 'active' : ''; ?>" 
                           href="cupons.php">
                            <i class="fas fa-ticket-alt me-1"></i>Cupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'categorias.php' ? 'active' : ''; ?>" 
                           href="categorias.php">
                            <i class="fas fa-tags me-1"></i>Categorias  
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'membros.php' ? 'active' : ''; ?>" 
                           href="membros.php">
                            <i class="fas fa-users me-1"></i>Membros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'slides-banner.php' ? 'active' : ''; ?>" 
                           href="slides-banner.php">
                            <i class="fas fa-images me-1"></i>Slides do Banner
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'usuarios-admin.php' ? 'active' : ''; ?>" 
                           href="usuarios-admin.php">
                            <i class="fas fa-users-cog me-1"></i>Usuários Admin
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle admin-user-menu" href="#" id="adminUserDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-shield me-2"></i>
                            Administrador ANETI
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="../index.php" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Ver Site
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>