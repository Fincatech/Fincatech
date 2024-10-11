<?php

nameSpace HappySoftware\Controller\Traits;

//  Incluimos las constantes globales
require_once( ABSPATH . 'src/Includes/defines.php' );

//  Incluimos la configuración del framework
require_once( ABSPATH . 'src/Includes/settings.php' );

trait ConfigTrait{

    private static $settings;

    public static function getHSNamespaceName()
    {
        global $appSettings;
        return $appSettings['project']['namespace'];
    }

    /** Devuelve el entorno de deploy */
    public function getEnvironment()
    {
        return $this->settings[ENVIRONMENT];
    }

    public function getSetting($setting)
    {
        return $this->settings[$setting];
    }

    public function GetURL()
    {
        //  Comprobamos el dominio que es para poder asignar el logo correspondiente
        $dominio = 'https://' . $_SERVER['SERVER_NAME'];
        // $aDominio = explode('.', $dominio);
        // $nombreFranquiciado = $aDominio[count($aDominio) - 2];
        return strtolower($dominio);
    }

}