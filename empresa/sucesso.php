<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Enviado - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        
                        <h2 class="text-success mb-4">Cadastro Enviado com Sucesso!</h2>
                        
                        <p class="lead mb-4">
                            Obrigado por se interessar em ser nosso parceiro!
                        </p>
                        
                        <div class="alert alert-info text-start">
                            <h6><i class="fas fa-info-circle"></i> Próximos Passos:</h6>
                            <ul class="mb-0">
                                <li>Seu cadastro foi enviado para análise da equipe ANETI</li>
                                <li>O processo de aprovação pode levar até 48 horas</li>
                                <li>Você receberá um e-mail com o resultado da análise</li>
                                <li>Após aprovação, sua empresa aparecerá no site</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="../index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home"></i> Página Inicial
                            </a>
                            <a href="cadastro.php" class="btn btn-outline-secondary">
                                <i class="fas fa-plus"></i> Novo Cadastro
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-muted">
                            <p class="mb-1"><i class="fas fa-envelope"></i> Dúvidas? Entre em contato:</p>
                            <p class="mb-0">
                                <a href="mailto:parceiros@aneti.net.br">parceiros@aneti.net.br</a> | 
                                <a href="tel:1112345678">(11) 1234-5678</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
