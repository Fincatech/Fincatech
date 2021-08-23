<?php

define( 'ABSPATH', dirname(dirname( __FILE__ )) . '/' );

//  Core de base de datos
require_once ABSPATH .'src/Includes/database/mysqlcore.php';

//  Trait de configuración TODO: Parametrizar y automatizar esta parte
require_once ABSPATH . 'src/Controller/Traits/ConfigTrait.php';
require_once ABSPATH . 'src/Controller/Traits/EntityTrait.php';

//  Traits del modelo
require_once ABSPATH . 'src/Model/Traits/DatabaseTrait.php';
require_once ABSPATH . 'src/Model/Traits/CrudTrait.php';
require_once ABSPATH . 'src/Model/Traits/EntityTrait.php';
require_once ABSPATH . 'src/Model/Traits/SchemaTrait.php';
require_once ABSPATH . 'src/Model/Traits/UtilsTrait.php';
require_once ABSPATH . 'src/Model/Traits/TableTrait.php';

//  Controlador
require_once ABSPATH .'src/Controller/HelperController.php';
require_once ABSPATH .'src/Controller/FrontController.php';

//  Modelo de la APP
require_once ABSPATH .'src/Model/Model.php';

//  Entidad
require_once ABSPATH .'src/Entity/EntityHelper.php';