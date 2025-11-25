-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: banco
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `licenca`
--

DROP TABLE IF EXISTS `licenca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `licenca` (
  `id_licenca` int NOT NULL,
  `fk_Usuario_id_usuario` int DEFAULT NULL,
  `fk_Caixa_id_caixa` int NOT NULL,
  `tipo_licenca` int DEFAULT NULL,
  `fk_Jogo_id_jogo` int NOT NULL,
  PRIMARY KEY (`id_licenca`,`fk_Jogo_id_jogo`,`fk_Caixa_id_caixa`),
  UNIQUE KEY `fk_Caixa_id_caixa` (`fk_Caixa_id_caixa`,`id_licenca`,`fk_Jogo_id_jogo`),
  KEY `FK_Licenca_2` (`fk_Usuario_id_usuario`),
  KEY `FK_Licenca_5` (`fk_Jogo_id_jogo`),
  CONSTRAINT `FK_Licenca_2` FOREIGN KEY (`fk_Usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `FK_Licenca_3` FOREIGN KEY (`fk_Caixa_id_caixa`) REFERENCES `caixa` (`id_caixa`) ON DELETE CASCADE,
  CONSTRAINT `FK_Licenca_5` FOREIGN KEY (`fk_Jogo_id_jogo`) REFERENCES `jogo` (`id_jogo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `licenca`
--

LOCK TABLES `licenca` WRITE;
/*!40000 ALTER TABLE `licenca` DISABLE KEYS */;
/*!40000 ALTER TABLE `licenca` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-25 18:42:23
