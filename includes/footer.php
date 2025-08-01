<!-- Footer -->
<footer style="background: linear-gradient(135deg, #012d6a 0%, #25a244 100%); color: white; padding: 40px 0 20px 0; margin-top: 40px;">
    <div class="container">
        <div class="row">
            <!-- Coluna 1: Clube de Vantagens ANETI -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-brand">
                    <h5 style="color: white; font-weight: 700; margin-bottom: 20px;">
                        <i class="fas fa-star me-2"></i>
                        Clube de Vantagens ANETI
                    </h5>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6; margin-bottom: 15px; font-size: 0.9rem;">
                        Benefícios exclusivos para membros da Associação Nacional dos Especialistas em Tecnologia da Informação.
                    </p>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                        <p style="margin-bottom: 8px;">
                            <i class="fas fa-users me-2"></i>Mais de 1.800 membros ativos
                        </p>
                        <p style="margin-bottom: 0;">
                            <i class="fas fa-handshake me-2"></i>50+ empresas parceiras
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Coluna 2: Navegação -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 style="color: white; font-weight: 600; margin-bottom: 20px;">Navegação</h6>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="<?= $base_path ?? '../' ?>index.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s; font-size: 0.9rem;">
                            <i class="fas fa-home me-2"></i>Início
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="<?= $base_path ?? '../' ?>public/categorias.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s; font-size: 0.9rem;">
                            <i class="fas fa-search me-2"></i>Buscar
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="<?= $base_path ?? '../' ?>public/login.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s; font-size: 0.9rem;">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="<?= $base_path ?? '../' ?>empresa/cadastro.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s; font-size: 0.9rem;">
                            <i class="fas fa-plus me-2"></i>Seja Parceiro
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s; font-size: 0.9rem;">
                            <i class="fas fa-cog me-2"></i>Área Admin
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Coluna 3: Categorias -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 style="color: white; font-weight: 600; margin-bottom: 20px;">Categorias</h6>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;">
                        <a href="<?= $base_path ?? '../' ?>public/categorias.php?cat=comer-beber" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; transition: color 0.3s;">
                            <i class="fas fa-utensils me-2"></i>Comer e Beber
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="<?= $base_path ?? '../' ?>public/categorias.php?cat=compras" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; transition: color 0.3s;">
                            <i class="fas fa-shopping-bag me-2"></i>Compras
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="<?= $base_path ?? '../' ?>public/categorias.php?cat=saude-bem-estar" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; transition: color 0.3s;">
                            <i class="fas fa-heartbeat me-2"></i>Saúde e Bem-estar
                        </a>
                    </li>
                    <li style="margin-bottom: 8px;">
                        <a href="<?= $base_path ?? '../' ?>public/categorias.php?cat=viagem-turismo" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; transition: color 0.3s;">
                            <i class="fas fa-plane me-2"></i>Viagem e Turismo
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Coluna 4: Contato -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 style="color: white; font-weight: 600; margin-bottom: 20px;">Contato</h6>
                <div style="color: rgba(255,255,255,0.8); line-height: 1.8;">
                    <p style="margin-bottom: 12px; font-size: 0.9rem;">
                        <i class="fas fa-envelope me-2"></i>
                        contato@aneti.org.br
                    </p>
                    <p style="margin-bottom: 12px; font-size: 0.9rem;">
                        <i class="fas fa-phone me-2"></i>
                        (61) 93618-0637
                    </p>
                    <p style="margin-bottom: 20px; font-size: 0.85rem;">
                        <strong>Siga-nos:</strong>
                    </p>
                    <div class="social-links">
                        <a href="#" style="color: rgba(255,255,255,0.8); margin-right: 12px; font-size: 1.1rem; transition: color 0.3s;">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" style="color: rgba(255,255,255,0.8); margin-right: 12px; font-size: 1.1rem; transition: color 0.3s;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" style="color: rgba(255,255,255,0.8); margin-right: 12px; font-size: 1.1rem; transition: color 0.3s;">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" style="color: rgba(255,255,255,0.8); font-size: 1.1rem; transition: color 0.3s;">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Coluna 5: Logo ANETI -->
            <div class="col-lg-3 col-md-12 mb-4 text-center">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    <div style="margin-bottom: 20px;">
                        <img src="<?= isset($base_path) ? $base_path : '../' ?>attached_assets/logo-branca_1754052485966.png" 
                             alt="ANETI Logo" 
                             style="height: 80px; width: auto; margin-bottom: 10px;">
                        <div style="color: rgba(255,255,255,0.8); font-size: 0.8rem; line-height: 1.4;">
                            Um produto ANETI<br>
                            Associação Nacional dos Especialistas em TI
                        </div>
                    </div>
                    <div style="color: rgba(255,255,255,0.6); font-size: 0.75rem; text-align: center;">
                        © <?php echo date('Y'); ?> ANETI. Todos os direitos reservados.<br>
                        Desenvolvido com 💙 para membros ANETI
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-brand .social-links a:hover {
    color: white !important;
}

footer ul li a:hover {
    color: white !important;
    padding-left: 5px;
}

@media (max-width: 768px) {
    footer .col-md-6.text-md-end {
        text-align: left !important;
        margin-top: 10px;
    }
}
</style>