-- ANETI Clube de Vantagens Database Schema
-- MySQL 8.0+ compatible

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist (for clean installation)
DROP TABLE IF EXISTS cupons;
DROP TABLE IF EXISTS empresas;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS categorias;

SET FOREIGN_KEY_CHECKS = 1;

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS aneti_clube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE aneti_clube;

-- Categorias table
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios table (ANETI members)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    plano ENUM('junior', 'pleno', 'senior') NOT NULL DEFAULT 'junior',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_plano (plano),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Empresas table (Partner companies)
CREATE TABLE empresas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    logo VARCHAR(255),
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    regras TEXT NOT NULL,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    destaque BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_cnpj (cnpj),
    INDEX idx_categoria (categoria),
    INDEX idx_cidade (cidade),
    INDEX idx_estado (estado),
    INDEX idx_status (status),
    INDEX idx_destaque (destaque),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (categoria) REFERENCES categorias(nome) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cupons table (Generated discount coupons)
CREATE TABLE cupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    empresa_id INT NOT NULL,
    codigo VARCHAR(36) NOT NULL UNIQUE,
    usado BOOLEAN DEFAULT FALSE,
    data_uso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_codigo (codigo),
    INDEX idx_usado (usado),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO categorias (nome, descricao) VALUES
('Alimentação', 'Restaurantes, lanchonetes, delivery e estabelecimentos do ramo alimentício'),
('Tecnologia', 'Lojas de eletrônicos, equipamentos de informática e serviços tecnológicos'),
('Educação', 'Cursos, treinamentos, universidades e instituições de ensino'),
('Saúde', 'Clínicas, laboratórios, farmácias e serviços de saúde'),
('Beleza', 'Salões de beleza, estética, cosméticos e cuidados pessoais'),
('Viagem', 'Agências de viagem, hotéis, pousadas e turismo'),
('Esporte', 'Academias, lojas de artigos esportivos e atividades físicas'),
('Entretenimento', 'Cinemas, teatros, eventos e atividades de lazer'),
('Compras', 'Lojas de roupas, calçados, acessórios e varejo em geral'),
('Serviços', 'Prestadores de serviços diversos, manutenção e consultoria'),
('Automotivo', 'Concessionárias, oficinas, postos de combustível e serviços automotivos'),
('Casa e Decoração', 'Móveis, decoração, materiais de construção e utilidades domésticas');

-- Insert demo admin user
-- Password: admin123 (MD5 hash)
INSERT INTO admins (nome, email, password) VALUES
('Administrador ANETI', 'admin@aneti.net.br', '0192023a7bbd73250516f069df18b500');

-- Insert demo users (ANETI members)
INSERT INTO usuarios (nome, email, plano) VALUES
('João Silva', 'joao.silva@email.com', 'junior'),
('Maria Santos', 'maria.santos@email.com', 'pleno'),
('Carlos Oliveira', 'carlos.oliveira@email.com', 'senior'),
('Ana Costa', 'ana.costa@email.com', 'pleno'),
('Pedro Fernandes', 'pedro.fernandes@email.com', 'junior'),
('Juliana Rodrigues', 'juliana.rodrigues@email.com', 'senior');

-- Insert demo companies
INSERT INTO empresas (nome, cnpj, cidade, estado, email, telefone, categoria, descricao, regras, status, destaque) VALUES
('TechStore Informática', '12.345.678/0001-90', 'São Paulo', 'SP', 'contato@techstore.com', '(11) 99999-1111', 'Tecnologia', 'Loja especializada em equipamentos de informática e eletrônicos com 15% de desconto para membros ANETI.', 'Desconto de 15% em produtos selecionados. Não cumulativo com outras promoções. Válido apenas para membros ativos da ANETI.', 'aprovada', 1),
('Restaurante Sabor & Arte', '23.456.789/0001-01', 'Rio de Janeiro', 'RJ', 'contato@saborarte.com', '(21) 98888-2222', 'Alimentação', 'Restaurante contemporâneo oferecendo 20% de desconto no almoço executivo para engenheiros da ANETI.', 'Desconto de 20% no almoço executivo de segunda a sexta. Não válido em feriados e datas comemorativas. Apresentar carteirinha da ANETI.', 'aprovada', 1),
('Academia FitLife', '34.567.890/0001-12', 'Belo Horizonte', 'MG', 'contato@fitlife.com', '(31) 97777-3333', 'Esporte', 'Academia completa com equipamentos modernos e 25% de desconto na mensalidade para membros ANETI.', 'Desconto de 25% na primeira mensalidade. Matrícula grátis. Válido apenas para novos alunos membros da ANETI.', 'aprovada', 1),
('Clínica OdontoSaúde', '45.678.901/0001-23', 'Porto Alegre', 'RS', 'contato@odontosaude.com', '(51) 96666-4444', 'Saúde', 'Clínica odontológica moderna oferecendo 30% de desconto em tratamentos para membros ANETI e familiares.', 'Desconto de 30% em consultas e tratamentos. Válido para titular e dependentes. Agendamento obrigatório com antecedência mínima de 24h.', 'aprovada', 0),
('Livraria Conhecimento', '56.789.012/0001-34', 'Brasília', 'DF', 'contato@conhecimento.com', '(61) 95555-5555', 'Educação', 'Livraria especializada em livros técnicos e cursos com 10% de desconto para membros ANETI.', 'Desconto de 10% em livros técnicos e cursos online. Não válido para livros em promoção. Compras acima de R$ 100,00.', 'aprovada', 0),
('Hotel Vista Mar', '67.890.123/0001-45', 'Florianópolis', 'SC', 'contato@vistmar.com', '(48) 94444-6666', 'Viagem', 'Hotel 4 estrelas com vista para o mar oferecendo 15% de desconto em hospedagem para membros ANETI.', 'Desconto de 15% nas diárias. Válido para reservas com antecedência mínima de 7 dias. Não válido em alta temporada e feriados prolongados.', 'aprovada', 1),
('Salão Elegance', '78.901.234/0001-56', 'Salvador', 'BA', 'contato@elegance.com', '(71) 93333-7777', 'Beleza', 'Salão de beleza completo com 20% de desconto em todos os serviços para membros ANETI.', 'Desconto de 20% em cortes, coloração, manicure e pedicure. Agendamento obrigatório. Não válido em vésperas de feriados.', 'pendente', 0),
('AutoCenter Express', '89.012.345/0001-67', 'Curitiba', 'PR', 'contato@autocenter.com', '(41) 92222-8888', 'Automotivo', 'Centro automotivo completo com 12% de desconto em serviços e peças para membros ANETI.', 'Desconto de 12% em mão de obra e peças originais. Não válido para serviços de funilaria e pintura. Orçamento válido por 15 dias.', 'aprovada', 0);

-- Insert some demo coupons
INSERT INTO cupons (usuario_id, empresa_id, codigo) VALUES
(1, 1, '550e8400-e29b-41d4-a716-446655440001'),
(1, 2, '550e8400-e29b-41d4-a716-446655440002'),
(2, 1, '550e8400-e29b-41d4-a716-446655440003'),
(2, 3, '550e8400-e29b-41d4-a716-446655440004'),
(3, 4, '550e8400-e29b-41d4-a716-446655440005'),
(3, 6, '550e8400-e29b-41d4-a716-446655440006'),
(4, 2, '550e8400-e29b-41d4-a716-446655440007'),
(5, 5, '550e8400-e29b-41d4-a716-446655440008');

-- Create indexes for better performance
CREATE INDEX idx_empresas_search ON empresas(nome, categoria, cidade, status);
CREATE INDEX idx_cupons_user_date ON cupons(usuario_id, created_at);
CREATE INDEX idx_cupons_empresa_date ON cupons(empresa_id, created_at);

-- Create a view for company statistics
CREATE VIEW vw_empresa_stats AS
SELECT 
    e.id,
    e.nome,
    e.categoria,
    e.status,
    COUNT(c.id) as total_cupons,
    COUNT(CASE WHEN DATE(c.created_at) = CURDATE() THEN 1 END) as cupons_hoje,
    COUNT(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as cupons_semana,
    COUNT(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as cupons_mes
FROM empresas e
LEFT JOIN cupons c ON e.id = c.empresa_id
GROUP BY e.id, e.nome, e.categoria, e.status;

-- Create a view for user statistics
CREATE VIEW vw_usuario_stats AS
SELECT 
    u.id,
    u.nome,
    u.email,
    u.plano,
    COUNT(c.id) as total_cupons,
    COUNT(CASE WHEN DATE(c.created_at) = CURDATE() THEN 1 END) as cupons_hoje,
    COUNT(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as cupons_semana,
    COUNT(CASE WHEN c.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as cupons_mes,
    MAX(c.created_at) as ultimo_cupom
FROM usuarios u
LEFT JOIN cupons c ON u.id = c.usuario_id
GROUP BY u.id, u.nome, u.email, u.plano;

-- Create stored procedure for cleanup old data (optional)
DELIMITER //
CREATE PROCEDURE CleanupOldData()
BEGIN
    -- Remove coupons older than 2 years
    DELETE FROM cupons WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);
    
    -- Remove rejected companies older than 6 months
    DELETE FROM empresas WHERE status = 'rejeitada' AND created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
END //
DELIMITER ;

-- Grant permissions (adjust as needed for your setup)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON aneti_clube.* TO 'aneti_user'@'localhost' IDENTIFIED BY 'secure_password';
-- FLUSH PRIVILEGES;

-- Display setup information
SELECT 'Database schema created successfully!' as Status;
SELECT 'Default admin user: admin@aneti.net.br / admin123' as Admin_Login;
SELECT COUNT(*) as Total_Categories FROM categorias;
SELECT COUNT(*) as Total_Demo_Users FROM usuarios;
SELECT COUNT(*) as Total_Demo_Companies FROM empresas;
SELECT COUNT(*) as Total_Demo_Coupons FROM cupons;
