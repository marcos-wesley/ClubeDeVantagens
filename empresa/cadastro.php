<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_POST) {
    $nome = sanitizeInput($_POST['nome']);
    $cnpj = sanitizeInput($_POST['cnpj']);
    $endereco = sanitizeInput($_POST['endereco']);
    $cidade = sanitizeInput($_POST['cidade']);
    $estado = sanitizeInput($_POST['estado']);
    $email = sanitizeInput($_POST['email']);
    $telefone = sanitizeInput($_POST['telefone']);
    $website = sanitizeInput($_POST['website']);
    $categoria = sanitizeInput($_POST['categoria']);
    $descricao = sanitizeInput($_POST['descricao']);
    $regras = sanitizeInput($_POST['regras']);
    
    // Validation
    if (empty($nome) || empty($cnpj) || empty($cidade) || empty($estado) || empty($email) || empty($telefone) || empty($categoria) || empty($descricao) || empty($regras)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!validateEmail($email)) {
        $error = 'E-mail inválido.';
    } else {
        // Handle file uploads
        $logo_filename = null;
        $imagem_detalhes_filename = null;
        
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['logo']['type'], $allowed_types) && $_FILES['logo']['size'] <= 5 * 1024 * 1024) {
                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $logo_filename = uniqid() . '.' . $extension;
                $upload_path = $upload_dir . $logo_filename;
                
                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $error = 'Erro ao fazer upload da logo.';
                }
            } else {
                $error = 'Arquivo de logo inválido. Use JPG, PNG, WebP ou GIF até 5MB.';
            }
        }
        
        // Handle detail image upload
        if (!$error && isset($_FILES['imagem_detalhes']) && $_FILES['imagem_detalhes']['size'] > 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['imagem_detalhes']['type'], $allowed_types) && $_FILES['imagem_detalhes']['size'] <= 5 * 1024 * 1024) {
                $extension = pathinfo($_FILES['imagem_detalhes']['name'], PATHINFO_EXTENSION);
                $imagem_detalhes_filename = uniqid() . '.' . $extension;
                $upload_path = $upload_dir . $imagem_detalhes_filename;
                
                if (!move_uploaded_file($_FILES['imagem_detalhes']['tmp_name'], $upload_path)) {
                    $error = 'Erro ao fazer upload da imagem de detalhes.';
                }
            } else {
                $error = 'Arquivo de imagem de detalhes inválido. Use JPG, PNG, WebP ou GIF até 5MB.';
            }
        }
        
        if (!$error) {
            try {
                $stmt = $conn->prepare("INSERT INTO empresas (nome, cnpj, logo, imagem_detalhes, endereco, cidade, estado, email, telefone, website, categoria, descricao, regras, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente', NOW())");
                $stmt->execute([$nome, $cnpj, $logo_filename, $imagem_detalhes_filename, $endereco, $cidade, $estado, $email, $telefone, $website, $categoria, $descricao, $regras]);
                
                redirect('sucesso.php');
            } catch (PDOException $e) {
                $error = 'Erro ao cadastrar empresa. Tente novamente.';
            }
        }
    }
}

$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empresa - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 150px;
        }
        .partner-hero {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 8px 25px rgba(1, 45, 106, 0.15);
        }
        .partner-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            margin-bottom: 30px;
        }
        .partner-card-header {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: #012d6a;
            box-shadow: 0 0 0 0.2rem rgba(1, 45, 106, 0.25);
        }
        .btn-aneti {
            background: linear-gradient(135deg, #012d6a 0%, #25a244 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-aneti:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(1, 45, 106, 0.3);
            color: white;
        }
        .btn-outline-aneti {
            border: 2px solid #012d6a;
            color: #012d6a;
            background: transparent;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-aneti:hover {
            background: #012d6a;
            color: white;
            transform: translateY(-2px);
        }
        .info-alert {
            background: linear-gradient(135deg, rgba(1, 45, 106, 0.1) 0%, rgba(37, 162, 68, 0.1) 100%);
            border-left: 4px solid #012d6a;
            border-radius: 10px;
            color: #012d6a;
        }
        .form-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            border-left: 4px solid #25a244;
        }
        .section-title {
            color: #012d6a;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 0.5rem;
            color: #25a244;
        }
        .upload-area {
            border: 2px dashed #012d6a;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(1, 45, 106, 0.02);
        }
        .upload-area:hover {
            background: rgba(1, 45, 106, 0.05);
            border-color: #25a244;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: white;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            color: #333;
        }
        .benefit-item i {
            color: #25a244;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        .benefit-item span {
            color: #333;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            body {
                padding-top: 130px;
            }
            .partner-hero {
                padding: 2rem 0;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <div class="partner-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-handshake me-3"></i>Seja um Parceiro ANETI
                    </h1>
                    <p class="lead mb-4">
                        Faça parte da maior rede de benefícios para engenheiros de TI do Brasil. 
                        Alcance mais de 1.800 profissionais qualificados e aumente suas vendas.
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="benefit-item">
                                <i class="fas fa-users"></i>
                                <span>Mais de 1.800 membros ativos</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Aumento comprovado em vendas</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item">
                                <i class="fas fa-star"></i>
                                <span>Exposição prioritária</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="benefit-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Credibilidade ANETI</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle p-4 d-inline-block" style="box-shadow: 0 10px 30px rgba(255,255,255,0.2);">
                        <i class="fas fa-store text-primary" style="font-size: 4rem; color: #012d6a !important;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="partner-card">
                    <div class="partner-card-header">
                        <h3 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Formulário de Cadastro</h3>
                        <p class="mb-0 mt-2">Preencha os dados da sua empresa para começar</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; border-radius: 10px;">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="info-alert alert">
                            <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Processo de Aprovação</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small>
                                        <i class="fas fa-check-circle text-success me-2"></i>Análise pela equipe ANETI<br>
                                        <i class="fas fa-clock text-warning me-2"></i>Resposta em até 48 horas
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small>
                                        <i class="fas fa-envelope text-info me-2"></i>Notificação por e-mail<br>
                                        <i class="fas fa-star text-primary me-2"></i>Publicação após aprovação
                                    </small>
                                </div>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <!-- Seção 1: Dados da Empresa -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-building"></i>Dados da Empresa
                                </h5>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="nome" class="form-label fw-semibold">Nome da Empresa *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required 
                                               placeholder="Digite o nome completo da empresa"
                                               value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cnpj" class="form-label fw-semibold">CNPJ *</label>
                                        <input type="text" class="form-control" id="cnpj" name="cnpj" required 
                                               placeholder="00.000.000/0000-00"
                                               value="<?php echo isset($_POST['cnpj']) ? htmlspecialchars($_POST['cnpj']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">E-mail Corporativo *</label>
                                        <input type="email" class="form-control" id="email" name="email" required 
                                               placeholder="contato@suaempresa.com.br"
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefone" class="form-label fw-semibold">Telefone *</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone" required 
                                               placeholder="(11) 99999-9999"
                                               value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="website" class="form-label fw-semibold">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           placeholder="https://www.suaempresa.com.br"
                                           value="<?php echo isset($_POST['website']) ? htmlspecialchars($_POST['website']) : ''; ?>">
                                </div>
                            </div>

                            <!-- Seção 2: Imagens -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-images"></i>Identidade Visual
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="logo" class="form-label fw-semibold">Logo da Empresa</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: #012d6a;"></i>
                                            <p class="mb-2">Clique para selecionar a logo</p>
                                            <input type="file" class="form-control" id="logo" name="logo" 
                                                   accept="image/jpeg,image/png,image/gif,image/webp">
                                            <small class="text-muted">JPG, PNG, WebP, GIF - Máx: 5MB</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="imagem_detalhes" class="form-label fw-semibold">Imagem de Capa</label>
                                        <div class="upload-area">
                                            <i class="fas fa-image fa-2x mb-2" style="color: #012d6a;"></i>
                                            <p class="mb-2">Imagem para página de detalhes</p>
                                            <input type="file" class="form-control" id="imagem_detalhes" name="imagem_detalhes" 
                                                   accept="image/jpeg,image/png,image/gif,image/webp">
                                            <small class="text-muted">JPG, PNG, WebP, GIF - Máx: 5MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 3: Localização -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-map-marker-alt"></i>Localização
                                </h5>
                                <div class="mb-3">
                                    <label for="endereco" class="form-label fw-semibold">Endereço Completo</label>
                                    <input type="text" class="form-control" id="endereco" name="endereco" 
                                           placeholder="Rua, número, bairro, CEP"
                                           value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="cidade" class="form-label fw-semibold">Cidade *</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" required 
                                               placeholder="Digite a cidade"
                                               value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="estado" class="form-label fw-semibold">Estado *</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="">Selecione o estado...</option>
                                        <option value="AC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AC') ? 'selected' : ''; ?>>Acre</option>
                                        <option value="AL" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AL') ? 'selected' : ''; ?>>Alagoas</option>
                                        <option value="AP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AP') ? 'selected' : ''; ?>>Amapá</option>
                                        <option value="AM" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AM') ? 'selected' : ''; ?>>Amazonas</option>
                                        <option value="BA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'BA') ? 'selected' : ''; ?>>Bahia</option>
                                        <option value="CE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'CE') ? 'selected' : ''; ?>>Ceará</option>
                                        <option value="DF" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'DF') ? 'selected' : ''; ?>>Distrito Federal</option>
                                        <option value="ES" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'ES') ? 'selected' : ''; ?>>Espírito Santo</option>
                                        <option value="GO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'GO') ? 'selected' : ''; ?>>Goiás</option>
                                        <option value="MA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MA') ? 'selected' : ''; ?>>Maranhão</option>
                                        <option value="MT" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MT') ? 'selected' : ''; ?>>Mato Grosso</option>
                                        <option value="MS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MS') ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MG') ? 'selected' : ''; ?>>Minas Gerais</option>
                                        <option value="PA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PA') ? 'selected' : ''; ?>>Pará</option>
                                        <option value="PB" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PB') ? 'selected' : ''; ?>>Paraíba</option>
                                        <option value="PR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PR') ? 'selected' : ''; ?>>Paraná</option>
                                        <option value="PE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PE') ? 'selected' : ''; ?>>Pernambuco</option>
                                        <option value="PI" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PI') ? 'selected' : ''; ?>>Piauí</option>
                                        <option value="RJ" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RJ') ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                        <option value="RN" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RN') ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RS') ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RO') ? 'selected' : ''; ?>>Rondônia</option>
                                        <option value="RR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RR') ? 'selected' : ''; ?>>Roraima</option>
                                        <option value="SC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SC') ? 'selected' : ''; ?>>Santa Catarina</option>
                                        <option value="SP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SP') ? 'selected' : ''; ?>>São Paulo</option>
                                        <option value="SE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SE') ? 'selected' : ''; ?>>Sergipe</option>
                                        <option value="TO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'TO') ? 'selected' : ''; ?>>Tocantins</option>
                                    </select>
                                </div>
                            </div>
                            </div>

                            <!-- Seção 4: Categoria e Benefício -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-tags"></i>Categoria e Benefício
                                </h5>
                                <div class="mb-3">
                                    <label for="categoria" class="form-label fw-semibold">Categoria do Negócio *</label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Escolha a categoria que melhor define seu negócio...</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['nome']); ?>" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == $category['nome']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="descricao" class="form-label fw-semibold">Descrição do Benefício *</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="4" required 
                                              placeholder="Descreva de forma detalhada o benefício que você oferece aos membros ANETI. Seja específico sobre descontos, vantagens e diferenciais..."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                                    <small class="text-muted">Esta descrição aparecerá no cartão da sua empresa no site</small>
                                </div>
                                <div class="mb-3">
                                    <label for="regras" class="form-label fw-semibold">Regras e Condições *</label>
                                    <textarea class="form-control" id="regras" name="regras" rows="4" required 
                                              placeholder="Ex: Desconto de 15% em todos os produtos. Válido apenas para membros ANETI mediante apresentação de cupom. Não cumulativo com outras promoções. Válido até 31/12/2025..."><?php echo isset($_POST['regras']) ? htmlspecialchars($_POST['regras']) : ''; ?></textarea>
                                    <small class="text-muted">Detalhe todas as condições para uso do benefício</small>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="text-center pt-4 border-top">
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <p class="text-muted mb-4">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            Ao enviar este formulário, você concorda com os termos de parceria da ANETI
                                        </p>
                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                            <a href="../index.php" class="btn btn-outline-aneti">
                                                <i class="fas fa-arrow-left me-2"></i>Voltar ao Início
                                            </a>
                                            <button type="submit" class="btn btn-aneti btn-lg">
                                                <i class="fas fa-paper-plane me-2"></i>Enviar Solicitação
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CNPJ mask
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Phone mask
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            e.target.value = value;
        });
    </script>
</body>
</html>
