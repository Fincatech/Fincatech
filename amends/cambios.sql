# [x] - SMS certificado
CREATE TABLE `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `message` varchar(160) NOT NULL,
  `storagefileid` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `emailscertificados` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `created`;

ALTER TABLE `mensaje` 
ADD COLUMN `mensajecertificadoid` INT(11) NULL AFTER `usercreate`;

# [ ] - 03-01-2023
ALTER TABLE `sms` 
ADD COLUMN `storagefileid` INT(11) NULL AFTER `message`;

# [ ] - 11-01-2023
CREATE TABLE admindocument (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuarioid` VARCHAR(45) NOT NULL,
  `frontid` VARCHAR(45) NULL,
  `rearid` VARCHAR(45) NULL,
  `created` DATETIME NULL,
  `updated` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

#17-01-2023
CREATE TABLE `certificadorequerimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) DEFAULT NULL,
  `idcomunidad` int(11) DEFAULT NULL,
  `idrequerimiento` int(11) DEFAULT NULL,
  `fechasubida` datetime DEFAULT NULL,
  `fechadescarga` datetime DEFAULT NULL,
  `fechacaducidad` date DEFAULT NULL,
  `idfichero` int(11) DEFAULT NULL,
  `idestado` int(11) DEFAULT '3',
  `estado` varchar(1) COLLATE utf8mb4_spanish2_ci DEFAULT 'P',
  `observaciones` TEXT COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `usercreate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

CREATE  VIEW `view_certificadocomunidad` AS 
    SELECT 
        `cr`.`id` AS `idrelacion`,
        `r`.`nombre` AS `requerimiento`,
        `r`.`orden` AS `orden`,
        `r`.`caduca` AS `caduca`,
        `r`.`sujetorevision` AS `sujetorevision`,
        `r`.`requieredescarga` AS `requieredescarga`,
        `r`.`activado` AS `activado`,
        `r`.`tipo` AS `tiporequerimiento`,
        `r`.`id` AS `idrequerimiento`,
        `r`.`idrequerimientotipo` AS `idrequerimientotipo`,
        P1() AS `idcomunidad`,
        `cr`.`idestado` AS `idestado`,
        `cr`.`created` AS `created`,
        `cr`.`observaciones` AS `observaciones`,
        `cr`.`updated` AS `fechaultimaactuacion`,
        `r`.`nombre` AS `nombre`,
        `fr`.`id` AS `idficherorequerimiento`,
        `fr`.`nombre` AS `nombreficherorequerimiento`,
        `fr`.`nombrestorage` AS `storageficherorequerimiento`,
        `fr`.`ubicacion` AS `ubicacionficherorequerimiento`,
        `fr`.`created` AS `fechasubida`,
        `f`.`id` AS `idfichero`,
        `f`.`nombre` AS `nombrefichero`,
        `f`.`nombrestorage` AS `storagefichero`,
        `f`.`ubicacion` AS `ubicacionfichero`
    FROM
        (((`requerimiento` `r`
        LEFT JOIN `certificadorequerimiento` `cr` ON (((`cr`.`idrequerimiento` = `r`.`id`)
            AND (`cr`.`idcomunidad` = P1()))))
        LEFT JOIN `ficheroscomunes` `f` ON ((`f`.`id` = `r`.`idfichero`)))
        LEFT JOIN `ficheroscomunes` `fr` ON ((`fr`.`id` = `cr`.`idfichero`)))
    WHERE
        (`r`.`idrequerimientotipo` = 10)
    ORDER BY `r`.`orden`

CREATE TABLE comunidadcertificado (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idcomunidad` INT(11) NULL,
  `idadministrador` INT(11) NULL,
  `fechasolicitud` DATETIME NULL,
  `solicitadouanataca` TINYINT(1) NULL DEFAULT 0,
  `aprobado` TINYINT(1) NULL DEFAULT 0,
  `fechaaprobacion` DATETIME NULL,
  `requerimientosIdS` VARCHAR(40) NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  `userapproved` INT(11) NULL,
  `estado` VARCHAR(1) NULL DEFAULT 'P',

  PRIMARY KEY (`id`));

# Nuevo rol para técnico de certificados
insert into rol(nombre, alias, created, usercreate) values('Revisor Certificados Digitales','revcert','2023-01-01',0);

#Nueva tabla para ejecución de cron
CREATE TABLE `cron` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(40) NOT NULL,
  `resultado` CHAR(1) NULL,
  `fechaejecucion` DATETIME NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

#12 abril 2023 -> Se añade el nombre del destinatario a la tabla de mensajes
ALTER TABLE `mensaje` 
ADD COLUMN `destinatarionombre` VARCHAR(255) NULL AFTER `usuarioid`;

#06 de junio 2023 -> Se añade tabla para validación de e-mails según respuesta de mensatek
CREATE TABLE `emailblacklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

#Miércoles 12 julio 2023
CREATE TABLE representantelegal (
  `id` INT NOT NULL AUTO_INCREMENT,
  `administradorid` INT(11),
  `nombre` VARCHAR(100) NULL,
  `apellido` VARCHAR(100) NULL,
  `apellido2` VARCHAR(100) NULL,
  `email` VARCHAR(255) NULL,
  `documento` VARCHAR(20) NULL,
  `imagenfrontal` INT(11) NULL `documento`,
  `imagentrasera` INT(11) NULL `imagenfrontal`,  
  `telefono` VARCHAR(15) NULL,
  `observaciones` TEXT NULL,
  `estado` VARCHAR(1) NULL DEFAULT 'A',
  `created` DATETIME NULL,
  `updated` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE comunidadcertificado 
CHANGE COLUMN `idadministrador` `idrepresentante` INT(11) NULL DEFAULT NULL ;