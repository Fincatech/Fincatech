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

