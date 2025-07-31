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
        // Handle logo upload
        $logo_filename = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
            $upload_result = uploadFile($_FILES['logo']);
            if ($upload_result['success']) {
                $logo_filename = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (!$error) {
            try {
                $stmt = $conn->prepare("INSERT INTO empresas (nome, cnpj, logo, endereco, cidade, estado, email, telefone, website, categoria, descricao, regras, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente', NOW())");
                $stmt->execute([$nome, $cnpj, $logo_filename, $endereco, $cidade, $estado, $email, $telefone, $website, $categoria, $descricao, $regras]);
                
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
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-store"></i> Cadastro de Empresa Parceira</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Informações Importantes</h6>
                            <ul class="mb-0">
                                <li>Seu cadastro será analisado pela equipe ANETI</li>
                                <li>O processo de aprovação pode levar até 48 horas</li>
                                <li>Você receberá um e-mail com o resultado da análise</li>
                                <li>Apenas empresas aprovadas aparecerão no site</li>
                            </ul>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="nome" class="form-label">Nome da Empresa *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cnpj" class="form-label">CNPJ *</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" required placeholder="00.000.000/0000-00" value="<?php echo isset($_POST['cnpj']) ? htmlspecialchars($_POST['cnpj']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo da Empresa</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 5MB</div>
                            </div>

                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço Completo</label>
                                <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Rua, número, bairro" value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="cidade" class="form-label">Cidade *</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade" required value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="">Selecione...</option>
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

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label">Telefone *</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" required placeholder="(11) 99999-9999" value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" placeholder="https://www.suaempresa.com.br" value="<?php echo isset($_POST['website']) ? htmlspecialchars($_POST['website']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoria *</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Selecione uma categoria...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['nome']); ?>" <?php echo (isset($_POST['categoria']) && $_POST['categoria'] == $category['nome']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição do Benefício *</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="4" required placeholder="Descreva o benefício oferecido aos membros da ANETI..."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="regras" class="form-label">Regras do Cupom *</label>
                                <textarea class="form-control" id="regras" name="regras" rows="4" required placeholder="Ex: Desconto de 10%. Válido apenas para membros ANETI. Não cumulativo com outras promoções..."><?php echo isset($_POST['regras']) ? htmlspecialchars($_POST['regras']) : ''; ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="../index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Enviar Cadastro
                                </button>
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
