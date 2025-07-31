<footer class="main-footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>Clube de Vantagens ANETI</h5>
                <p>Benefícios exclusivos para membros da Associação Nacional de Engenheiros de Tecnologia da Informação.</p>
            </div>
            <div class="col-lg-2 mb-4">
                <h6>Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/empresa/') !== false ? '../index.php' : 'index.php'; ?>" class="text-light">Início</a></li>
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php' : 'public/buscar.php'; ?>" class="text-light">Buscar</a></li>
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/empresa/') !== false ? 'cadastro.php' : 'empresa/cadastro.php'; ?>" class="text-light">Seja Parceiro</a></li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6>Categorias</h6>
                <ul class="list-unstyled">
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php?categoria=Alimentação' : 'public/buscar.php?categoria=Alimentação'; ?>" class="text-light">Alimentação</a></li>
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php?categoria=Tecnologia' : 'public/buscar.php?categoria=Tecnologia'; ?>" class="text-light">Tecnologia</a></li>
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php?categoria=Educação' : 'public/buscar.php?categoria=Educação'; ?>" class="text-light">Educação</a></li>
                    <li><a href="<?php echo strpos($_SERVER['PHP_SELF'], '/public/') !== false ? 'buscar.php?categoria=Saúde' : 'public/buscar.php?categoria=Saúde'; ?>" class="text-light">Saúde</a></li>
                </ul>
            </div>
            <div class="col-lg-3 mb-4">
                <h6>Contato</h6>
                <p><i class="fas fa-envelope"></i> contato@aneti.net.br</p>
                <p><i class="fas fa-phone"></i> (11) 1234-5678</p>
                <div class="social-links">
                    <a href="#" class="text-light me-2"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light me-2"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-12 text-center">
                <p>&copy; <?php echo date('Y'); ?> ANETI - Associação Nacional de Engenheiros de Tecnologia da Informação. Todos os direitos reservados.</p>
            </div>
        </div>
    </div>
</footer>
