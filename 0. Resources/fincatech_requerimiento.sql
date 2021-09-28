-- MySQL dump 10.13  Distrib 8.0.22, for macos10.15 (x86_64)
--
-- Host: localhost    Database: fincatech
-- ------------------------------------------------------
-- Server version	5.7.34

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
-- Dumping data for table `requerimiento`
--

LOCK TABLES `requerimiento` WRITE;
/*!40000 ALTER TABLE `requerimiento` DISABLE KEYS */;
INSERT INTO `requerimiento` VALUES (1,'Operatoria y aceptación de condiciones',22,0,NULL,NULL,'COM',1,6,NULL,NULL,'2021-01-01 00:00:00',NULL,NULL),(2,'Medidas de emergencia',NULL,0,NULL,NULL,'COM',1,6,NULL,NULL,'2021-01-01 00:00:00',NULL,NULL),(3,'Planificación de la actividad preventiva',NULL,0,NULL,NULL,'COM',1,6,NULL,NULL,'2021-01-01 00:00:00',NULL,NULL),(4,'Evaluación de riesgos',23,0,NULL,NULL,'COM',1,6,NULL,NULL,'2021-01-01 00:00:00',NULL,NULL),(5,'Plan de prevención',NULL,0,NULL,NULL,'COMEM',1,6,NULL,NULL,'2021-01-01 00:00:00',NULL,NULL),(6,'Justificante entrega documentación al trabajador',NULL,0,0,1,NULL,1,5,-1,NULL,'2021-09-26 22:39:29',NULL,3),(7,'Justificante entrega de EPIs',NULL,0,0,0,NULL,1,5,-1,NULL,'2021-09-26 23:30:01',NULL,3),(8,'Certificado de aptitud medica',NULL,0,1,0,NULL,1,5,-1,NULL,'2021-09-26 23:30:24',NULL,3),(9,'Certificado formación riesgos del puesto de trabajo',NULL,0,1,0,NULL,1,5,-1,NULL,'2021-09-26 23:31:11',NULL,3),(10,'Medidas de emergencia - Empresa contratista',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:32:28',NULL,3),(11,'Planificación de la actividad preventiva - Empresa contratista',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:32:55',NULL,3),(12,'Evaluación de riesgos - Empresa contratista',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:33:31',NULL,3),(13,'Certificado de estar al corriente con la seguridad social',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:33:49',NULL,3),(14,'Operatoria y aceptación de condiciones',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:34:03',NULL,3),(15,'Certificado de estar al corriente con la hacienda pública',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:34:20',NULL,3),(16,'Plan de prevención - Empresa contratista',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:34:41',NULL,3),(17,'RNT - Relación nominal de trabajadores, antiguo TC2',NULL,0,1,0,NULL,1,4,-1,NULL,'2021-09-26 23:34:57',NULL,3);
/*!40000 ALTER TABLE `requerimiento` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-27  9:42:56
