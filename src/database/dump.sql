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
-- Table structure for table `autonomo`
--

DROP TABLE IF EXISTS `autonomo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autonomo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `tipodocumento` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `numerodocumento` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codigopostal` varchar(5) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idlocalidad` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `idempresa` int(11) DEFAULT NULL,
  `idtipopuestoempleado` int(11) DEFAULT NULL,
  `idavatar` int(11) DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autonomo`
--

LOCK TABLES `autonomo` WRITE;
/*!40000 ALTER TABLE `autonomo` DISABLE KEYS */;
/*!40000 ALTER TABLE `autonomo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `camarasseguridad`
--

DROP TABLE IF EXISTS `camarasseguridad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `camarasseguridad` (
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `camarasseguridad`
--

LOCK TABLES `camarasseguridad` WRITE;
/*!40000 ALTER TABLE `camarasseguridad` DISABLE KEYS */;
INSERT INTO `camarasseguridad` VALUES (1,'Cámara 003','Cámara secundaria de garaje principal',-1,1,'2021-08-19 15:41:22','A',NULL,NULL),(3,'Cámara 002','Cámara secundaria de garaje principal',-1,1,'2021-08-20 11:36:48','A',NULL,NULL);
/*!40000 ALTER TABLE `camarasseguridad` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `comunidadrequerimientos`
--

DROP TABLE IF EXISTS `comunidadrequerimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comunidadrequerimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomunidad` int(11) NOT NULL,
  `idrequerimiento` int(11) NOT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `idestado` int(11) NOT NULL,
  `fechasubida` datetime DEFAULT NULL,
  `fechadescarga` datetime DEFAULT NULL,
  `fechacaducidad` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_COMREQ_ESTADO_idx` (`idestado`),
  CONSTRAINT `FK_COMREQ_ESTADO` FOREIGN KEY (`idestado`) REFERENCES `documentoestado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comunidadrequerimientos`
--

LOCK TABLES `comunidadrequerimientos` WRITE;
/*!40000 ALTER TABLE `comunidadrequerimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `comunidadrequerimientos` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `documentoestado`
--

DROP TABLE IF EXISTS `documentoestado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentoestado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentoestado`
--

LOCK TABLES `documentoestado` WRITE;
/*!40000 ALTER TABLE `documentoestado` DISABLE KEYS */;
INSERT INTO `documentoestado` VALUES (1,'No adjuntado'),(2,'No descargado'),(3,'No verificado'),(4,'Adjuntado'),(5,'Descargado'),(6,'Verificado'),(7,'Rechazado');
/*!40000 ALTER TABLE `documentoestado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado`
--

DROP TABLE IF EXISTS `empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `tipodocumento` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `numerodocumento` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codigopostal` varchar(5) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idlocalidad` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `idtipopuestoempleado` int(11) DEFAULT NULL,
  `idavatar` int(11) DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado`
--

LOCK TABLES `empleado` WRITE;
/*!40000 ALTER TABLE `empleado` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleadocomunidad`
--

DROP TABLE IF EXISTS `empleadocomunidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleadocomunidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idempleado` int(11) NOT NULL,
  `idcomunidad` int(11) DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `fechaalta` date DEFAULT NULL,
  `fechabaja` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleadocomunidad`
--

LOCK TABLES `empleadocomunidad` WRITE;
/*!40000 ALTER TABLE `empleadocomunidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleadocomunidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleadoempresa`
--

DROP TABLE IF EXISTS `empleadoempresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleadoempresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idempleado` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idcomunidad` int(11) DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `fechaalta` date DEFAULT NULL,
  `fechabaja` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EMPLEMPR_EMPRESA_idx` (`idempresa`),
  KEY `FK_EMPLEMPR_COMUNIDAD_idx` (`idcomunidad`),
  KEY `FK_EMPLEMPR_EMPLEADO_idx` (`idempleado`),
  CONSTRAINT `FK_EMPLEMPR_COMUNIDAD` FOREIGN KEY (`idcomunidad`) REFERENCES `comunidad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_EMPLEMPR_EMPLEADO` FOREIGN KEY (`idempleado`) REFERENCES `empleado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_EMPLEMPR_EMPRESA` FOREIGN KEY (`idempresa`) REFERENCES `empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleadoempresa`
--

LOCK TABLES `empleadoempresa` WRITE;
/*!40000 ALTER TABLE `empleadoempresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleadoempresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleadorequerimiento`
--

DROP TABLE IF EXISTS `empleadorequerimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleadorequerimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idempresa` int(11) DEFAULT NULL,
  `idempleado` int(11) DEFAULT NULL,
  `fechasubida` datetime DEFAULT NULL,
  `fechadescarga` datetime DEFAULT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EMPLREQ_EMPLEADO_idx` (`idempleado`),
  CONSTRAINT `FK_EMPLREQ_EMPLEADO` FOREIGN KEY (`idempleado`) REFERENCES `empleado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleadorequerimiento`
--

LOCK TABLES `empleadorequerimiento` WRITE;
/*!40000 ALTER TABLE `empleadorequerimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleadorequerimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razonsocial` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `cif` varchar(20) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `personacontacto` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idlocalidad` int(11) DEFAULT NULL,
  `codigopostal` varchar(5) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresarequerimiento`
--

DROP TABLE IF EXISTS `empresarequerimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresarequerimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomunidad` int(11) DEFAULT NULL,
  `idrequerimiento` int(11) DEFAULT NULL,
  `idempresa` int(11) NOT NULL,
  `idestado` int(11) DEFAULT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EMPREQ_ESTADO_idx` (`idestado`),
  KEY `FK_EMPREQ_EMPRESA_idx` (`idempresa`),
  KEY `FK_EMPREQ_REQUISITO_idx` (`idrequerimiento`),
  CONSTRAINT `FK_EMPREQ_EMPRESA` FOREIGN KEY (`idempresa`) REFERENCES `empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_EMPREQ_ESTADO` FOREIGN KEY (`idestado`) REFERENCES `documentoestado` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_EMPREQ_REQUISITO` FOREIGN KEY (`idrequerimiento`) REFERENCES `requerimientos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresarequerimiento`
--

LOCK TABLES `empresarequerimiento` WRITE;
/*!40000 ALTER TABLE `empresarequerimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresarequerimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ficheroscomunes`
--

DROP TABLE IF EXISTS `ficheroscomunes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ficheroscomunes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `ubicacion` text COLLATE utf8mb4_spanish2_ci,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ficheroscomunes`
--

LOCK TABLES `ficheroscomunes` WRITE;
/*!40000 ALTER TABLE `ficheroscomunes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ficheroscomunes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informevaloracionseguimiento`
--

DROP TABLE IF EXISTS `informevaloracionseguimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `informevaloracionseguimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informevaloracionseguimiento`
--

LOCK TABLES `informevaloracionseguimiento` WRITE;
/*!40000 ALTER TABLE `informevaloracionseguimiento` DISABLE KEYS */;
INSERT INTO `informevaloracionseguimiento` VALUES (1,'Informe seguimiento modificada','2021-08-21 00:00:00',-1,'2021-08-19 15:06:55','A'),(2,'Nota informativa','2021-08-21 00:00:00',-1,'2021-08-19 15:06:55','A'),(4,'Nota informativa','2021-08-21 00:00:00',-1,'2021-08-19 15:06:56','A'),(5,'Nota informativa','2021-08-21 00:00:00',-1,'2021-08-19 15:06:57','A'),(6,'Nota informativa','2021-08-21 00:00:00',-1,'2021-08-19 15:06:57','A');
/*!40000 ALTER TABLE `informevaloracionseguimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instalacion`
--

DROP TABLE IF EXISTS `instalacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instalacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `idtipoinstalacion` int(11) DEFAULT NULL,
  `idcomunidad` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instalacion`
--

LOCK TABLES `instalacion` WRITE;
/*!40000 ALTER TABLE `instalacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `instalacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria`
--

DROP TABLE IF EXISTS `maquinaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maquinaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `tipo` varchar(1) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `tiponombre` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `certificadoce` tinyint(1) DEFAULT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria`
--

LOCK TABLES `maquinaria` WRITE;
/*!40000 ALTER TABLE `maquinaria` DISABLE KEYS */;
/*!40000 ALTER TABLE `maquinaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notasinformativas`
--

DROP TABLE IF EXISTS `notasinformativas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notasinformativas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notasinformativas`
--

LOCK TABLES `notasinformativas` WRITE;
/*!40000 ALTER TABLE `notasinformativas` DISABLE KEYS */;
INSERT INTO `notasinformativas` VALUES (1,'Nota informativa modificada','Comunidad Test 004',-1,'2021-08-19 14:57:50','A',NULL,NULL),(2,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 14:57:51','A',NULL,NULL),(4,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 14:57:53','A',NULL,NULL),(5,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 14:57:53','A',NULL,NULL),(6,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 14:57:53','A',NULL,NULL),(7,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 14:57:54','A',NULL,NULL),(8,'Nota informativa','2021-08-11',-1,'2021-08-19 15:24:55','A',NULL,NULL),(9,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 15:26:06','A',NULL,NULL),(10,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 15:26:07','A',NULL,NULL),(11,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 15:26:08','A',NULL,NULL),(12,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 15:26:50','A',NULL,NULL),(13,'Nota informativa','Comunidad Test 004',-1,'2021-08-19 15:26:56','A',NULL,NULL);
/*!40000 ALTER TABLE `notasinformativas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requerimientos`
--

DROP TABLE IF EXISTS `requerimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requerimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `caduca` tinyint(1) NOT NULL DEFAULT '0',
  `tipo` varchar(5) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `activado` tinyint(1) NOT NULL DEFAULT '1',
  `tiempocaducidad` int(11) NOT NULL DEFAULT '0',
  `idcomunidad` int(11) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requerimientos`
--

LOCK TABLES `requerimientos` WRITE;
/*!40000 ALTER TABLE `requerimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `requerimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipopuestoempleado`
--

DROP TABLE IF EXISTS `tipopuestoempleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipopuestoempleado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipopuestoempleado`
--

LOCK TABLES `tipopuestoempleado` WRITE;
/*!40000 ALTER TABLE `tipopuestoempleado` DISABLE KEYS */;
INSERT INTO `tipopuestoempleado` VALUES (1,'Secretario / Administrativo'),(2,'Pintor'),(3,'Profesor de tenis'),(4,'Jardinería'),(5,'Mantenimiento'),(6,'Limpieza'),(7,'Consejería'),(8,'Recepcionista'),(9,'Gerente'),(10,'Empleado de fincas');
/*!40000 ALTER TABLE `tipopuestoempleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiposinstalacion`
--

DROP TABLE IF EXISTS `tiposinstalacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiposinstalacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiposinstalacion`
--

LOCK TABLES `tiposinstalacion` WRITE;
/*!40000 ALTER TABLE `tiposinstalacion` DISABLE KEYS */;
INSERT INTO `tiposinstalacion` VALUES (1,'Ascensores'),(2,'Instalaciones eléctricas'),(3,'Grupo contra incendios'),(4,'Otros');
/*!40000 ALTER TABLE `tiposinstalacion` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Table structure for table `usuarioRol`
--

DROP TABLE IF EXISTS `usuarioRol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarioRol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarioRol`
--

LOCK TABLES `usuarioRol` WRITE;
/*!40000 ALTER TABLE `usuarioRol` DISABLE KEYS */;
INSERT INTO `usuarioRol` VALUES (1,'Super Admin','sudo','2021-06-06 12:32:24',NULL,0),(2,'Administrador de sistema','admin','2021-06-06 12:32:24',NULL,0),(3,'Gestor DPD','dpd','2021-06-06 12:32:24',NULL,0),(4,'Revisor documental','revdoc','2021-06-06 12:32:24',NULL,0),(5,'Administrador de fincas','admfincas','2021-06-06 12:32:24',NULL,0),(6,'Contratista','contratista','2021-06-06 12:32:24',NULL,0),(7,'Empleado','empleado','2021-06-06 12:32:24',NULL,0);
/*!40000 ALTER TABLE `usuarioRol` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-06 22:39:43
