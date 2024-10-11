<?php

namespace HappySoftware;

global $databaseCore;

define( 'ABSPATH', dirname(dirname( __FILE__ )) . '/' );
define( 'ROOTDIR', dirname(dirname(dirname(dirname( __FILE__ )))) . '/' );

class Autoload
{

    public static function autoLoadclass($nombreClase) {
        // Directorio base donde se encuentran todas las clases
        $directorioBase = ABSPATH . 'src/';

        // Reemplaza los caracteres de espacio de nombres con el separador de directorios (por ejemplo, \ a /)
        $nombreClase = str_replace('\\', '/', $nombreClase);
        $nombreClase = str_replace(__NAMESPACE__ . '/', '', $nombreClase);
        $nombreClase = str_replace('Fincatech/', '', $nombreClase);

        // Construye la ruta completa al archivo de la clase
        $rutaArchivo = $directorioBase . $nombreClase . '.php';

        // Verifica si el archivo de la clase existe y lo incluye
        if (file_exists($rutaArchivo)) {
            //  Recuperamos el nombre de la clase
            $className = explode('/', $nombreClase);
            $className = $className[count($className)-1];
            if(!class_exists($className)){
                include_once $rutaArchivo;
            }
        }
    }

}

// Registra la función de autocarga
spl_autoload_register(__NAMESPACE__ . '\Autoload::autoLoadclass');

    function processDirToInclude($_path)
    {

        if (!is_dir($_path)) {
            exit('Invalid diretory path');
        }

        foreach (scandir($_path) as $file) {
            if ($file !== '.' && $file !== '..' && $file != '@eaDir') 
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
