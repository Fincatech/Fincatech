ALTER TABLE `fincatech`.`spa` 
CHANGE COLUMN `codigopostal` `codpostal` VARCHAR(5) NULL DEFAULT NULL ;

ALTER TABLE `fincatech`.`usuario` 
ADD COLUMN `password` VARCHAR(40) NULL AFTER `rolid`;

####### 23/09/2021

