ALTER TABLE `fincatech`.`spa` 
CHANGE COLUMN `codigopostal` `codpostal` VARCHAR(5) NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`usuario` 
ADD COLUMN `password` VARCHAR(40) NULL AFTER `rolid`;

####### 23/09/2021



CREATE TABLE `fincatech`.`comunidadempresa` (
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

ALTER TABLE `fincatech`.`informevaloracionseguimiento` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `estado`;

ALTER TABLE `fincatech`.`tipopuestoempleado` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `nombre`;

ALTER TABLE `fincatech`.`tiposinstalacion` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `nombre`;

ALTER TABLE `fincatech`.`usuario` 
ADD COLUMN `movil` VARCHAR(20) NULL AFTER `telefono`;

# TODO: 24092021

CREATE TABLE `fincatech`.`dpd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomunidad` int(11) DEFAULT NULL,
  `consulta` text COLLATE utf8mb4_spanish2_ci,
  `respuesta` text COLLATE utf8mb4_spanish2_ci,
  `solucionado` tinyint(1) DEFAULT '0',
  `usercreate` int(11) DEFAULT NULL,
  `create` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

ALTER TABLE `fincatech`.`dpd` 
ADD COLUMN `fecharesolucion` DATETIME NULL AFTER `create`;

ALTER TABLE `fincatech`.`dpd` 
ADD COLUMN `idfichero` INT(11) NULL AFTER `fecharesolucion`;

ALTER TABLE `fincatech`.`ficheroscomunes` 
ADD COLUMN `nombrestorage` VARCHAR(255) NULL AFTER `nombre`;

CREATE TABLE `fincatech`.`ficheroshistorico` (
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

ALTER TABLE `fincatech`.`requerimientos` 
RENAME TO  `fincatech`.`requerimiento` ;

CREATE TABLE `fincatech`.`requerimientotipo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NULL,
  `created` DATETIME NULL,
  `usercreate` INT(11) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `fincatech`.`informevaloracionseguimiento` 
ADD COLUMN `idusuario` INT(11) NULL AFTER `idfichero`,
CHANGE COLUMN `estado` `estado` VARCHAR(1) NULL DEFAULT NULL AFTER `idusuario`;

INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Documentación básica', '2021-01-01', '1');
INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Cámaras de seguridad', '2021-01-01', '1');
INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Contratos de cesión a terceros', '2021-01-01', '1');
INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('PRL - Empresa', '2021-01-01', '1');
INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('PRL - Empleado', '2021-01-01', '1');
INSERT INTO `fincatech`.`requerimientotipo` (`nombre`, `created`, `usercreate`) VALUES ('Comunidad', '2021-01-01', '1');

ALTER TABLE `fincatech`.`requerimiento` 
ADD COLUMN `idrequerimientotipo` INT(11) NULL AFTER `tiempocaducidad`;

UPDATE `fincatech`.`requerimiento` SET `idrequerimientotipo` = '1', `idcomunidad` = NULL WHERE (`id` = '1');
UPDATE `fincatech`.`requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '2');
UPDATE `fincatech`.`requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '3');
UPDATE `fincatech`.`requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '4');
UPDATE `fincatech`.`requerimiento` SET `idrequerimientotipo` = '1' WHERE (`id` = '5');
UPDATE `fincatech`.`requerimiento` SET `tipo` = 'COMEM' WHERE (`id` = '5');

ALTER TABLE `fincatech`.`requerimiento` 
DROP COLUMN `tiempocaducidad`,
ADD COLUMN `sujetorevision` TINYINT(1) NULL AFTER `caduca`,
ADD COLUMN `requieredescarga` TINYINT(1) NULL AFTER `sujetorevision`;

ALTER TABLE `fincatech`.`requerimiento` 
CHANGE COLUMN `caduca` `caduca` TINYINT(1) NULL DEFAULT '0' ;

ALTER TABLE `fincatech`.`comunidadempresa` 
ADD COLUMN `usercreate` INT(11) NULL AFTER `created`;

##########################################################################################
## 27/09/2021
