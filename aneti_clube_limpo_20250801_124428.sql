-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: aneti_clube
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('editor','admin','super') COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legacy_ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_ativo` (`legacy_ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Administrador ANETI','admin@aneti.net.br','super','ativo','$2y$12$Kht.3tsd0sTJjeVkuXL3TuVAbsw4oy/hjkD.fKHMi37HiOhTy1mxS',1,'2025-07-31 16:28:55','2025-08-01 12:26:36'),(3,'Editor Teste','editor@teste.com','editor','ativo','$2y$12$6swHejnMUJw1Xb2r/Z7yF.3GIELEbM5k3BlgUbtUs2VLgcaQ1zKGO',1,'2025-08-01 12:32:48','2025-08-01 12:32:48'),(4,'Marcos Wesley','marcos.wesley@hotmail.com.br','super','ativo','$2y$12$KnNuhzlRo6R.K3s1h6bOFOWCMRh.XDUEtQ0EVCbjuedKt.05Ftmgm',1,'2025-08-01 12:35:20','2025-08-01 12:35:20');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avaliacoes`
--

DROP TABLE IF EXISTS `avaliacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avaliacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `usuario_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int NOT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `avaliacoes_chk_1` CHECK (((`rating` >= 1) and (`rating` <= 5)))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avaliacoes`
--

LOCK TABLES `avaliacoes` WRITE;
/*!40000 ALTER TABLE `avaliacoes` DISABLE KEYS */;
INSERT INTO `avaliacoes` VALUES (1,1,'João Silva','joao@email.com',5,'Excelente atendimento e produtos de qualidade!','2025-07-31 17:24:50','2025-07-31 17:24:50'),(2,1,'Maria Santos','maria@email.com',4,'Muito bom, recomendo!','2025-07-31 17:24:50','2025-07-31 17:24:50'),(3,2,'Pedro Costa','pedro@email.com',5,'Comida deliciosa e ambiente agradável.','2025-07-31 17:24:50','2025-07-31 17:24:50'),(4,6,'Ana Lima','ana@email.com',4,'Hotel muito confortável, vista linda!','2025-07-31 17:24:50','2025-07-31 17:24:50'),(5,6,'Marcos Wesley','',3,'Excelente Hotel','2025-07-31 17:27:24','2025-07-31 17:27:24'),(6,6,'Marcos Fernadno','',2,'Excelente Hotel','2025-07-31 17:27:45','2025-07-31 17:27:45'),(7,6,'Novo Comente','',4,'Comente novo','2025-07-31 22:15:26','2025-07-31 22:15:26');
/*!40000 ALTER TABLE `avaliacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  KEY `idx_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Alimentação','Restaurantes, lanchonetes, delivery e estabelecimentos do ramo alimentício','2025-07-31 16:28:55'),(2,'Tecnologia','Lojas de eletrônicos, equipamentos de informática e serviços tecnológicos','2025-07-31 16:28:55'),(3,'Educação','Cursos, treinamentos, universidades e instituições de ensino','2025-07-31 16:28:55'),(4,'Saúde','Clínicas, laboratórios, farmácias e serviços de saúde','2025-07-31 16:28:55'),(5,'Beleza','Salões de beleza, estética, cosméticos e cuidados pessoais','2025-07-31 16:28:55'),(6,'Viagem','Agências de viagem, hotéis, pousadas e turismo','2025-07-31 16:28:55'),(7,'Esporte','Academias, lojas de artigos esportivos e atividades físicas','2025-07-31 16:28:55'),(8,'Entretenimento','Cinemas, teatros, eventos e atividades de lazer','2025-07-31 16:28:55'),(9,'Compras','Lojas de roupas, calçados, acessórios e varejo em geral','2025-07-31 16:28:55'),(10,'Serviços','Prestadores de serviços diversos, manutenção e consultoria','2025-07-31 16:28:55'),(11,'Automotivo','Concessionárias, oficinas, postos de combustível e serviços automotivos','2025-07-31 16:28:55'),(12,'Casa e Decoração','Móveis, decoração, materiais de construção e utilidades domésticas','2025-07-31 16:28:55');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupons`
--

DROP TABLE IF EXISTS `cupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `codigo` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `data_uso` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_usado` (`usado`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_cupons_user_date` (`usuario_id`,`created_at`),
  KEY `idx_cupons_empresa_date` (`empresa_id`,`created_at`),
  CONSTRAINT `cupons_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cupons_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cupons`
--

LOCK TABLES `cupons` WRITE;
/*!40000 ALTER TABLE `cupons` DISABLE KEYS */;
INSERT INTO `cupons` VALUES (1,1,1,'550e8400-e29b-41d4-a716-446655440001',0,NULL,'2025-07-31 16:28:55'),(2,1,2,'550e8400-e29b-41d4-a716-446655440002',0,NULL,'2025-07-31 16:28:55'),(3,2,1,'550e8400-e29b-41d4-a716-446655440003',0,NULL,'2025-07-31 16:28:55'),(4,2,3,'550e8400-e29b-41d4-a716-446655440004',0,NULL,'2025-07-31 16:28:55'),(5,3,4,'550e8400-e29b-41d4-a716-446655440005',0,NULL,'2025-07-31 16:28:55'),(6,3,6,'550e8400-e29b-41d4-a716-446655440006',0,NULL,'2025-07-31 16:28:55'),(7,4,2,'550e8400-e29b-41d4-a716-446655440007',0,NULL,'2025-07-31 16:28:55'),(8,5,5,'550e8400-e29b-41d4-a716-446655440008',0,NULL,'2025-07-31 16:28:55'),(9,1,6,'7ab9a273-40e8-4db2-b594-61c1c3c3a207',0,NULL,'2025-07-31 20:12:54'),(10,1,6,'065fd0c7-d7ec-4d0a-b8bc-c87a162eba9c',0,NULL,'2025-07-31 23:11:13'),(11,1,6,'316e65e1-409b-41af-be7e-7602c511bb9e',0,NULL,'2025-07-31 23:11:31'),(12,1,5,'8ab2a8da-2724-4e11-b7c1-890acb84b332',0,NULL,'2025-07-31 23:11:46'),(13,1,5,'3cc5a1b6-a862-448e-99c0-970b98a99359',0,NULL,'2025-07-31 23:13:31'),(14,1,5,'1aa9a46c-84dc-4c71-a21c-07181652bfc1',0,NULL,'2025-07-31 23:14:33'),(15,1,5,'f83d6984-2099-433c-b52c-aa7aea7a299b',0,NULL,'2025-07-31 23:15:56'),(16,1,5,'84b80d51-ae14-492c-b375-674cd87c876d',0,NULL,'2025-07-31 23:18:03');
/*!40000 ALTER TABLE `cupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `regras` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pendente','aprovada','rejeitada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `destaque` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `desconto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem_detalhes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cnpj` (`cnpj`),
  KEY `idx_nome` (`nome`),
  KEY `idx_cnpj` (`cnpj`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_cidade` (`cidade`),
  KEY `idx_estado` (`estado`),
  KEY `idx_status` (`status`),
  KEY `idx_destaque` (`destaque`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_empresas_search` (`nome`,`categoria`,`cidade`,`status`),
  CONSTRAINT `empresas_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`nome`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'Magalu','12.345.678/0001-90','688b9c1595ede.jpeg','São Paulo','SP','contato@techstore.com','(11) 99999-1111','Tecnologia','Loja especializada em equipamentos de informática e eletrônicos com 15% de desconto para membros ANETI.','Desconto de 15% em produtos selecionados. Não cumulativo com outras promoções. Válido apenas para membros ativos da ANETI.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:38:45','15','https://techstore.com','Rua Augusta, 123',NULL),(2,'Centauro','23.456.789/0001-01','688b9c2200a2c.webp','Rio de Janeiro','RJ','contato@saborarte.com','(21) 98888-2222','Alimentação','Restaurante contemporâneo oferecendo 20% de desconto no almoço executivo para engenheiros da ANETI.','Desconto de 20% no almoço executivo de segunda a sexta. Não válido em feriados e datas comemorativas. Apresentar carteirinha da ANETI.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:38:58','20','https://saborarte.com','Rua Copacabana, 456',NULL),(3,'O Boticario','34.567.890/0001-12','688b9c30d508e.webp','Belo Horizonte','MG','contato@fitlife.com','(31) 97777-3333','Esporte','Academia completa com equipamentos modernos e 25% de desconto na mensalidade para membros ANETI.','Desconto de 25% na primeira mensalidade. Matrícula grátis. Válido apenas para novos alunos membros da ANETI.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:39:12','25','https://fitlife.com','Av. Brasil, 789',NULL),(4,'NetShoes','45.678.901/0001-23','688b9c8c66058.webp','Porto Alegre','RS','contato@odontosaude.com','(51) 96666-4444','Saúde','Clínica odontológica moderna oferecendo 30% de desconto em tratamentos para membros ANETI e familiares.','Desconto de 30% em consultas e tratamentos. Válido para titular e dependentes. Agendamento obrigatório com antecedência mínima de 24h.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:40:44','30','https://odontosaude.com','Rua da Praia, 321',NULL),(5,'Petz','56.789.012/0001-34','688b9c9f54d3d.webp','Brasília','DF','contato@conhecimento.com','(61) 95555-5555','Educação','Livraria especializada em livros técnicos e cursos com 10% de desconto para membros ANETI.','Desconto de 10% em livros técnicos e cursos online. Não válido para livros em promoção. Compras acima de R$ 100,00.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:41:03','10','https://conhecimento.com','SQN 123, Asa Norte',NULL),(6,'Hotel Vista Mar','67.890.123/0001-45','688b9b3e9bf61.png','Florianópolis','SC','contato@vistmar.com','(48) 94444-6666','Viagem','Hotel 4 estrelas com vista para o mar oferecendo 15% de desconto em hospedagem para membros ANETI.','Desconto de 15% nas diárias. Válido para reservas com antecedência mínima de 7 dias. Não válido em alta temporada e feriados prolongados.','aprovada',1,'2025-07-31 16:28:55','2025-07-31 16:35:10','15','https://vistamar.com','Av. Beira Mar, 555','688b9b3e9c08c.jpg'),(7,'Salão Elegance','78.901.234/0001-56',NULL,'Salvador','BA','contato@elegance.com','(71) 93333-7777','Beleza','Salão de beleza completo com 20% de desconto em todos os serviços para membros ANETI.','Desconto de 20% em cortes, coloração, manicure e pedicure. Agendamento obrigatório. Não válido em vésperas de feriados.','pendente',0,'2025-07-31 16:28:55','2025-07-31 16:32:50','20','https://elegance.com','Rua Castro Alves, 88',NULL),(8,'AutoCenter Express','89.012.345/0001-67',NULL,'Curitiba','PR','contato@autocenter.com','(41) 92222-8888','Automotivo','Centro automotivo completo com 12% de desconto em serviços e peças para membros ANETI.','Desconto de 12% em mão de obra e peças originais. Não válido para serviços de funilaria e pintura. Orçamento válido por 15 dias.','aprovada',0,'2025-07-31 16:28:55','2025-07-31 16:32:51','12','https://autocenter.com','Rua XV de Novembro, 999',NULL);
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membros`
--

DROP TABLE IF EXISTS `membros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plano` enum('junior','pleno','senior') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'junior',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_plano` (`plano`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membros`
--

LOCK TABLES `membros` WRITE;
/*!40000 ALTER TABLE `membros` DISABLE KEYS */;
INSERT INTO `membros` VALUES (1,'João Silva','joao.silva@email.com','junior',1,'2025-07-31 16:32:43','2025-07-31 16:32:43'),(2,'Maria Santos','maria.santos@email.com','pleno',1,'2025-07-31 16:32:43','2025-07-31 16:32:43'),(3,'Carlos Oliveira','carlos.oliveira@email.com','senior',1,'2025-07-31 16:32:43','2025-07-31 16:32:43'),(4,'Ana Costa','ana.costa@email.com','pleno',1,'2025-07-31 16:32:43','2025-07-31 16:32:43'),(5,'Pedro Fernandes','pedro.fernandes@email.com','junior',1,'2025-07-31 16:32:43','2025-07-31 16:32:43'),(6,'Juliana Rodrigues','juliana.rodrigues@email.com','senior',1,'2025-07-31 16:32:43','2025-07-31 16:32:43');
/*!40000 ALTER TABLE `membros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membros_api_access`
--

DROP TABLE IF EXISTS `membros_api_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membros_api_access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plano` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `primeiro_acesso` datetime NOT NULL,
  `ultimo_acesso` datetime NOT NULL,
  `total_acessos` int DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_ultimo_acesso` (`ultimo_acesso`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membros_api_access`
--

LOCK TABLES `membros_api_access` WRITE;
/*!40000 ALTER TABLE `membros_api_access` DISABLE KEYS */;
INSERT INTO `membros_api_access` VALUES (1,1010,'Fernando','maarcoswesleey@gmail.com','Honra','2025-08-01 11:49:49','2025-08-01 11:49:49',1);
/*!40000 ALTER TABLE `membros_api_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slides_banner`
--

DROP TABLE IF EXISTS `slides_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slides_banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` int NOT NULL DEFAULT '0',
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mobile_only` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_ordem` (`ordem`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slides_banner`
--

LOCK TABLES `slides_banner` WRITE;
/*!40000 ALTER TABLE `slides_banner` DISABLE KEYS */;
INSERT INTO `slides_banner` VALUES (3,'1754050558_Banner_para_loja_online_de_smartwatch_e_acess__rios_azul_e_branco__3_.png',1,'ativo','2025-08-01 12:15:58','2025-08-01 12:15:58',0);
/*!40000 ALTER TABLE `slides_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plano` enum('junior','pleno','senior') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'junior',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_plano` (`plano`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'João Silva','joao.silva@email.com','junior',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e'),(2,'Maria Santos','maria.santos@email.com','pleno',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e'),(3,'Carlos Oliveira','carlos.oliveira@email.com','senior',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e'),(4,'Ana Costa','ana.costa@email.com','pleno',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e'),(5,'Pedro Fernandes','pedro.fernandes@email.com','junior',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e'),(6,'Juliana Rodrigues','juliana.rodrigues@email.com','senior',1,'2025-07-31 16:28:55','2025-07-31 20:26:29','e10adc3949ba59abbe56e057f20f883e');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vw_empresa_stats`
--

DROP TABLE IF EXISTS `vw_empresa_stats`;
/*!50001 DROP VIEW IF EXISTS `vw_empresa_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_empresa_stats` AS SELECT 
 1 AS `id`,
 1 AS `nome`,
 1 AS `categoria`,
 1 AS `status`,
 1 AS `total_cupons`,
 1 AS `cupons_hoje`,
 1 AS `cupons_semana`,
 1 AS `cupons_mes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_usuario_stats`
--

DROP TABLE IF EXISTS `vw_usuario_stats`;
/*!50001 DROP VIEW IF EXISTS `vw_usuario_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_usuario_stats` AS SELECT 
 1 AS `id`,
 1 AS `nome`,
 1 AS `email`,
 1 AS `plano`,
 1 AS `total_cupons`,
 1 AS `cupons_hoje`,
 1 AS `cupons_semana`,
 1 AS `cupons_mes`,
 1 AS `ultimo_cupom`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_empresa_stats`
--

/*!50001 DROP VIEW IF EXISTS `vw_empresa_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_empresa_stats` AS select `e`.`id` AS `id`,`e`.`nome` AS `nome`,`e`.`categoria` AS `categoria`,`e`.`status` AS `status`,count(`c`.`id`) AS `total_cupons`,count((case when (cast(`c`.`created_at` as date) = curdate()) then 1 end)) AS `cupons_hoje`,count((case when (`c`.`created_at` >= (now() - interval 7 day)) then 1 end)) AS `cupons_semana`,count((case when (`c`.`created_at` >= (now() - interval 30 day)) then 1 end)) AS `cupons_mes` from (`empresas` `e` left join `cupons` `c` on((`e`.`id` = `c`.`empresa_id`))) group by `e`.`id`,`e`.`nome`,`e`.`categoria`,`e`.`status` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_usuario_stats`
--

/*!50001 DROP VIEW IF EXISTS `vw_usuario_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_usuario_stats` AS select `u`.`id` AS `id`,`u`.`nome` AS `nome`,`u`.`email` AS `email`,`u`.`plano` AS `plano`,count(`c`.`id`) AS `total_cupons`,count((case when (cast(`c`.`created_at` as date) = curdate()) then 1 end)) AS `cupons_hoje`,count((case when (`c`.`created_at` >= (now() - interval 7 day)) then 1 end)) AS `cupons_semana`,count((case when (`c`.`created_at` >= (now() - interval 30 day)) then 1 end)) AS `cupons_mes`,max(`c`.`created_at`) AS `ultimo_cupom` from (`usuarios` `u` left join `cupons` `c` on((`u`.`id` = `c`.`usuario_id`))) group by `u`.`id`,`u`.`nome`,`u`.`email`,`u`.`plano` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-01 12:44:28
