Cambios:

====================
        Varios
====================
[] routes.php
[] security.php
[] root/app/security.php
[] root->public/index.php
[x] api/v1/src/Includes/defines.php

====================
        js
====================
[x] certificadodigital
[] adminfincas

====================
       Lógica
====================
Controller          : 
                        [] CertificadodigitalController
                        [] ComunidadController
                        [] FrontController
                        [] SmsController
                        [] Comunidad
                        [] Documental
                        [] RepresentanteLegal
                        [] Usuario

Model               :   [] CertificadodigitalModel
                        [] Comunidad
                        [] Documental
                        [] RepresentanteLegal
                        
Controller/Trait    :   [] Smstrait
                        [] FilesTrait
                        [] UanatacaTrait
                        [] SecurityTrait
                        [] CrudTrait

Entity              :   [] EntityHelper
                        [] RelationEntity

====================
        Vistas
====================



====================
        bbdd
====================

Nueva tabla para contratos por sms
Nueva tabla para registro de envíos sms

Nuevo tipo de requerimiento para certificados digitales
Documentos de requerimientos para certificados digitales

[dd/mm/aaaa] [ ] SMS y envíos de e-mails certificados para administradores de fincas
Subida SMS y E-mails certificados para administradores de fincas

API:

        SMSController, SmsModel, Sms (entity)
        Tabla de control de SMS
        Routes 

FRONT:

        
[] 04/09/2023 - TÉCNICO REVISIÓN DOCUMENTACIÓN CAE

+ Nuevo rol: TECNICO DOCUMENTACIÓN CAE: REVCAE
INSERT INTO `rol` (`nombre`, `alias`, `created`, `usercreate`) VALUES ('Revisor Documentación CAE', 'revcae', '2023-01-09 00:00:00', '0');
