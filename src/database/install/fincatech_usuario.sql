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
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cif` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localidad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idlocalidad` int(11) DEFAULT NULL,
  `idprovincia` int(11) DEFAULT NULL,
  `codpostal` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emailcontacto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rolId` int(11) NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Oscar Super Admin','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','oscar@happysoftware.es',1,'',NULL,'A','2021-06-06 12:34:10',NULL,0),(2,'Admin 001','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','administrador@fincatech.es',2,'',NULL,'A','2021-06-06 12:36:26',NULL,1),(3,'Administrador de fincas 001','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'A','2021-06-06 12:36:26',NULL,1),(4,'Administrador de fincas 004','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'A','2021-06-06 12:36:26',NULL,1),(5,'Administrador de fincas 005','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'A','2021-06-06 12:36:26',NULL,1),(6,'Administrador de fincas 006','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'A','2021-06-06 12:36:26',NULL,1),(7,'Administrador de fincas 007','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'A','2021-06-06 12:36:26',NULL,1),(8,'Administrador de fincas 008','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'H','2021-06-06 12:36:26',NULL,1),(9,'Administrador de fincas 009','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'H','2021-06-06 12:36:26',NULL,1),(11,'Administrador de fincas 0011','1R','Calle Terraza, 172 - 1º A3','Estepona',NULL,29,'29680','684086020','adminfinca@happysoftware.es',5,' ',NULL,'H','2021-06-06 12:36:26',NULL,1),(12,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:10',NULL,1),(13,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:12',NULL,1),(14,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:13',NULL,1),(15,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:13',NULL,1),(16,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:14',NULL,1),(17,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:14',NULL,1),(18,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:14',NULL,1),(19,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:15',NULL,1),(20,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','H','2021-08-11 13:59:15',NULL,1),(21,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:15',NULL,1),(22,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:15',NULL,1),(23,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:15',NULL,1),(24,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:15',NULL,1),(25,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-11 13:59:16',NULL,1),(26,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-17 11:22:15',NULL,1),(28,'oscar rodríguez rodrigo','1R','Calle Terraza, 172 - 1ºA3','Estepona',1,29,'29680','684086020','hola@happysoftware.es',5,'','','A','2021-08-17 11:22:30',NULL,1),(29,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-17 11:22:30',NULL,1),(30,'Usuario','1R','Calle Terraza, 172','Estepona',1,29,'29680','','oscar@happysoftware.es',5,'','','A','2021-08-17 11:22:31',NULL,1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-06 22:41:12
