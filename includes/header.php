<header class="main-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/empresa/') !== false ? '../index.php' : 'index.php'; ?>">
                <svg width="120" height="40" viewBox="0 0 120 40" class="logo">
                    <rect x="10" y="10" width="100" height="20" rx="5" fill="#012d6a"/>
                    <text x="60" y="25" text-anchor="middle" font-size="14" fill="white" font-weight="bold">ANETI</text>
                </svg>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/empresa/') !== false ? '../index.php' : 'index.php'; ?>">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php' : 'public/buscar.php'; ?>">
                            <i class="fas fa-search"></i> Buscar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo strpos($_SERVER['PHP_SELF'], '/empresa/') !== false ? 'cadastro.php' : 'empresa/cadastro.php'; ?>">
                            <i class="fas fa-store"></i> Seja Parceiro
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_nome']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'dashboard.php' : 'public/dashboard.php'; ?>">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'logout.php' : 'public/logout.php'; ?>">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'login.php' : 'public/login.php'; ?>">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
