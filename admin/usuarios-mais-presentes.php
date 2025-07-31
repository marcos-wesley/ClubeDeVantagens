<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

// Dados simulados baseados nas especificações
$usuarios_presentes = [
    ['nome' => 'Ana', 'avatar' => 'https://i.pravatar.cc/45?img=1', 'sessoes' => 88],
    ['nome' => 'Bianca', 'avatar' => 'https://i.pravatar.cc/45?img=5', 'sessoes' => 85],
    ['nome' => 'Jonathan', 'avatar' => 'https://i.pravatar.cc/45?img=3', 'sessoes' => 74],
    ['nome' => 'Samuel', 'avatar' => 'https://i.pravatar.cc/45?img=4', 'sessoes' => 69]
];

$usuarios_ativos = [
    ['nome' => 'Ana', 'avatar' => 'https://i.pravatar.cc/45?img=1', 'resgates' => 18],
    ['nome' => 'Samuel', 'avatar' => 'https://i.pravatar.cc/45?img=4', 'resgates' => 16],
    ['nome' => 'Isadora', 'avatar' => 'https://i.pravatar.cc/45?img=6', 'resgates' => 15],
    ['nome' => 'Bianca', 'avatar' => 'https://i.pravatar.cc/45?img=5', 'resgates' => 13]
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Admin ANETI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cog"></i> Admin ANETI
            </a>
            
            <div class="navbar-nav">
                <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" href="empresas.php"><i class="fas fa-store"></i> Empresas</a>
                <a class="nav-link" href="cupons.php"><i class="fas fa-ticket-alt"></i> Cupons</a>
                <a class="nav-link" href="categorias.php"><i class="fas fa-tags"></i> Categorias</a>
                <a class="nav-link" href="membros.php"><i class="fas fa-users"></i> Membros</a>
                <a class="nav-link active" href="usuarios-mais-presentes.php"><i class="fas fa-user-friends"></i> Rankings</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
    </nav>

    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1 class="admin-greeting">Rankings de Usuários</h1>
                    <p class="admin-subtitle">Rankings dos usuários mais presentes e ativos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Usuários Mais Presentes -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <h5 class="widget-title">Usuários mais presentes</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        
                        <ul class="ranking-list">
                            <?php foreach ($usuarios_presentes as $index => $usuario): ?>
                                <li class="ranking-item">
                                    <div class="ranking-number"><?php echo $index + 1; ?></div>
                                    <img src="<?php echo $usuario['avatar']; ?>" alt="Avatar" class="user-avatar-large">
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo $usuario['nome']; ?></p>
                                        <p class="metric-detail">(<?php echo $usuario['sessoes']; ?> sessões)</p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Usuários Mais Ativos -->
            <div class="col-lg-6 mb-4">
                <div class="card widget-card">
                    <div class="widget-header">
                        <div class="widget-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h5 class="widget-title">Usuários mais ativos</h5>
                    </div>
                    <div class="widget-body">
                        <div class="period-tabs">
                            <button class="period-tab active">Último mês</button>
                            <button class="period-tab">Últimos 7 dias</button>
                            <button class="period-tab">Últimas 24 horas</button>
                        </div>
                        
                        <ul class="ranking-list">
                            <?php foreach ($usuarios_ativos as $index => $usuario): ?>
                                <li class="ranking-item">
                                    <div class="ranking-number"><?php echo $index + 1; ?></div>
                                    <img src="<?php echo $usuario['avatar']; ?>" alt="Avatar" class="user-avatar-large">
                                    <div class="metric-info">
                                        <p class="metric-name"><?php echo $usuario['nome']; ?></p>
                                        <p class="metric-detail">(<?php echo $usuario['resgates']; ?> resgates)</p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidade dos filtros de período
        document.querySelectorAll('.period-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active de todos os tabs do mesmo container
                const container = this.closest('.widget-body');
                container.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
                
                // Adiciona active ao tab clicado
                this.classList.add('active');
                
                // Aqui você pode adicionar lógica para filtrar os dados
                console.log('Filtro selecionado:', this.textContent);
            });
        });
    </script>
</body>
</html>