<?php

global $databaseCore;

define( 'ABSPATH', dirname(dirname( __FILE__ )) . '/' );

function processDirToInclude($_path)
{

    if (!is_dir($_path)) {
        exit('Invalid diretory path');
    }

    foreach (scandir($_path) as $file) {
        if ($file !== '.' && $file !== '..') 
            require_once $_path . '/' . $file;
    }

}

//  Core de base de datos
    require_once ABSPATH .'src/Includes/database/mysqlcore.php';

//  Trait de configuración 
    processDirToInclude(ABSPATH . 'src/Controller/Traits');

//  Traits del modelo
    processDirToInclude(ABSPATH . 'src/Model/Traits');

//  Controlador
    require_once ABSPATH .'src/Controller/HelperController.php';
    require_once ABSPATH .'src/Controller/FrontController.php';

//  Modelo de la APP
    require_once ABSPATH .'src/Model/Model.php';

//  Entidad
    require_once ABSPATH .'src/Entity/EntityHelper.php';
