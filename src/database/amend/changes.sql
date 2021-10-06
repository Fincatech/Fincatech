ALTER TABLE `spa` 
CHANGE COLUMN `codigopostal` `codpostal` VARCHAR(5) NULL DEFAULT NULL ;

ALTER TABLE `usuario` 
ADD COLUMN `password` VARCHAR(40) NULL AFTER `rolid`;

####### 23/09/2021



CREATE TABLE `comunidadempresa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idcomunidad` INT(11) NULL,
  `idempresa` INT(11) NULL,
  `activa` TINYINT(1) NULL DEFAULT 1,
  `created` DATETIME NULL,
  PRIMARY KEY (`id`));

CREATE 
VIEW `view_empresascomunidad` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`razonsocial` AS `razonsocial`,
        `e`.`cif` AS `cif`,
        `e`.`telefono` AS `telefono`,
        `e`.`personacontacto` AS `personacontacto`,
        `e`.`email` AS `email`,
        `e`.`direccion` AS `direccion`,
        `e`.`idlocalidad` AS `idlocalidad`,
        `e`.`provinciaid` AS `provinciaid`,
        `e`.`localidad` AS `localidad`,
        `e`.`codigopostal` AS `codigopostal`,
        `e`.`created` AS `created`,
        `e`.`updated` AS `updated`,
        `e`.`usercreate` AS `usercreate`,
        `ce`.`idcomunidad` AS `idcomunidad`
    FROM
        ((`comunidadempresa` `ce`
        LEFT JOIN `comunidad` `c` ON ((`ce`.`idcomunidad` = `c`.`id`)))
        LEFT JOIN `empresa` `e` ON ((`e`.`id` = `ce`.`idempresa`)))

ALTER TABLE `informevaloracionseguimiento` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `estado`;

ALTER TABLE `tipopuestoempleado` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `nombre`;

ALTER TABLE `tiposinstalacion` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `nombre`;

ALTER TABLE `usuario` 
ADD COLUMN `movil` VARCHAR(20) NULL AFTER `telefono`;

# TODO: 24092021

CREATE TABLE `dpd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomunidad` int(11) DEFAULT NULL,
  `consulta` text COLLATE utf8mb4_spanish2_ci,
  `respuesta` text COLLATE utf8mb4_spanish2_ci,
  `solucionado` tinyint(1) DEFAULT '0',
  `usercreate` int(11) DEFAULT NULL,
  `create` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

ALTER TABLE `dpd` 
ADD COLUMN `fecharesolucion` DATETIME NULL AFTER `create`;

ALTER TABLE `dpd` 
ADD COLUMN `idfichero` INT(11) NULL AFTER `fecharesolucion`;

ALTER TABLE `ficheroscomunes` 
ADD COLUMN `nombrestorage` VARCHAR(255) NULL AFTER `nombre`;

CREATE TABLE `ficheroshistorico` (
  `id` INT NOT NULL,
  `entidadorigen` VARCHAR(70) NULL,
  `idoriginal` INT(11) NULL,
  `idcomunidad` INT(11) NULL,
  `idempresa` INT(11) NULL,
  `idrequerimiento` INT(11) NULL,
  `nombre` TEXT NULL,
  `ubicacion` TEXT NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `requerimientos` 
RENAME TO  `requerimiento` ;

CREATE TABLE `requerimientotipo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `informevaloracionseguimiento` 
ADD COLUMN `idusuario` INT(11) NULL AFTER `idfichero`,
CHANGE COLUMN `estado` `estado` VARCHAR(1) NULL DEFAULT NULL AFTER `idusuario`;

INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Documentación básica', '2021-01-01', '1');
INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Cámaras de seguridad', '2021-01-01', '1');
INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Contratos de cesión a terceros', '2021-01-01', '1');
INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('PRL - Empresa', '2021-01-01', '1');
INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('PRL - Empleado', '2021-01-01', '1');
INSERT INTO `requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Comunidad', '2021-01-01', '1');

ALTER TABLE `requerimiento` 
ADD COLUMN `idrequerimientotipo` INT(11) NULL AFTER `tiempocaducidad`;

UPDATE `requerimiento` SET `idrequerimientotipo` = '1', `idcomunidad` = NULL WHERE (`id` = '1');
UPDATE `requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '2');
UPDATE `requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '3');
UPDATE `requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '4');
UPDATE `requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '5');
UPDATE `requerimiento` SET `tipo` = 'COMEM' WHERE (`id` = '5');

ALTER TABLE `requerimiento` 
DROP COLUMN `tiempocaducidad`,
ADD COLUMN `sujetorevision` TINYINT(1) NULL AFTER `caduca`,
ADD COLUMN `requieredescarga` TINYINT(1) NULL AFTER `sujetorevision`;

ALTER TABLE `requerimiento` 
CHANGE COLUMN `caduca` `caduca` TINYINT(1) NULL DEFAULT '0' ;

ALTER TABLE `comunidadempresa` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `created`;

##########################################################################################
## 27/09/2021

ALTER TABLE `empleado` 
ADD COLUMN `localidad` VARCHAR(100) NULL AFTER `idlocalidad`,
ADD COLUMN `provinciaid` INT(11) NULL AFTER `localidad`;

ALTER TABLE `empleado` 
CHANGE COLUMN `codigopostal` `codpostal` VARCHAR(5) NULL DEFAULT NULL ;


ALTER TABLE `empleado` 
ADD COLUMN `telefono` VARCHAR(20) NULL AFTER `email`;

CREATE 
VIEW `view_empleadosempresa` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`idempleado` AS `idempleado`,
        `e`.`idempresa` AS `idempresa`,
        `epl`.`nombre` AS `nombre`,
        `epl`.`numerodocumento` AS `numerodocumento`,
        `epl`.`localidad` AS `localidad`,
        `epl`.`email` AS `email`,
        `epl`.`telefono` AS `telefono`,
        `epl`.`estado` AS `estado`,
        `tpe`.`nombre` AS `puesto`,
        `emp`.`razonsocial` AS `razonsocial`,
        `e`.`fechaalta` AS `fechaalta`,
        `epl`.`created` AS `created`
    FROM
        (((`empleadoempresa` `e`
        LEFT JOIN `empleado` `epl` ON ((`epl`.`id` = `e`.`idempleado`)))
        LEFT JOIN `empresa` `emp` ON ((`emp`.`id` = `e`.`idempresa`)))
        LEFT JOIN `tipopuestoempleado` `tpe` ON ((`tpe`.`id` = `epl`.`idtipopuestoempleado`)));

ALTER TABLE `empleado` 
CHANGE COLUMN `tipodocumento` `tipodocumento` VARCHAR(1) NULL DEFAULT 'N' ;        

ALTER TABLE `empleadoempresa` 
CHANGE COLUMN `fechaalta` `fechaalta` DATETIME NULL DEFAULT NULL ,
CHANGE COLUMN `fechabaja` `fechabaja` DATETIME NULL DEFAULT NULL ;

############ 28092021

ALTER TABLE `empresa` 
ADD COLUMN `estado` VARCHAR(1) NULL AFTER `codpostal`,
CHANGE COLUMN `codigopostal` `codpostal` VARCHAR(5) NULL DEFAULT NULL ;

ALTER TABLE `usuario` 
ADD COLUMN `idspa` INT(11) NULL AFTER `estado`;

CREATE TABLE `tiposservicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(40) NULL,
  `precio` FLOAT NULL,
  `preciocomunidad` FLOAT NULL,
  `retorno` FLOAT NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

INSERT INTO `tiposservicios` (`nombre`) VALUES ('CAE');
INSERT INTO `tiposservicios` (`nombre`) VALUES ('RGPD');
INSERT INTO `tiposservicios` (`nombre`) VALUES ('PRL');
INSERT INTO `tiposservicios` (`nombre`) VALUES ('Instalaciones');
INSERT INTO `tiposservicios` (`nombre`) VALUES ('Certificados Digitales');

update fincatech.tiposservicios set precio = 0, preciocomunidad = 0, retorno = 0;

CREATE TABLE `comunidadservicioscontratados` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idcomunidad` INT(11) NULL,
  `idservicio` INT(11) NULL,
  `precio` FLOAT NULL,
  `preciocomunidad` FLOAT NULL,
  `retorno` FLOAT NULL,
  `usercreate` INT(11) NULL,
  `created` DATETIME NULL,
  PRIMARY KEY (`id`));

#####################
ALTER TABLE `comunidad` 
ADD COLUMN `idspa` INT(11) NULL AFTER `cif`;

ALTER TABLE `usuario` 
DROP COLUMN `idspa`;

#########

CREATE OR REPLACE VIEW `view_comunidadservicioscontratados` AS
    SELECT 
        `s`.`nombre` AS `nombre`,
        `cs`.`contratado` AS `contratado`,
        `s`.`precio` AS `servicioprecioservicio`,
        `s`.`preciocomunidad` AS `serviciopreciocomunidad`,
        `s`.`retorno` AS `servicioretorno`,
        `cs`.`id` AS `idserviciocomunidad`,
        `cs`.`idcomunidad` AS `idcomunidad`,
        `cs`.`idservicio` AS `id`,
        `cs`.`precio` AS `precio`,
        `cs`.`preciocomunidad` AS `preciocomunidad`,
        `cs`.`retorno` AS `retorno`,
        `cs`.`usercreate` AS `usercreate`,
        `cs`.`created` AS `created`
    FROM
        (`tiposservicios` `s`
        LEFT JOIN `comunidadservicioscontratados` `cs` ON ((`cs`.`idservicio` = `s`.`id`)))

INSERT INTO `comunidadservicioscontratados` (`idcomunidad`, `idservicio`, `precio`, `preciocomunidad`, `retorno`, `usercreate`, `created`) VALUES ('22', '1', '0', '0', '0', '1', '2021-01-01');
INSERT INTO `comunidadservicioscontratados` (`idcomunidad`, `idservicio`, `precio`, `preciocomunidad`, `retorno`, `usercreate`, `created`) VALUES ('22', '2', '0', '0', '0', '1', '2021-01-01');
INSERT INTO `comunidadservicioscontratados` (`idcomunidad`, `idservicio`, `precio`, `preciocomunidad`, `retorno`, `usercreate`, `created`) VALUES ('22', '3', '0', '0', '0', '1', '2021-01-01');
INSERT INTO `comunidadservicioscontratados` (`idcomunidad`, `idservicio`, `precio`, `preciocomunidad`, `retorno`, `usercreate`, `created`) VALUES ('22', '4', '0', '0', '0', '1', '2021-01-01');
INSERT INTO `comunidadservicioscontratados` (`idcomunidad`, `idservicio`, `precio`, `preciocomunidad`, `retorno`, `usercreate`, `created`) VALUES ('22', '5', '0', '0', '0', '1', '2021-01-01');

ALTER TABLE `comunidadservicioscontratados` 
ADD COLUMN `contratado` TINYINT(1) NULL DEFAULT 0 AFTER `preciocomunidad`;
   
####

ALTER TABLE `empresa` 
ADD COLUMN `idtipoempresa` INT(11) NULL AFTER `codpostal`;

CREATE TABLE `empresatipo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(20) NULL,
  PRIMARY KEY (`id`));

INSERT INTO `empresatipo` (`nombre`) VALUES ('Empresa');
INSERT INTO `empresatipo` (`nombre`) VALUES ('Autónomo');

#########

ALTER TABLE `comunidad` 
ADD COLUMN `ibancomunidad` VARCHAR(30) NULL AFTER `cif`;

CREATE OR REPLACE VIEW `view_comunidadesempresa` AS
    SELECT 
        `ce`.`activa` AS `activa`,
        `ce`.`created` AS `fechaasociacion`,
        `ce`.`id` AS `idrelation`,
        `c`.`id` AS `idcomunidad`,
        `e`.`id` AS `idempresa`,
        `c`.`codigo` AS `codigo`,
        `c`.`nombre` AS `nombre`,
        `c`.`idspa` AS `idspa`,
        `c`.`usuarioId` AS `usuarioId`
    FROM
        ((`comunidadempresa` `ce`
        LEFT JOIN `empresa` `e` ON ((`e`.`id` = `ce`.`idempresa`)))
        LEFT JOIN `comunidad` `c` ON ((`c`.`id` = `ce`.`idcomunidad`)))
    ORDER BY `c`.`nombre`

create function idempresarequerimiento() returns INTEGER DETERMINISTIC NO SQL return @idempresarequerimiento;

CREATE OR REPLACE VIEW `view_documentoscaeempresa` AS
    SELECT 
        `er`.`id` AS `idrelacion`,
        `r`.`nombre` AS `requerimiento`,
        `r`.`caduca` AS `caduca`,
        `r`.`sujetorevision` AS `sujetorevision`,
        `r`.`requieredescarga` AS `requieredescarga`,
        `r`.`activado` AS `activado`,
        `r`.`id` AS `idrequerimiento`,
        `r`.`idrequerimientotipo` AS `idrequerimientotipo`,
        `er`.`idempresa` AS `idempresa`,
        `er`.`idcomunidad` AS `idcomunidad`,
        `er`.`idestado` AS `idestado`,
        `er`.`created` AS `created`,
        `er`.`updated` AS `fechaultimaactuacion`,
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
        LEFT JOIN `empresarequerimiento` `er` ON (((`er`.`idrequerimiento` = `r`.`id`)
            AND (`er`.`idempresa` = IDEMPRESAREQUERIMIENTO()))))
        LEFT JOIN `ficheroscomunes` `f` ON ((`f`.`id` = `r`.`idfichero`)))
        LEFT JOIN `ficheroscomunes` `fr` ON ((`fr`.`id` = `er`.`idfichero`)))
    WHERE
        (`r`.`idrequerimientotipo` = 4)
    ORDER BY `r`.`nombre`

ALTER TABLE `comunidadrequerimiento` 
DROP FOREIGN KEY `FK_COMREQ_ESTADO`;

ALTER TABLE `comunidadrequerimiento` 
ADD COLUMN `estado` VARCHAR(2) NULL AFTER `fechacaducidad`,
CHANGE COLUMN `idrequerimiento` `idtiporequerimiento` INT(11) NOT NULL ,
CHANGE COLUMN `idestado` `idestado` INT(11) NULL ;

ALTER TABLE `comunidadrequerimiento` 
ADD CONSTRAINT `FK_COMREQ_ESTADO`
  FOREIGN KEY (`idestado`)
  REFERENCES `documentoestado` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `comunidadrequerimiento` 
CHANGE COLUMN `idtiporequerimiento` `idrequerimiento` INT(11) NOT NULL ;

ALTER TABLE `empleadorequerimiento` 
ADD COLUMN `idrequerimiento` INT(11) NULL AFTER `idempleado`;

######## 04/10/2021
USE `fincatech`;
CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `view_documentoscomunidad` AS
    SELECT 
        `cr`.`id` AS `idrelacion`,
        `r`.`nombre` AS `requerimiento`,
        `r`.`caduca` AS `caduca`,
        `r`.`sujetorevision` AS `sujetorevision`,
        `r`.`requieredescarga` AS `requieredescarga`,
        `r`.`activado` AS `activado`,
        `r`.`tipo` AS `tiporequerimiento`,
        `r`.`id` AS `idrequerimiento`,
        `r`.`idrequerimientotipo` AS `idrequerimientotipo`,
        IDEMPRESAREQUERIMIENTO() AS `idcomunidad`,
        `cr`.`idestado` AS `idestado`,
        `cr`.`created` AS `created`,
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
        LEFT JOIN `comunidadrequerimiento` `cr` ON (((`cr`.`idrequerimiento` = `r`.`id`)
            AND (`cr`.`idcomunidad` = IDEMPRESAREQUERIMIENTO()))))
        LEFT JOIN `ficheroscomunes` `f` ON ((`f`.`id` = `r`.`idfichero`)))
        LEFT JOIN `ficheroscomunes` `fr` ON ((`fr`.`id` = `cr`.`idfichero`)))
    WHERE
        (`r`.`idrequerimientotipo` = 6)
    ORDER BY `r`.`nombre`;

######## 05102021 14:09

CREATE OR REPLACE
VIEW `view_empleadosempresa` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`idempleado` AS `idempleado`,
        `e`.`idempresa` AS `idempresa`,
        `e`.`idcomunidad` AS `idcomunidad`,
        `epl`.`nombre` AS `nombre`,
        `epl`.`numerodocumento` AS `numerodocumento`,
        `epl`.`localidad` AS `localidad`,
        `epl`.`email` AS `email`,
        `epl`.`telefono` AS `telefono`,
        `epl`.`estado` AS `estado`,
        `tpe`.`nombre` AS `puesto`,
        `emp`.`razonsocial` AS `razonsocial`,
        DATE_FORMAT(`e`.`fechaalta`, '%d-%m-%Y') AS `fechaalta`,
        DATE_FORMAT(`e`.`fechabaja`, '%d-%m-%Y') AS `fechabaja`,
        `epl`.`created` AS `created`,
        'Externo' AS `tipoempleado`
    FROM
        (((`empleadoempresa` `e`
        LEFT JOIN `empleado` `epl` ON ((`epl`.`id` = `e`.`idempleado`)))
        LEFT JOIN `empresa` `emp` ON ((`emp`.`id` = `e`.`idempresa`)))
        LEFT JOIN `tipopuestoempleado` `tpe` ON ((`tpe`.`id` = `epl`.`idtipopuestoempleado`)));

ALTER TABLE `fincatech`.`empleadoempresa` 
DROP FOREIGN KEY `FK_EMPLEMPR_EMPRESA`,
DROP FOREIGN KEY `FK_EMPLEMPR_EMPLEADO`,
DROP FOREIGN KEY `FK_EMPLEMPR_COMUNIDAD`;
ALTER TABLE `fincatech`.`empleadoempresa` 
DROP INDEX `FK_EMPLEMPR_EMPLEADO_idx` ,
DROP INDEX `FK_EMPLEMPR_COMUNIDAD_idx` ,
DROP INDEX `FK_EMPLEMPR_EMPRESA_idx` ;
;

ALTER TABLE `fincatech`.`empleadoempresa` 
CHANGE COLUMN `fechaalta` `fechaalta` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `fechabaja` `fechabaja` DATE NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`empleadocomunidad` 
CHANGE COLUMN `fechaalta` `fechaalta` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `fechabaja` `fechabaja` DATE NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`empresa` 
CHANGE COLUMN `razonsocial` `razonsocial` VARCHAR(100) NOT NULL ;

ALTER TABLE `fincatech`.`comunidad` 
CHANGE COLUMN `localidad` `localidad` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`comunidad` 
CHANGE COLUMN `localidadid` `idlocalidad` INT(11) NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`comunidad` 
CHANGE COLUMN `cif` `cif` VARCHAR(20) NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`empresa` 
CHANGE COLUMN `razonsocial` `razonsocial` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_spanish_ci' NOT NULL ;

ALTER TABLE `fincatech`.`comunidad` 
CHANGE COLUMN `nombre` `nombre` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_spanish_ci' NOT NULL ;

USE `fincatech`;
CREATE OR REPLACE VIEW `view_empleadoscomunidad` AS
    SELECT 
        `e`.`id` AS `id`,
        `e`.`idempleado` AS `idempleado`,
        null as idempresa,
        `e`.`idcomunidad` AS `idcomunidad`,
        `epl`.`nombre` AS `nombre`,
        `epl`.`numerodocumento` AS `numerodocumento`,
        `epl`.`localidad` AS `localidad`,
        `epl`.`email` AS `email`,
        `epl`.`telefono` AS `telefono`,
        `epl`.`estado` AS `estado`,
        `tpe`.`nombre` AS `puesto`,
        `com`.`nombre` AS `razonsocial`,
        DATE_FORMAT(`e`.`fechaalta`, '%d-%m-%Y') AS `fechaalta`,
        DATE_FORMAT(`e`.`fechabaja`, '%d-%m-%Y') AS `fechabaja`,
        `epl`.`created` AS `created`,
        'Comunidad' AS `tipoempleado`
    FROM
        (((`empleadocomunidad` `e`
        LEFT JOIN `empleado` `epl` ON ((`epl`.`id` = `e`.`idempleado`)))
        LEFT JOIN `comunidad` `com` ON ((`com`.`id` = `e`.`idcomunidad`)))
        LEFT JOIN `tipopuestoempleado` `tpe` ON ((`tpe`.`id` = `epl`.`idtipopuestoempleado`)));

ALTER TABLE `fincatech`.`empleadorequerimiento` 
ADD COLUMN `fechacaducidad` DATE NULL AFTER `fechadescarga`,
ADD COLUMN `idestado` INT(11) NULL AFTER `idfichero`,
ADD COLUMN `observaciones` VARCHAR(255) NULL AFTER `idestado`;

CREATE OR REPLACE 
VIEW `view_documentosempleado` AS
    SELECT 
        `eplr`.`id` AS `idrelacion`,
        `r`.`nombre` AS `requerimiento`,
        `r`.`caduca` AS `caduca`,
        `r`.`sujetorevision` AS `sujetorevision`,
        `r`.`requieredescarga` AS `requieredescarga`,
        `r`.`activado` AS `activado`,
        `r`.`tipo` AS `tiporequerimiento`,
        `r`.`id` AS `idrequerimiento`,
        `r`.`idrequerimientotipo` AS `idrequerimientotipo`,
        IDEMPRESAREQUERIMIENTO() AS `idempleado`,
        `eplr`.`idestado` AS `idestado`,
        `eplr`.`created` AS `created`,
        `eplr`.`updated` AS `fechaultimaactuacion`,
        `eplr`.`observaciones` AS `observaciones`,
        `eplr`.`fechadescarga` AS `fechadescarga`,
        `eplr`.`fechacaducidad` AS `fechacaducidad`,
        eplr.estado,
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
        LEFT JOIN `empleadorequerimiento` `eplr` ON (((`eplr`.`idrequerimiento` = `r`.`id`)
            AND (`eplr`.`idempleado` = IDEMPRESAREQUERIMIENTO()))))
        LEFT JOIN `ficheroscomunes` `f` ON ((`f`.`id` = `r`.`idfichero`)))
        LEFT JOIN `ficheroscomunes` `fr` ON ((`fr`.`id` = `eplr`.`idfichero`)))
    WHERE
        (`r`.`idrequerimientotipo` = 5)
    ORDER BY `r`.`nombre`;

########### 5/10/2021 20:07

ALTER TABLE `fincatech`.`empleadorequerimiento` 
ADD COLUMN `idcomunidad` INT(11) NULL AFTER `idempresa`;
