<?php
// Determine the correct path based on current directory
$is_subdirectory = strpos($_SERVER['PHP_SELF'], '/public/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                  strpos($_SERVER['PHP_SELF'], '/empresa/') !== false;
$base_path = $is_subdirectory ? '../' : '';
?>

<footer class="main-footer text-white py-5" style="background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); margin-top: auto;">
    <div class="container">
        <!-- Seção Principal do Footer -->
        <div class="row g-4">
            <!-- Coluna 1: Informações do Clube -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-section">
                    <h5 class="mb-3" style="color: white; font-weight: 600;">
                        <i class="fas fa-star me-2"></i>Clube de Vantagens ANETI
                    </h5>
                    <p class="mb-3" style="color: rgba(255,255,255,0.9); line-height: 1.6;">
                        Benefícios exclusivos para membros da Associação Nacional dos Especialistas em Tecnologia da Informação.
                    </p>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users me-2" style="color: #25a244;"></i>
                        <span style="color: rgba(255,255,255,0.9);">Mais de 1.800 membros ativos</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-store me-2" style="color: #25a244;"></i>
                        <span style="color: rgba(255,255,255,0.9);">50+ empresas parceiras</span>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Links Úteis -->
            <div class="col-lg-2 col-md-6 col-6">
                <div class="footer-section">
                    <h6 class="mb-3" style="color: white; font-weight: 600;">Navegação</h6>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2">
                            <a href="<?= $base_path ?>index.php" class="footer-link">
                                <i class="fas fa-home me-2"></i>Início
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/categorias.php" class="footer-link">
                                <i class="fas fa-search me-2"></i>Buscar
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/login.php" class="footer-link">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>empresa/cadastro.php" class="footer-link">
                                <i class="fas fa-handshake me-2"></i>Seja Parceiro
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>admin/login.php" class="footer-link admin-link">
                                <i class="fas fa-cog me-2"></i>Área Admin
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Coluna 3: Categorias Populares -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="footer-section">
                    <h6 class="mb-3" style="color: white; font-weight: 600;">Categorias</h6>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/categorias.php?cat=comer-beber" class="footer-link">
                                <i class="fas fa-utensils me-2"></i>Comer e Beber
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/categorias.php?cat=compras" class="footer-link">
                                <i class="fas fa-shopping-bag me-2"></i>Compras
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/categorias.php?cat=saude-bem-estar" class="footer-link">
                                <i class="fas fa-heart me-2"></i>Saúde e Bem-estar
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= $base_path ?>public/categorias.php?cat=viagem-turismo" class="footer-link">
                                <i class="fas fa-plane me-2"></i>Viagem e Turismo
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Coluna 4: Contato e Redes Sociais -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-section">
                    <h6 class="mb-3" style="color: white; font-weight: 600;">Contato</h6>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope me-2" style="color: #25a244;"></i>
                            <a href="mailto:contato@aneti.org.br" class="footer-link">contato@aneti.org.br</a>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-phone me-2" style="color: #25a244;"></i>
                            <span style="color: rgba(255,255,255,0.9);">(61) 93618-0637</span>
                        </div>
                    </div>
                    <div class="social-links">
                        <h6 class="mb-2" style="color: white; font-size: 14px;">Siga-nos:</h6>
                        <a href="https://www.facebook.com/profile.php?id=100092650971851&mibextid=LQQJ4d" target="_blank" class="social-link me-3" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://instagram.com/anetioficial?igshid=MzRlODBiNWFlZA" target="_blank" class="social-link me-3" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/anetioficial/" target="_blank" class="social-link me-3" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.youtube.com/@anetioficial" target="_blank" class="social-link" title="Youtube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divisor -->
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">

        <!-- Seção Bottom: Logo ANETI e Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <img src="<?= $base_path ?>assets/images/logo-aneti-branca.png" alt="ANETI" 
                         style="height: 50px; width: auto; margin-right: 15px;">
                    <div>
                        <div style="color: white; font-weight: 600; font-size: 16px;">um produto ANETI</div>
                        <div style="color: rgba(255,255,255,0.8); font-size: 12px;">
                            Associação Nacional dos Especialistas em TI
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-md-end text-center mt-3 mt-md-0">
                    <p class="mb-0" style="color: rgba(255,255,255,0.8); font-size: 14px;">
                        &copy; <?php echo date('Y'); ?> ANETI. Todos os direitos reservados.
                    </p>
                    <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 12px;">
                        Desenvolvido com <i class="fas fa-heart" style="color: #25a244;"></i> para membros ANETI
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-link {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.footer-link:hover {
    color: #25a244;
    text-decoration: none;
    transform: translateX(5px);
}

.admin-link {
    position: relative;
    padding: 3px 8px;
    border-radius: 4px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(5px);
    margin-left: -5px;
}

.admin-link:hover {
    background: rgba(37, 162, 68, 0.2);
    color: #25a244;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    color: white;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.social-link:hover {
    background: #25a244;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(37, 162, 68, 0.4);
}

.footer-section {
    height: 100%;
}

@media (max-width: 768px) {
    .main-footer {
        padding: 3rem 0 !important;
    }

    .footer-section h5,
    .footer-section h6 {
        font-size: 16px;
    }

    .social-link {
        width: 35px;
        height: 35px;
    }
}
</style>
