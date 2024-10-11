<?php

    $folder = 'fincatech/';

    date_default_timezone_set('Europe/Madrid');

    define( 'ABSPATH', dirname(dirname( __FILE__ )) . '/' );
    define('PROTOCOL',(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://');
    define('DOMAIN',$_SERVER['HTTP_HOST']);
    define('ROOT_URL', preg_replace("/\/$/",'',PROTOCOL.DOMAIN.str_replace(array('\\',"index.php","index.html"), '', dirname(htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES))),1).'/');// Remove backslashes for Windows compatibility

    //  Obtenemos la carpeta del proyecto por si estuviese en una subcarpeta
    
    $folder = 'fincatech/';

    define('HOME_URL', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $folder);

//  Cargamos composer y sus dependencias
    require_once(ABSPATH . "vendor/autoload.php");

//  Inicializamos la aplicación
    error_reporting(E_ALL);

    //  Constantes globales
    require_once(ABSPATH . 'app/defines.php');
    //  Configuración de la aplicación
    require_once(ABSPATH . 'app/settings.php');
    //  Constantes de seguridad. JWT
    require_once(ABSPATH . 'app/security.php');
    //  Bloques que se cargarán para las diferentes vistas y roles de usuario
    require_once(ABSPATH . 'app/views.php');

    //  Instancia de traits TODO: Automatizarlo
    require_once(ABSPATH . 'src/Controller/Traits/SecurityTrait.php');
    require_once(ABSPATH . 'src/Controller/Traits/TableTrait.php');
    require_once(ABSPATH . 'src/Controller/Traits/ViewTrait.php');
    require_once(ABSPATH . 'src/Controller/Traits/UtilsTrait.php');
    // require_once(ABSPATH . 'src/Controller/Traits/UanatacaTrait.php');
    
    require_once(ABSPATH . 'src/Controller/FrontController.php');
    
    //  Instanciamos el front controller
    $App = new \HappySoftware\Controller\MainController;

    $App->Run();