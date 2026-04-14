-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: chat_db
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

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
-- Table structure for table `chamado_anexos`
--

DROP TABLE IF EXISTS `chamado_anexos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado_anexos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` int unsigned NOT NULL,
  `arquivo_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamanho_bytes` int unsigned DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chamado_id` (`chamado_id`),
  CONSTRAINT `chamado_anexos_ibfk_1` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamado_anexos`
--

LOCK TABLES `chamado_anexos` WRITE;
/*!40000 ALTER TABLE `chamado_anexos` DISABLE KEYS */;
INSERT INTO `chamado_anexos` VALUES (1,15,'chamados/15/ff8915a69be2dcbe4487452acd560281.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-03-24 20:05:15'),(2,16,'chamados/16/f6a40b2f214c1f1fc52c930e13c4ff08.png','Captura de tela 2026-01-20 163117.png','image/png',66214,'2026-03-25 16:42:33'),(3,17,'chamados/17/3dd4ad2558d5fe4c07be84195e2e737c.png','Captura de tela 2025-11-10 161423.png','image/png',56914,'2026-03-25 16:54:03'),(4,18,'chamados/18/5298d8df8d52dce65187c803013d63a2.png','Captura de tela 2025-11-10 161423.png','image/png',56914,'2026-03-25 16:57:53'),(5,19,'chamados/19/a0783b7bb76549676561aa1142aec02f.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-03-25 17:01:36'),(6,20,'chamados/20/90830c9d97822d82f6454c3115e9ca05.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-03-25 18:52:41'),(7,21,'chamados/21/9059b06248511458557f36cae9bed51f.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-03-25 19:35:13'),(8,22,'chamados/22/211a58c650c2f6716aaeabd59640179b.png','Captura de tela 2025-11-13 153152.png','image/png',17534,'2026-03-25 19:41:40'),(9,23,'chamados/23/3587134e84a8596c889e489cdf4c14a1.png','Captura de tela 2025-11-18 165131.png','image/png',62105,'2026-03-25 19:43:03'),(10,24,'chamados/24/8e4b676c661de020dbb9993d570af56a.png','Captura de tela 2025-10-14 155734.png','image/png',524788,'2026-03-25 19:47:31'),(11,25,'chamados/25/7eae554049cb7f54471e229673953fee.png','Captura de tela 2025-11-18 164725.png','image/png',121648,'2026-03-30 19:41:30'),(12,26,'chamados/26/c4053d659da53044c3440238bdead05a.png','Captura de tela 2025-11-11 144730.png','image/png',84518,'2026-03-31 16:36:52'),(13,27,'chamados/27/b71ba02c7f83a8102c3f873556358307.png','Captura de tela 2025-11-13 152627.png','image/png',70634,'2026-03-31 17:11:28'),(14,28,'chamados/28/b035e66554545b2a892a6ec05b7d32a5.png','Captura de tela 2025-11-13 153152.png','image/png',17534,'2026-03-31 17:54:16'),(15,29,'chamados/29/786d54725062674a471fdd0a5659fe75.png','Captura de tela 2025-11-13 161755.png','image/png',256149,'2026-03-31 17:58:27'),(16,30,'chamados/30/4c2a315618fc1e779bd01ba1ee25a127.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-03-31 17:59:31'),(17,31,'chamados/31/63b05bf8e682fe0e7704e6eec5753c58.png','Captura de tela 2025-11-13 152627.png','image/png',70634,'2026-03-31 18:09:42'),(18,32,'chamados/32/06600a664c07a60f297e09f87a298f19.png','Captura de tela 2025-11-13 152627.png','image/png',70634,'2026-03-31 18:11:24'),(19,33,'chamados/33/2110be1a4d2c2679028e551f51a763e4.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-03-31 18:15:56'),(20,33,'chamados/33/a116c021d88072ae053add75a2c49d0e.png','Captura de tela 2025-11-11 144209.png','image/png',91010,'2026-03-31 18:15:56'),(21,34,'chamados/34/803c6f7d670b9d120646f78a9b721ca0.png','Captura de tela 2025-11-11 144209.png','image/png',91010,'2026-03-31 18:36:44'),(22,34,'chamados/34/fd715056b3dc8098869a48f2e4264315.png','Captura de tela 2025-11-10 161423.png','image/png',56914,'2026-03-31 18:36:44'),(23,34,'chamados/34/177dc2151386bda9c54f8c76dc07cca7.png','Captura de tela 2025-11-05 155052.png','image/png',46162,'2026-03-31 18:36:44'),(24,34,'chamados/34/6d5a6ca4de60a032710a5f63bc0de978.png','Captura de tela 2025-10-14 155734.png','image/png',524788,'2026-03-31 18:36:44'),(25,35,'chamados/35/19a84c325bd62bf9e0632598054d7c84.png','Captura de tela 2025-11-11 150121.png','image/png',72317,'2026-04-01 17:41:32'),(26,35,'chamados/35/c385e2454a7901de983abccf42ee6a2d.png','Captura de tela 2025-11-11 144730.png','image/png',84518,'2026-04-01 17:41:32'),(27,36,'chamados/36/2161002059f9d79eab3b76acb569a906.png','Captura de tela 2025-11-13 161755.png','image/png',256149,'2026-04-06 17:23:45'),(28,37,'chamados/37/40c434ea8026304fe0ed26d10b96d426.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-07 18:26:01'),(29,38,'chamados/38/914acd159bb1d9db9e0a316527bf3e93.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-07 18:28:23'),(30,38,'chamados/38/4a1868625a82335d3ded11a5648c16d2.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-04-07 18:28:23'),(31,39,'chamados/39/e53c75b67647f43e0e9ed40a36e8f9ae.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-07 19:08:10'),(32,39,'chamados/39/c2229c1f0f4418985177e2f737367cf6.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-04-07 19:08:10'),(33,40,'chamados/40/5821dbfeb2d66cea5303795d2b20dd10.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-07 19:12:07'),(34,40,'chamados/40/39a8fcb66bc1eae5db8ec25c2e436d56.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-04-07 19:12:07'),(35,41,'chamados/41/7162b9d4504be20efaa6cb9914183adf.png','Captura de tela 2025-11-13 152627.png','image/png',70634,'2026-04-07 19:15:39'),(36,41,'chamados/41/ca088e333de86e8e9f27257c10872610.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-07 19:15:39'),(37,41,'chamados/41/ac8fb6805eeb6cbc1fe1306b40521f1b.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-04-07 19:15:39'),(38,42,'chamados/42/05eab83c2a4d632d997d5a1a717c62a9.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-13 17:13:21'),(39,42,'chamados/42/bab65aeb46f607a73fe51b6c0d51b46c.png','Captura de tela 2025-11-12 145607.png','image/png',123581,'2026-04-13 17:13:21');
/*!40000 ALTER TABLE `chamado_anexos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chamado_comentario_anexos`
--

DROP TABLE IF EXISTS `chamado_comentario_anexos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado_comentario_anexos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `comentario_id` int unsigned NOT NULL,
  `arquivo_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamanho_bytes` int unsigned DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chamado_comentario_anexos_comentario` (`comentario_id`),
  CONSTRAINT `chamado_comentario_anexos_ibfk_1` FOREIGN KEY (`comentario_id`) REFERENCES `chamado_comentarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamado_comentario_anexos`
--

LOCK TABLES `chamado_comentario_anexos` WRITE;
/*!40000 ALTER TABLE `chamado_comentario_anexos` DISABLE KEYS */;
INSERT INTO `chamado_comentario_anexos` VALUES (1,2,'chamados-comentarios/4/2/512d2ed40da89ed9d0a1d83e14e66a62.png','Captura de tela 2025-11-12 150650.png','image/png',34510,'2026-04-01 17:58:16'),(2,3,'chamados-comentarios/4/3/0ee26caff2c45ee40942a3c62c979438.png','Captura de tela 2025-11-11 150734.png','image/png',47206,'2026-04-01 17:58:26');
/*!40000 ALTER TABLE `chamado_comentario_anexos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chamado_comentarios`
--

DROP TABLE IF EXISTS `chamado_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado_comentarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chamado_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('comentario','resolucao') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comentario',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chamado_comentarios_chamado_criado` (`chamado_id`,`criado_em`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `chamado_comentarios_ibfk_1` FOREIGN KEY (`chamado_id`) REFERENCES `chamados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chamado_comentarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamado_comentarios`
--

LOCK TABLES `chamado_comentarios` WRITE;
/*!40000 ALTER TABLE `chamado_comentarios` DISABLE KEYS */;
INSERT INTO `chamado_comentarios` VALUES (1,4,1,'teste comentário','comentario','2026-04-01 17:58:09'),(2,4,1,NULL,'comentario','2026-04-01 17:58:16'),(3,4,1,NULL,'comentario','2026-04-01 17:58:26'),(4,35,1,'quase finalizado','comentario','2026-04-01 17:59:08'),(5,35,1,'finalizado com sucesso','resolucao','2026-04-01 17:59:33'),(6,35,1,'depois de um tempo','comentario','2026-04-01 18:00:00'),(7,34,1,'testando os comentários','comentario','2026-04-01 18:13:24'),(8,38,1,'servidor parou de funcionar','comentario','2026-04-07 18:29:28'),(9,34,1,'.','comentario','2026-04-07 19:08:22'),(10,40,1,'teste','comentario','2026-04-07 19:12:25'),(11,41,1,'teste','comentario','2026-04-07 19:15:58'),(12,42,1,'comentário 1','comentario','2026-04-14 17:02:35');
/*!40000 ALTER TABLE `chamado_comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chamado_taxonomias`
--

DROP TABLE IF EXISTS `chamado_taxonomias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamado_taxonomias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subcategoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_categoria_subcategoria` (`categoria`,`subcategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamado_taxonomias`
--

LOCK TABLES `chamado_taxonomias` WRITE;
/*!40000 ALTER TABLE `chamado_taxonomias` DISABLE KEYS */;
INSERT INTO `chamado_taxonomias` VALUES (1,'ERP','Financeiro',1,'2026-03-25 18:49:56'),(2,'ERP','Fiscal',1,'2026-03-25 18:49:56'),(3,'ERP','Contabilidade',1,'2026-03-25 18:49:56'),(4,'ERP','Vendas',1,'2026-03-25 18:49:56'),(5,'ERP','Estoque',1,'2026-03-25 18:49:56'),(6,'Infraestrutura','Servidor',1,'2026-03-25 18:49:56'),(7,'Infraestrutura','Backup',1,'2026-03-25 18:49:56'),(8,'Infraestrutura','Cloud',1,'2026-03-25 18:49:56'),(9,'Infraestrutura','Banco de Dados',1,'2026-03-25 18:49:56'),(10,'Engenharia','AutoCAD',1,'2026-03-25 18:49:56'),(11,'Engenharia','Solidworks',1,'2026-03-25 18:49:56'),(12,'Engenharia','Revisão Técnica',1,'2026-03-25 18:49:56'),(13,'Redes','Wi-Fi',1,'2026-03-25 18:49:56'),(14,'Redes','Cabeamento',1,'2026-03-25 18:49:56'),(15,'Redes','VPN',1,'2026-03-25 18:49:56'),(16,'Segurança','Antivírus',1,'2026-03-25 18:49:56'),(17,'Segurança','Firewall',1,'2026-03-25 18:49:56'),(18,'Segurança','Câmeras',1,'2026-03-25 18:49:56'),(19,'Hardware','Desktop/Notebook',1,'2026-03-25 18:49:56'),(20,'Hardware','Impressora',1,'2026-03-25 18:49:56'),(21,'Hardware','Periféricos',1,'2026-03-25 18:49:56'),(22,'Acessos','Reset de Senha',1,'2026-03-25 18:49:56'),(23,'Acessos','Novo Usuário',1,'2026-03-25 18:49:56'),(24,'Acessos','Permissões',1,'2026-03-25 18:49:56'),(25,'ERP','Marketing',1,'2026-03-25 19:06:29'),(27,'Geral','Geral',1,'2026-03-25 19:22:30'),(41,'Acessos','Nome de usuário',0,'2026-03-31 16:38:49'),(44,'Acessos','Nome usuário',0,'2026-03-31 16:40:02'),(58,'Acessos','Confirmar Senha',1,'2026-04-13 19:24:35');
/*!40000 ALTER TABLE `chamado_taxonomias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chamados`
--

DROP TABLE IF EXISTS `chamados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chamados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned NOT NULL,
  `atribuido_a` int unsigned DEFAULT NULL,
  `resolvido_por` int unsigned DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subcategoria` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao_rich` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aberto','classificado','em_andamento','resolvido','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberto',
  `prioridade` enum('baixa','media','alta','critica') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `atribuido_a` (`atribuido_a`),
  KEY `fk_chamados_resolvido_por` (`resolvido_por`),
  CONSTRAINT `chamados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chamados_ibfk_2` FOREIGN KEY (`atribuido_a`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_chamados_resolvido_por` FOREIGN KEY (`resolvido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamados`
--

LOCK TABLES `chamados` WRITE;
/*!40000 ALTER TABLE `chamados` DISABLE KEYS */;
INSERT INTO `chamados` VALUES (1,1,NULL,NULL,'Teste','ERP','Financeiro','Teste','resolvido','alta','2026-03-20 16:51:13','2026-03-24 18:33:43'),(2,1,NULL,NULL,'Impressora quebrada','Hardware','Impressora','Impressora do 2 andar nao funciona','resolvido','media','2026-03-20 16:51:31','2026-03-24 19:10:47'),(3,1,NULL,NULL,'teste2','','','teste2','resolvido','baixa','2026-03-20 16:59:57','2026-03-24 19:31:01'),(4,1,NULL,NULL,'teste3','Segurança','Antivírus','teste3','cancelado','critica','2026-03-20 17:01:47','2026-04-01 18:59:17'),(5,1,NULL,NULL,'teste4','Acessos','Novo Usuário','teste4','classificado','alta','2026-03-20 19:25:11','2026-03-24 19:28:50'),(6,1,NULL,NULL,'teste emergência','Engenharia','Solidworks','teste','resolvido','media','2026-03-23 16:57:24','2026-03-24 19:18:06'),(7,1,NULL,NULL,'teste emergencia','ERP','Financeiro','teste','resolvido','critica','2026-03-23 17:16:34','2026-03-24 18:29:30'),(8,1,NULL,NULL,'teste2','Redes','Cabeamento','teste','resolvido','baixa','2026-03-23 17:21:05','2026-03-24 18:43:20'),(9,2,NULL,NULL,'Wi-FI caiu','Redes','Wi-Fi','Sem conexão','resolvido','media','2026-03-24 18:07:46','2026-03-25 20:01:02'),(10,3,NULL,NULL,'Servidor caiu','Infraestrutura','Servidor','Que bom que a parte visual e a movimentação dos cards finalmente ficaram 100%! Estamos quase lá.\r\n\r\nOs dois problemas que sobraram acontecem pelo mesmo motivo: o sistema de chamados ainda não sabe exatamente como o seu sistema de chat funciona. Como eu não tenho acesso ao código do seu chat, precisei \"chutar\" algumas coisas. Para deixarmos isso perfeito agora, preciso que você me tire duas dúvidas rápidas sobre como o seu chat foi construído:\r\n\r\n1. Sobre o botão \"Chamar Setor\" ir para o Admin\r\nNo código Javascript, eu coloquei para o botão redirecionar para a URL: /chat?conversa_com=ID_DO_USUARIO. Provavelmente o seu sistema usa um formato de link diferente para abrir a conversa com alguém.\r\n\r\nO que eu preciso que você faça:\r\nAbra o seu sistema normalmente, vá até o chat e clique para conversar com algum usuário específico. Olhe lá em cima na barra de endereços do navegador. Como fica a URL?\r\n(Exemplo: fica /chat?id=5, ou /mensagens/usuario/5, ou /chat/5?)\r\n\r\nSabendo isso, eu arrumo a função chamarSetor no Javascript na hora.\r\n\r\n2. Sobre a mensagem automática não enviar\r\nLembra que eu te perguntei sobre a tabela conversas na mensagem anterior?\r\nA sua tabela mensagens exige obrigatoriamente um conversa_id. O banco de dados está bloqueando a mensagem automática porque nós não estamos dizendo a ele em qual conversa colocar o texto.','resolvido','critica','2026-03-24 18:46:56','2026-03-24 19:31:18'),(11,6,1,1,'Computador não liga','Hardware','Desktop/Notebook','Computador não liga','resolvido','baixa','2026-03-24 19:19:04','2026-03-30 19:39:31'),(12,6,NULL,NULL,'Cabo de rede não funciona','Infraestrutura','Backup','Cabo de rede não funciona','resolvido','media','2026-03-24 19:33:06','2026-03-30 17:39:25'),(13,1,NULL,NULL,'Teste de Imagem','ERP','Financeiro','testar imagem','classificado','media','2026-03-24 19:49:09','2026-03-31 17:27:43'),(14,4,1,1,'Teste 2 de Imagem','Hardware','Impressora','imagem funciona?','resolvido','critica','2026-03-24 19:56:43','2026-03-30 19:39:31'),(15,4,NULL,NULL,'Teste 3 imagem','ERP','Estoque','imagem','resolvido','media','2026-03-24 20:05:15','2026-03-25 16:44:10'),(16,2,NULL,NULL,'Ar condicionado não esfria','Hardware','Periféricos','JavaScript Vanilla.\r\n\r\n* Arquitetura: MVC simplificado. O roteamento é feito pelo Slim 4 em `public/index.php`. O histórico de mensagens e chamados é via API REST, enquanto o tempo real do chat é via WebSocket.\r\n\r\nO que precisamos implementar: Uma nova funcionalidade de Triagem e Classificação de Chamados exclusiva para usuários com papel (`papel`) \'ti\'.\r\n\r\nRequisitos Detalhados:\r\n\r\n1. Acesso: Adicionar um botão no Dashboard/Tela Inicial visível apenas para usuários \'ti\' que direcione para uma nova visualização de \"Gestão de Chamados\".\r\n\r\n2. Fluxo de Triagem:\r\n\r\n   * Novos chamados devem cair em uma seção de \"Pendentes de Classificação\".\r\n\r\n   * O profissional de TI deve poder selecionar um chamado e definir:\r\n\r\n      * Prioridade: Baixa, Média, Alta ou Crítica.\r\n\r\n      * Categoria: ERP, Engenharia, Infraestrutura, Redes, Segurança, Hardware ou Acessos.\r\n\r\n      * Subcategoria: Baseada na categoria (Ex: ERP -> Financeiro/Fiscal; Engenharia -> AutoCAD/Solidworks; Infraestrutura -> Servidor/Backup/Cloud/Banco de Dados).\r\n\r\n3. Visualização Pós-Classificação:\r\n\r\n   * Após classificados, os chamados devem ser movidos para uma seção de \"Chamados Documentados\".\r\n\r\n   * Esta seção deve ser organizada por Setor/Categoria e ordenada pela Urgência (Crítica > Alta > Média > Baixa).','resolvido','media','2026-03-25 16:42:33','2026-03-25 19:07:38'),(17,2,NULL,NULL,'Servidor parou','Infraestrutura','Servidor','.','resolvido','critica','2026-03-25 16:54:03','2026-03-25 16:54:41'),(18,2,NULL,NULL,'Servidor parou','Infraestrutura','Servidor','.','resolvido','critica','2026-03-25 16:57:53','2026-03-25 16:58:43'),(19,2,1,1,'Servidor caiu','Redes','VPN',';','resolvido','alta','2026-03-25 17:01:36','2026-03-30 19:39:31'),(20,1,NULL,NULL,'Chamado 1','ERP','Marketing','teste','resolvido','media','2026-03-25 18:52:41','2026-03-30 18:50:07'),(21,2,NULL,NULL,'Teste notificação','Segurança','Antivírus','teste','resolvido','alta','2026-03-25 19:35:13','2026-03-25 19:35:53'),(22,2,NULL,NULL,'Novo teste notificação','Segurança','Antivírus','Notificação de chamados ok?','resolvido','baixa','2026-03-25 19:41:40','2026-03-30 17:37:59'),(23,2,NULL,NULL,'Teste de notificação','Redes','VPN','funciona?','resolvido','media','2026-03-25 19:43:03','2026-03-25 19:56:39'),(24,2,NULL,NULL,'ultimo teste de notificação','Geral','Geral','','resolvido','critica','2026-03-25 19:47:31','2026-03-25 20:08:18'),(25,4,1,1,'Teste usuário que finaliza chamado','Engenharia','AutoCAD','Vou focar só nesse ponto e rastrear o fluxo completo: onde grava resolvido_por, qual endpoint é chamado ao finalizar e como o nome é montado no retorno para a tela.\r\n\r\nAchei uma hipótese forte: no seu banco real provavelmente a coluna chamados.resolvido_por não existe (ou não foi migrada), então o sistema cai no fallback e sempre retorna Nao informado. Vou confirmar as rotas e endurecer o backend para criar/usar essa coluna automaticamente.\r\n\r\nVou aplicar uma correção definitiva no backend para não depender de migração manual: se resolvido_por não existir, o sistema tenta criar automaticamente antes de atualizar/listar. Em seguida valido sintaxe.\r\n\r\nAjuste aplicado no backend para autocriar/usar resolvido_por; agora vou validar sintaxe e checar no banco se a coluna existe de fato.\r\n\r\nTenho um diagnóstico concreto: no banco, todos os chamados resolvidos continuam com resolvido_por = NULL, inclusive os de hoje. Vou checar se existe outro fluxo frontend que sobrescreve status para resolvido sem gravar o usuário, e então corrigir esse ponto direto.\r\n\r\nEncontrei evidência no banco: os chamados estão realmente ficando com resolvido_por = NULL. Para destravar isso agora, vou adicionar um fallback server-side: quando vier NULL, o backend identifica o finalizador pela mensagem automática do chamado e já devolve o nome correto (e tenta corrigir o registro no banco).\r\n\r\nFiz mais um endurecimento para garantir resultado no seu uso real: ao finalizar, também vou gravar o usuário em atribuido_a e usar isso como fallback de nome. Assim, mesmo se resolvido_por falhar em algum ambiente, o nome ainda aparece corretamente.\r\n\r\nTambém deixei o fluxo de finalização mais confiável: o backend agora retorna explicitamente o resolvido_por_nome salvo, e o frontend só mostra sucesso com esse retorno. Isso evita falso positivo de “finalizado” quando algo falha silenciosamente.\r\n\r\nCorreção focada no nome de quem finalizou foi aplicada com reforço em 3 níveis.\r\n\r\nGravação mais robusta ao finalizar\r\nAo finalizar chamado, o backend agora grava:\r\nstatus = resolvido\r\nresolvido_por = usuário logado\r\natribuido_a = usuário logado (fallback)\r\nArquivo alterado: ChamadoController.php\r\nExibição com fallback real no histórico\r\nNa listagem de chamados, o nome exibido agora usa:\r\nCOALESCE(r.nome, a.nome) como resolvido_por_nome\r\nIsso garante nome mesmo se resolvido_por falhar em algum cenário, usando atribuido_a.\r\nArquivo alterado: ChamadoController.php\r\nRecuperação automática para casos antigos\r\nSe chamado está resolvido e ainda vem sem nome, o backend tenta recuperar pelo texto da mensagem automática e corrige o banco.\r\nTambém deixei rotina de autocorreção já rodando na listagem.\r\nArquivo alterado: ChamadoController.php\r\nFrontend só confirma sucesso com retorno válido\r\nO botão Finalizar agora valida JSON de sucesso e mostra o nome retornado da API.\r\nEvita “sucesso falso”.\r\nArquivo alterado: dashboard_ti.php:744\r\nValidação executada\r\n\r\nSem erros de sintaxe:\r\nChamadoController.php\r\ndashboard_ti.php\r\nDiagnóstico encontrado no banco\r\n\r\nA coluna resolvido_por existe.\r\nMuitos chamados antigos estavam com resolvido_por NULL.\r\nFiz backfill automático e corrigiu 3 chamados que tinham mensagem automática compatível.\r\nOs que ficaram NULL não tinham mensagem automática rastreável para inferir o finalizador com segurança.\r\nPróximo teste que você deve fazer agora\r\n\r\nFinalize um chamado novo no dashboard.\r\nEle deve aparecer no histórico com o nome correto (não “Nao informado”).\r\nSe quiser, eu também deixo um script administrativo para preencher manualmente os antigos que ainda ficaram sem identificação.','resolvido','media','2026-03-30 19:41:30','2026-03-30 19:42:21'),(26,2,5,5,'Teste de notificação','Acessos','Novo Usuário','teste de notificação + imagens','resolvido','critica','2026-03-31 16:36:52','2026-03-31 16:41:07'),(27,1,5,5,'teste refatoração','Infraestrutura','Banco de Dados','teste refatoração','resolvido','media','2026-03-31 17:11:28','2026-03-31 17:13:13'),(28,5,NULL,NULL,'teste imagem','Redes','Cabeamento','cade a imagem?','classificado','media','2026-03-31 17:54:16','2026-03-31 17:54:32'),(29,2,1,1,'Impressora não funciona','Hardware','Impressora','impressora parou de funcionar','resolvido','baixa','2026-03-31 17:58:27','2026-03-31 17:58:59'),(30,2,1,1,'Impressora não funciona 2','Redes','Cabeamento','De novo','resolvido','baixa','2026-03-31 17:59:31','2026-03-31 17:59:48'),(31,2,1,1,'teste mil de imagens','Redes','Cabeamento','teste de upload de imagens múltiplas','resolvido','alta','2026-03-31 18:09:42','2026-03-31 18:10:24'),(32,2,NULL,NULL,'OADASDK',NULL,NULL,'fAEFA','cancelado','media','2026-03-31 18:11:24','2026-04-01 19:01:20'),(33,2,1,1,'novo teste das imagens','Segurança','Antivírus','.','resolvido','alta','2026-03-31 18:15:56','2026-03-31 18:16:40'),(34,1,NULL,NULL,'teste de 4 imagens','Redes','Wi-Fi','4 imagens','classificado','critica','2026-03-31 18:36:44','2026-04-01 18:13:03'),(35,1,1,1,'teste chamado 3','Redes','VPN','adsd','resolvido','media','2026-04-01 17:41:32','2026-04-01 17:59:33'),(36,1,NULL,NULL,'Impressora não funciona',NULL,NULL,'Impressora não imprime','aberto','media','2026-04-06 17:23:45','2026-04-06 17:23:45'),(37,1,NULL,NULL,'TESTE',NULL,NULL,'.','aberto','media','2026-04-07 18:26:01','2026-04-07 18:26:01'),(38,2,1,1,'servidor parou de funcionar','Infraestrutura','Servidor','.','resolvido','alta','2026-04-07 18:28:23','2026-04-14 17:04:22'),(39,1,NULL,NULL,'TESTE@',NULL,NULL,'.','aberto','media','2026-04-07 19:08:10','2026-04-07 19:08:10'),(40,1,NULL,NULL,'Teste2','Infraestrutura','Banco de Dados','v','classificado','media','2026-04-07 19:12:07','2026-04-07 19:12:16'),(41,1,1,1,'TESTE3','Geral','Geral','.','resolvido','critica','2026-04-07 19:15:39','2026-04-14 17:03:59'),(42,1,NULL,NULL,'teste refactor','Engenharia','Revisão Técnica','.','classificado','baixa','2026-04-13 17:13:21','2026-04-13 17:17:04');
/*!40000 ALTER TABLE `chamados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversas`
--

DROP TABLE IF EXISTS `conversas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('privada','grupo','setor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'privada',
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `criado_por` int unsigned NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `criado_por` (`criado_por`),
  CONSTRAINT `conversas_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversas`
--

LOCK TABLES `conversas` WRITE;
/*!40000 ALTER TABLE `conversas` DISABLE KEYS */;
INSERT INTO `conversas` VALUES (1,'grupo','Geral','Grupo geral para avisos',1,'2026-03-19 19:12:02'),(2,'grupo','TI',NULL,1,'2026-03-19 19:12:02'),(3,'privada',NULL,NULL,1,'2026-03-20 17:14:21'),(5,'privada',NULL,NULL,1,'2026-03-20 17:24:22'),(7,'privada',NULL,NULL,2,'2026-03-20 17:38:58'),(8,'privada',NULL,NULL,1,'2026-03-20 19:23:53'),(11,'privada',NULL,NULL,6,'2026-03-23 17:03:46'),(12,'privada',NULL,NULL,6,'2026-03-23 17:04:08'),(16,'privada',NULL,NULL,3,'2026-03-24 19:19:39'),(17,'privada',NULL,NULL,2,'2026-03-24 19:53:40'),(18,'privada',NULL,NULL,3,'2026-03-24 20:05:56'),(19,'privada',NULL,NULL,1,'2026-03-25 19:16:05'),(23,'privada',NULL,NULL,5,'2026-03-31 16:41:07'),(25,'privada',NULL,NULL,7,'2026-03-31 16:53:18'),(26,'privada',NULL,NULL,3,'2026-03-31 16:53:58');
/*!40000 ALTER TABLE `conversas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagens`
--

DROP TABLE IF EXISTS `mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `conversa_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arquivo_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excluida_em` timestamp NULL DEFAULT NULL,
  `excluida_por` int unsigned DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_conversa_criado` (`conversa_id`,`criado_em`),
  KEY `fk_mensagens_excluida_por` (`excluida_por`),
  CONSTRAINT `fk_mensagens_excluida_por` FOREIGN KEY (`excluida_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagens`
--

LOCK TABLES `mensagens` WRITE;
/*!40000 ALTER TABLE `mensagens` DISABLE KEYS */;
INSERT INTO `mensagens` VALUES (1,1,1,'Bem-vindos ao chat interno!',NULL,NULL,NULL,NULL,'2026-03-19 19:12:02'),(2,1,1,'Este é o canal geral da empresa.',NULL,NULL,NULL,NULL,'2026-03-19 19:12:02'),(3,1,1,'Primeira mensagem via API!',NULL,NULL,NULL,NULL,'2026-03-19 19:12:28'),(4,1,1,'Testando API de envio!',NULL,NULL,NULL,NULL,'2026-03-19 19:14:01'),(5,1,1,'teste',NULL,NULL,NULL,NULL,'2026-03-19 19:23:39'),(6,1,1,'teste2',NULL,NULL,NULL,NULL,'2026-03-19 19:24:46'),(7,2,1,'ti teste',NULL,NULL,NULL,NULL,'2026-03-19 19:26:04'),(8,1,1,'teste',NULL,NULL,NULL,NULL,'2026-03-20 16:33:36'),(9,1,1,'sofia testando chat',NULL,NULL,NULL,NULL,'2026-03-20 16:39:50'),(10,1,1,'de novo sofia testando o chat',NULL,NULL,NULL,NULL,'2026-03-20 16:40:15'),(11,1,2,'sjdas',NULL,NULL,NULL,NULL,'2026-03-20 16:42:59'),(12,1,1,'chat',NULL,NULL,NULL,NULL,'2026-03-20 16:43:08'),(13,1,1,'teste',NULL,NULL,NULL,NULL,'2026-03-20 16:43:29'),(14,1,2,'não deu',NULL,NULL,NULL,NULL,'2026-03-20 16:44:06'),(15,1,1,'teste de novo',NULL,NULL,NULL,NULL,'2026-03-20 16:45:05'),(16,1,1,'tempo real?',NULL,NULL,NULL,NULL,'2026-03-20 16:46:22'),(17,1,2,'deu certo',NULL,NULL,NULL,NULL,'2026-03-20 16:46:28'),(18,2,1,'teste',NULL,NULL,NULL,NULL,'2026-03-20 16:46:59'),(19,2,2,'teste',NULL,NULL,NULL,NULL,'2026-03-20 16:47:04'),(20,2,2,'notificação?',NULL,NULL,NULL,NULL,'2026-03-20 16:47:19'),(22,3,2,'ola',NULL,NULL,NULL,NULL,'2026-03-20 17:25:40'),(23,3,2,'ola',NULL,NULL,NULL,NULL,'2026-03-20 17:28:15'),(24,1,2,'geral teste',NULL,NULL,NULL,NULL,'2026-03-20 17:28:31'),(25,1,2,'ola',NULL,NULL,NULL,NULL,'2026-03-20 17:30:37'),(26,1,2,'geral teste',NULL,NULL,NULL,NULL,'2026-03-20 17:31:58'),(27,3,2,'teste not',NULL,NULL,NULL,NULL,'2026-03-20 17:32:09'),(28,1,2,'geral teste',NULL,NULL,NULL,NULL,'2026-03-20 17:33:17'),(29,3,2,'admin',NULL,NULL,NULL,NULL,'2026-03-20 17:33:46'),(30,3,2,'teste not',NULL,NULL,NULL,NULL,'2026-03-20 17:35:58'),(31,3,2,'test',NULL,NULL,NULL,NULL,'2026-03-20 17:36:05'),(32,3,2,'oi',NULL,NULL,NULL,NULL,'2026-03-20 17:38:13'),(33,1,2,'oi',NULL,NULL,NULL,NULL,'2026-03-20 17:38:22'),(34,1,1,'oi',NULL,NULL,NULL,NULL,'2026-03-20 17:38:35'),(35,7,2,'ola',NULL,NULL,NULL,NULL,'2026-03-20 17:39:03'),(36,7,2,'oi',NULL,NULL,NULL,NULL,'2026-03-20 17:40:40'),(37,7,3,'oi sofia',NULL,NULL,NULL,NULL,'2026-03-20 17:46:07'),(38,7,3,'oi sofia de novo',NULL,NULL,NULL,NULL,'2026-03-20 17:46:37'),(39,7,3,'oi sofia',NULL,NULL,NULL,NULL,'2026-03-20 17:48:00'),(40,2,2,'oi TI',NULL,NULL,NULL,NULL,'2026-03-20 17:48:17'),(41,3,1,'sofia',NULL,NULL,NULL,NULL,'2026-03-20 18:01:17'),(42,8,1,'oi',NULL,NULL,NULL,NULL,'2026-03-20 19:23:56'),(43,8,4,'oi',NULL,NULL,NULL,NULL,'2026-03-20 19:24:18'),(44,2,4,'ti teste',NULL,NULL,NULL,NULL,'2026-03-20 19:24:27'),(45,5,1,'oi',NULL,NULL,NULL,NULL,'2026-03-20 19:25:30'),(46,5,1,'oi',NULL,NULL,NULL,NULL,'2026-03-20 19:25:57'),(47,2,2,'ola',NULL,NULL,NULL,NULL,'2026-03-20 19:50:10'),(48,5,3,'oi',NULL,NULL,NULL,NULL,'2026-03-23 16:34:18'),(49,3,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 16:34:57'),(50,3,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 16:35:32'),(51,3,1,'teste',NULL,NULL,NULL,NULL,'2026-03-23 16:54:47'),(52,3,2,'teste',NULL,NULL,NULL,NULL,'2026-03-23 16:54:52'),(53,3,2,'teste',NULL,NULL,NULL,NULL,'2026-03-23 16:54:57'),(56,2,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 16:59:56'),(58,11,6,'ola sofia',NULL,NULL,NULL,NULL,'2026-03-23 17:03:52'),(59,12,6,'ola admin',NULL,NULL,NULL,NULL,'2026-03-23 17:04:11'),(60,12,6,'ola',NULL,NULL,NULL,NULL,'2026-03-23 17:04:20'),(61,12,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 17:07:11'),(62,12,6,'oi',NULL,NULL,NULL,NULL,'2026-03-23 17:07:36'),(63,12,1,'olá',NULL,NULL,NULL,NULL,'2026-03-23 17:07:43'),(64,12,1,'teste',NULL,NULL,NULL,NULL,'2026-03-23 17:14:32'),(67,12,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 17:16:20'),(72,12,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 17:19:29'),(76,12,1,'oi',NULL,NULL,NULL,NULL,'2026-03-23 17:20:45'),(77,3,1,'.',NULL,NULL,NULL,NULL,'2026-03-23 18:40:00'),(78,3,2,'.',NULL,NULL,NULL,NULL,'2026-03-23 18:40:06'),(79,3,1,'.',NULL,NULL,NULL,NULL,'2026-03-23 19:25:36'),(80,3,2,'.',NULL,NULL,NULL,NULL,'2026-03-23 19:25:47'),(81,5,3,'Chamado #6 (\"teste emergência\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-24 19:18:06'),(82,5,3,'Chamado #3 (\"teste2\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-24 19:31:01'),(83,17,2,'oi',NULL,NULL,NULL,NULL,'2026-03-24 19:53:43'),(84,18,3,'Chamado #15 (\"Teste 3 imagem\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 16:44:10'),(85,7,3,'Chamado #17 (\"Servidor parou\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 16:54:41'),(86,7,3,'oi',NULL,NULL,NULL,NULL,'2026-03-25 16:56:55'),(87,7,3,'oi',NULL,NULL,NULL,NULL,'2026-03-25 16:57:11'),(88,7,3,'Chamado #18 (\"Servidor parou\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 16:58:43'),(89,7,2,'ok',NULL,NULL,NULL,NULL,'2026-03-25 16:59:03'),(90,5,3,'oi',NULL,NULL,NULL,NULL,'2026-03-25 18:51:34'),(91,5,1,'oi',NULL,NULL,NULL,NULL,'2026-03-25 18:51:47'),(94,3,1,'Chamado #16 (\"Ar condicionado não esfria\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 19:07:38'),(98,8,1,'oi',NULL,NULL,NULL,NULL,'2026-03-25 19:15:44'),(99,19,1,'oi teste',NULL,NULL,NULL,NULL,'2026-03-25 19:16:08'),(100,19,5,'oi',NULL,NULL,NULL,NULL,'2026-03-25 19:16:22'),(101,3,1,'Chamado #21 (\"Teste notificação\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 19:35:53'),(102,3,2,'',NULL,NULL,'2026-03-25 19:37:20',2,'2026-03-25 19:36:25'),(103,3,1,'olha','chat-mensagens/3/de0e782f7e23ccc939e3d34182c66fd7.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-03-25 19:37:00'),(104,3,2,'',NULL,NULL,'2026-03-25 19:41:06',2,'2026-03-25 19:38:43'),(105,3,2,'notificação?',NULL,NULL,NULL,NULL,'2026-03-25 19:41:50'),(106,3,2,'.',NULL,NULL,NULL,NULL,'2026-03-25 19:43:11'),(107,3,1,'sofia emoji?👍👍',NULL,NULL,NULL,NULL,'2026-03-25 19:48:36'),(108,3,2,'',NULL,NULL,'2026-03-25 19:49:03',2,'2026-03-25 19:48:55'),(109,3,2,'🎉',NULL,NULL,NULL,NULL,'2026-03-25 19:48:58'),(110,3,1,'',NULL,NULL,'2026-03-25 19:55:02',1,'2026-03-25 19:54:53'),(111,3,1,'.',NULL,NULL,NULL,NULL,'2026-03-25 19:55:17'),(112,3,2,'',NULL,NULL,'2026-03-25 19:55:27',2,'2026-03-25 19:55:22'),(113,3,1,'Chamado #23 (\"Teste de notificação\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 19:56:39'),(114,3,1,'',NULL,NULL,'2026-03-25 19:59:48',1,'2026-03-25 19:59:44'),(115,3,2,'',NULL,NULL,'2026-03-25 20:00:07',2,'2026-03-25 20:00:04'),(116,3,2,'✌️',NULL,NULL,NULL,NULL,'2026-03-25 20:00:26'),(117,3,1,'Chamado #9 (\"Wi-FI caiu\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 20:01:02'),(118,3,2,'',NULL,NULL,'2026-03-25 20:07:48',2,'2026-03-25 20:07:44'),(119,3,1,'Chamado #24 (\"ultimo teste de notificação\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-25 20:08:18'),(120,3,1,'Chamado #22 (\"Novo teste notificação\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-30 17:37:59'),(121,12,1,'Chamado #12 (\"Cabo de rede não funciona\") foi finalizado pela equipe de TI.',NULL,NULL,NULL,NULL,'2026-03-30 17:39:25'),(122,12,1,'oi',NULL,NULL,NULL,NULL,'2026-03-30 17:39:50'),(123,19,1,'oi',NULL,NULL,NULL,NULL,'2026-03-30 17:57:04'),(125,19,1,'','chat-mensagens/19/c9f4ffa8859b10a9b716326c438ad534.png','Captura de tela 2025-11-13 152627.png',NULL,NULL,'2026-03-30 17:58:43'),(126,19,1,'','chat-mensagens/19/5bf8f2f025a985559124aa1459c11157.png','Captura de tela 2025-11-13 153152.png',NULL,NULL,'2026-03-30 17:58:43'),(127,19,1,'','chat-mensagens/19/33113a618fd22858e55cc44f17e6a34e.pdf','EstudodeCaso_aluno_pdf.pdf',NULL,NULL,'2026-03-30 17:59:05'),(128,12,1,'Chamado #11 (\"Computador não liga\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-30 18:05:25'),(130,3,1,'Chamado #19 (\"Servidor caiu\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-30 19:02:11'),(131,8,1,'Chamado #14 (\"Teste 2 de Imagem\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-30 19:29:15'),(133,8,1,'Chamado #25 (\"Teste usuário que finaliza chamado\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-30 19:42:21'),(134,23,5,'Chamado #26 (\"Teste de notificação\") foi finalizado por teste.',NULL,NULL,NULL,NULL,'2026-03-31 16:41:07'),(135,3,2,'teste notificação',NULL,NULL,NULL,NULL,'2026-03-31 16:46:27'),(136,3,2,'',NULL,NULL,'2026-03-31 16:49:36',2,'2026-03-31 16:49:33'),(137,3,2,'','chat-mensagens/3/d7504c6e2f7b69b9ee361512a44feb8e.png','Captura de tela 2025-11-12 150650.png',NULL,NULL,'2026-03-31 16:49:57'),(138,25,7,'oi',NULL,NULL,NULL,NULL,'2026-03-31 16:53:21'),(139,26,3,'oi',NULL,NULL,NULL,NULL,'2026-03-31 16:54:01'),(140,25,1,'oi',NULL,NULL,NULL,NULL,'2026-03-31 17:11:50'),(141,25,7,'oi',NULL,NULL,NULL,NULL,'2026-03-31 17:11:57'),(142,25,7,'','chat-mensagens/25/421a12bd0377fd31fd6968a95aeaf897.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-03-31 17:12:03'),(143,19,5,'Chamado #27 (\"teste refatoração\") foi finalizado por teste.',NULL,NULL,NULL,NULL,'2026-03-31 17:13:13'),(144,19,1,'','chat-mensagens/19/a81c1b310e968799c6acafb5c384d4da.png','Captura de tela 2025-11-13 153152.png',NULL,NULL,'2026-03-31 17:27:10'),(145,19,1,'oi',NULL,NULL,NULL,NULL,'2026-03-31 17:27:23'),(146,19,5,'oi',NULL,NULL,NULL,NULL,'2026-03-31 17:57:23'),(147,19,5,'oi',NULL,NULL,NULL,NULL,'2026-03-31 17:57:30'),(148,3,1,'Chamado #29 (\"Impressora não funciona\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-31 17:58:59'),(149,3,1,'Chamado #30 (\"Impressora não funciona 2\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-31 17:59:48'),(150,3,1,'','chat-mensagens/3/1420d649aee3cb3df256f2b003935ae6.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-03-31 18:01:54'),(151,3,1,'','chat-mensagens/3/af00432a45e3b0a137e02790e1c203dc.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-03-31 18:02:04'),(152,3,1,'','chat-mensagens/3/55d5365ae013a93eddae00d33ed24331.png','Captura de tela 2025-11-12 150650.png',NULL,NULL,'2026-03-31 18:02:04'),(153,3,1,'.',NULL,NULL,NULL,NULL,'2026-03-31 18:02:29'),(156,3,1,'Chamado #31 (\"teste mil de imagens\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-31 18:10:24'),(157,3,1,'Chamado #33 (\"novo teste das imagens\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-03-31 18:16:40'),(158,3,1,'oi',NULL,NULL,NULL,NULL,'2026-03-31 18:17:14'),(159,3,2,'oi',NULL,NULL,NULL,NULL,'2026-03-31 18:17:27'),(160,3,1,'**Sofia**',NULL,NULL,NULL,NULL,'2026-03-31 19:13:19'),(161,3,1,'**Sofia**',NULL,NULL,NULL,NULL,'2026-03-31 19:13:26'),(162,3,1,'/Sofia/',NULL,NULL,NULL,NULL,'2026-03-31 19:13:32'),(163,3,1,'**Sofia**',NULL,NULL,NULL,NULL,'2026-03-31 19:13:40'),(164,3,1,'*Sofia*',NULL,NULL,NULL,NULL,'2026-03-31 19:13:50'),(165,3,1,'.',NULL,NULL,NULL,NULL,'2026-03-31 19:23:18'),(166,3,1,'oi',NULL,NULL,NULL,NULL,'2026-04-02 16:40:02'),(167,3,2,'',NULL,NULL,'2026-04-02 16:40:49',2,'2026-04-02 16:40:05'),(168,3,2,'','chat-mensagens/3/a80c9b6d1d514da1dc47a068e7ac984c.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-04-02 16:40:26'),(169,1,1,'','chat-mensagens/1/0bd8ada05d8e4beabe4b5c1b096ad0f9.pdf','EstudodeCaso_Matemática_Sofia.pdf',NULL,NULL,'2026-04-02 18:20:16'),(170,1,1,'','chat-mensagens/1/e60e439a506a0e18e3737c28bcc01a3b.png','Captura de tela 2025-11-12 145607.png',NULL,NULL,'2026-04-02 18:20:26'),(171,1,1,'','chat-mensagens/1/2f578eeff5c6fd5d656e98d4159e6b26.png','Captura de tela 2025-11-12 150650.png',NULL,NULL,'2026-04-02 18:20:26'),(172,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-02 18:31:56'),(173,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-02 18:34:02'),(174,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-02 18:35:20'),(175,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-02 18:35:25'),(176,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-02 18:35:35'),(177,3,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:35:38'),(178,1,2,'geral',NULL,NULL,NULL,NULL,'2026-04-02 18:37:01'),(179,1,2,'geral',NULL,NULL,NULL,NULL,'2026-04-02 18:37:15'),(180,1,2,'geral',NULL,NULL,NULL,NULL,'2026-04-02 18:37:22'),(181,3,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:37:28'),(182,23,2,'teste',NULL,NULL,NULL,NULL,'2026-04-02 18:37:35'),(183,2,2,'ti',NULL,NULL,NULL,NULL,'2026-04-02 18:37:48'),(184,2,2,'.',NULL,NULL,NULL,NULL,'2026-04-02 18:38:53'),(185,1,2,'.',NULL,NULL,NULL,NULL,'2026-04-02 18:38:59'),(186,1,2,'geral',NULL,NULL,NULL,NULL,'2026-04-02 18:39:15'),(187,3,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:39:24'),(188,23,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:39:43'),(189,23,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:39:48'),(190,1,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:39:52'),(191,2,2,'ti',NULL,NULL,NULL,NULL,'2026-04-02 18:39:55'),(192,1,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:45:16'),(193,2,2,'ti',NULL,NULL,NULL,NULL,'2026-04-02 18:45:20'),(194,3,2,'admin',NULL,NULL,NULL,NULL,'2026-04-02 18:45:24'),(195,3,2,'o',NULL,NULL,NULL,NULL,'2026-04-06 17:11:28'),(196,3,1,'oi',NULL,NULL,NULL,NULL,'2026-04-06 17:11:32'),(197,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-06 17:11:53'),(198,3,2,'.',NULL,NULL,NULL,NULL,'2026-04-06 17:11:58'),(199,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-06 17:12:15'),(200,3,1,'.',NULL,NULL,NULL,NULL,'2026-04-06 17:12:24'),(201,3,2,'oi',NULL,NULL,NULL,NULL,'2026-04-06 17:12:29'),(202,2,2,'oi',NULL,NULL,NULL,NULL,'2026-04-07 18:03:34'),(203,2,2,'oi',NULL,NULL,NULL,NULL,'2026-04-07 18:03:41'),(204,2,1,'oi',NULL,NULL,NULL,NULL,'2026-04-07 18:03:45'),(205,2,1,'oi',NULL,NULL,NULL,NULL,'2026-04-07 18:05:23'),(206,8,1,'','chat-mensagens/8/06da8f480b4e88b1e56dde58096e8446.txt','notas.txt',NULL,NULL,'2026-04-07 18:35:00'),(207,8,1,'.',NULL,NULL,NULL,NULL,'2026-04-13 16:42:11'),(208,25,1,'.',NULL,NULL,NULL,NULL,'2026-04-13 16:42:17'),(209,3,1,'Chamado #38 (\"servidor parou de funcionar\") foi finalizado por Administrador.',NULL,NULL,NULL,NULL,'2026-04-14 17:04:22');
/*!40000 ALTER TABLE `mensagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participantes`
--

DROP TABLE IF EXISTS `participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participantes` (
  `conversa_id` int unsigned NOT NULL,
  `usuario_id` int unsigned NOT NULL,
  `entrou_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultima_leitura` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`conversa_id`,`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `participantes_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `conversas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participantes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participantes`
--

LOCK TABLES `participantes` WRITE;
/*!40000 ALTER TABLE `participantes` DISABLE KEYS */;
INSERT INTO `participantes` VALUES (1,1,'2026-03-19 19:12:02','2026-04-09 16:45:27'),(1,2,'2026-03-20 16:41:32','2026-04-14 17:05:04'),(1,3,'2026-03-20 19:27:09','2026-03-25 16:40:54'),(1,4,'2026-03-20 19:27:10','2026-03-20 19:30:38'),(1,5,'2026-03-24 20:18:26','2026-03-25 19:24:31'),(1,6,'2026-03-24 20:18:26','2026-03-24 20:19:00'),(2,1,'2026-03-19 19:12:02','2026-04-13 18:46:13'),(2,2,'2026-03-20 16:41:32','2026-04-07 18:05:23'),(2,3,'2026-03-20 19:22:27','2026-03-24 18:51:00'),(2,4,'2026-03-20 19:22:29','2026-03-30 19:29:10'),(3,1,'2026-03-20 17:14:21','2026-04-14 17:03:50'),(3,2,'2026-03-20 17:14:21','2026-04-14 17:04:52'),(5,1,'2026-03-20 17:24:22','2026-04-02 18:37:19'),(5,3,'2026-03-20 17:24:22','2026-04-01 19:19:02'),(7,2,'2026-03-20 17:38:58','2026-04-02 18:33:54'),(7,3,'2026-03-20 17:38:58','2026-04-01 19:19:22'),(8,1,'2026-03-20 19:23:53','2026-04-13 17:23:35'),(8,4,'2026-03-20 19:23:53','2026-04-01 18:37:41'),(11,2,'2026-03-23 17:03:46','2026-03-25 20:23:24'),(11,6,'2026-03-23 17:03:46','2026-03-30 17:42:27'),(12,1,'2026-03-23 17:04:08','2026-04-13 16:42:18'),(12,6,'2026-03-23 17:04:08','2026-03-30 18:05:35'),(16,3,'2026-03-24 19:19:39','2026-03-24 20:00:06'),(16,6,'2026-03-24 19:19:39','2026-03-30 17:39:40'),(17,2,'2026-03-24 19:53:40','2026-03-24 19:53:43'),(17,4,'2026-03-24 19:53:40','2026-03-30 19:29:09'),(18,3,'2026-03-24 20:05:56','2026-04-01 19:19:23'),(18,4,'2026-03-24 20:05:56','2026-03-30 19:33:48'),(19,1,'2026-03-25 19:16:05','2026-04-07 18:25:49'),(19,5,'2026-03-25 19:16:05','2026-03-31 17:57:30'),(23,2,'2026-03-31 16:41:07','2026-04-02 18:39:48'),(23,5,'2026-03-31 16:41:07','2026-03-31 17:13:01'),(25,1,'2026-03-31 16:53:18','2026-04-13 16:42:17'),(25,7,'2026-03-31 16:53:18','2026-03-31 17:11:57'),(26,3,'2026-03-31 16:53:58','2026-04-01 19:19:55'),(26,7,'2026-03-31 16:53:58','2026-03-31 16:54:07');
/*!40000 ALTER TABLE `participantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setores`
--

DROP TABLE IF EXISTS `setores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setores`
--

LOCK TABLES `setores` WRITE;
/*!40000 ALTER TABLE `setores` DISABLE KEYS */;
INSERT INTO `setores` VALUES (1,'TI',NULL,'2026-03-19 18:36:26'),(2,'Administrativo',NULL,'2026-03-19 18:36:26'),(3,'Operacional',NULL,'2026-03-19 18:36:26'),(4,'Financeiro','Setor financeiro','2026-03-20 17:08:33'),(5,'Vendas','','2026-03-20 17:10:08'),(7,'teste','.','2026-03-23 17:21:22');
/*!40000 ALTER TABLE `setores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_presenca`
--

DROP TABLE IF EXISTS `user_presenca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_presenca` (
  `usuario_id` int unsigned NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `last_seen` timestamp NULL DEFAULT NULL,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_id`),
  CONSTRAINT `fk_user_presenca_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_presenca`
--

LOCK TABLES `user_presenca` WRITE;
/*!40000 ALTER TABLE `user_presenca` DISABLE KEYS */;
INSERT INTO `user_presenca` VALUES (1,0,'2026-04-14 17:03:54','2026-04-14 17:03:54'),(2,1,'2026-04-14 17:04:47','2026-04-14 17:04:47'),(3,0,'2026-04-01 19:20:01','2026-04-01 19:20:01'),(4,0,'2026-04-01 19:01:00','2026-04-01 19:01:00'),(5,0,'2026-03-31 17:57:44','2026-03-31 17:57:44'),(6,0,'2026-03-30 19:01:58','2026-03-30 19:01:58'),(7,0,'2026-03-31 17:12:54','2026-03-31 17:12:54');
/*!40000 ALTER TABLE `user_presenca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setor_id` int unsigned DEFAULT NULL,
  `papel` enum('admin','ti','usuario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `setor_id` (`setor_id`),
  KEY `idx_email` (`email`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@empresa.com','$2y$12$RrB5AeiJTZ8.KiiMCbRfduaqTYADU1gBwjmcnqL2.oseznpO1.X6C',2,'admin',1,'2026-03-19 18:36:26'),(2,'Sofia','sofia@empresa.com','$2y$10$2yKO3rZ3GDOkdmi1sSvIt.u90VClcYr6bggbY45.ZwT6Fj9R9HsMW',2,'usuario',1,'2026-03-20 16:41:32'),(3,'João Silva','joao@empresa.com','$2y$12$3rx0pANyJbcAfnfkb9.6cOO5zHIf2SBmDNDX4RQiOkszfWCGZ5gBi',1,'ti',1,'2026-03-20 17:08:39'),(4,'User','user@empresa.com','$2y$12$NnZqK1d7Ytsci67eqGOjX.8NU0sgOQisa/Nuk6ak8pjXd5j4TNzbu',2,'admin',1,'2026-03-20 18:36:56'),(5,'teste','teste@gmail.com','$2y$12$uUViJPQkAWGzA6HGkSBvkONOJJf4Pp9/y0.H8OHU/wCLaY30MKgEa',7,'ti',1,'2026-03-23 16:56:05'),(6,'Laisa Financeiro','laisa@empresa.com','$2y$12$rdH6xpwgPfcaftDrrdt8jOSMTJkWAXt8gZE5otonj.parHEW8SCki',4,'usuario',1,'2026-03-23 17:01:52'),(7,'Maria Silva','maria@empresa.com','$2y$12$.LWVnf6m9fOS7lwOZHBbSeyIOgFniHClp.tU0y9XWLdBQqx.HMj9.',3,'usuario',1,'2026-03-31 16:52:46'),(8,'Paginação de Usuários','paginacao@empresa.com','$2y$12$dw4NYvvTA39IuXdbPazHcugTaKmJiRaN0PvKPo3RlasRM0RhgNaBu',2,'usuario',1,'2026-04-01 18:31:46'),(9,'9User','9user@empresa.com','$2y$12$EVm8d/3ym9w6ipH9eX/PpeynMAPtSePk02346MgCk92G4HNyvIpzO',3,'usuario',1,'2026-04-01 18:33:15');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'chat_db'
--

--
-- Dumping routines for database 'chat_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-14 17:54:52
