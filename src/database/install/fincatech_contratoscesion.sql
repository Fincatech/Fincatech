CREATE DATABASE  IF NOT EXISTS `fincatech` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci */;
USE `fincatech`;
-- MySQL dump 10.13  Distrib 8.0.21, for macos10.15 (x86_64)
--
-- Host: localhost    Database: fincatech
-- ------------------------------------------------------
-- Server version	5.7.26

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
-- Table structure for table `contratoscesion`
--

DROP TABLE IF EXISTS `contratoscesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratoscesion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `idcomunidad` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratoscesion`
--

LOCK TABLES `contratoscesion` WRITE;
/*!40000 ALTER TABLE `contratoscesion` DISABLE KEYS */;
INSERT INTO `contratoscesion` VALUES (1,'Contrato 003','Contrato modificado',-1,1,'2021-08-19 15:59:28','A',NULL,NULL),(3,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:39','A',NULL,NULL),(4,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:39','A',NULL,NULL),(5,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:39','A',NULL,NULL),(6,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:39','A',NULL,NULL),(7,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:39','A',NULL,NULL),(8,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(9,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(10,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(11,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(12,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(13,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-19 16:00:40','A',NULL,NULL),(14,'Contrato de cesión','Contrato de cesión de datos para pepito fulanito',-1,1,'2021-08-20 11:37:13','A',NULL,NULL);
/*!40000 ALTER TABLE `contratoscesion` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-06 22:41:13
