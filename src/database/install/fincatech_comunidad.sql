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
-- Table structure for table `comunidad`
--

DROP TABLE IF EXISTS `comunidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comunidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localidad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idlocalidad` int(11) DEFAULT NULL,
  `idprovincia` int(11) DEFAULT NULL,
  `codpostal` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presidente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailcontacto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cif` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) NOT NULL,
  `userupdate` int(11) DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuarioId` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`,`usuarioId`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comunidad`
--

LOCK TABLES `comunidad` WRITE;
/*!40000 ALTER TABLE `comunidad` DISABLE KEYS */;
INSERT INTO `comunidad` VALUES (1,'001','Comunidad Test 001','Calle Terraza','1',1,1,'29680','Pepito','123456789','aa@a.com','1','2021-06-12 15:14:44',NULL,1,NULL,'A',1),(2,'002','Comunidad Test 002','Calle Terraza','1',1,1,'29680','Pepito','123456789','aa@a.com','1','2021-06-12 15:15:11',NULL,1,NULL,'A',2),(3,'003','Comunidad Test 003','Calle Terraza','1',1,1,'29680','Pepito','123456789','aa@a.com','1','2021-06-12 15:15:13',NULL,1,NULL,'A',1),(18,'004','Comunidad Test 004','Calle Terraza','1',1,1,'29680','Pepito','123456789','aa@a.com','1','2021-08-17 12:13:33',NULL,1,NULL,'A',1),(20,'020','Comunidad de Propietarios El Jabeque Estepona','Calle Modificada','1',1,29,'29680','Oscar Rodríguez Rodrigo','684086020','cp@eljabeque.com','1R','2021-08-17 12:13:35',NULL,1,NULL,'A',1),(22,'004','Comunidad Test 004','Calle Terraza','1',1,1,'29680','Pepito','123456789','aa@a.com','1','2021-08-17 12:13:36',NULL,1,NULL,'A',1);
/*!40000 ALTER TABLE `comunidad` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-06 22:41:11
