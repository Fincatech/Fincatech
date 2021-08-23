<?php

nameSpace HappySoftware\Controller\Traits;

//  Incluimos las constantes globales
require_once( ABSPATH . 'src/Includes/defines.php' );

//  Incluimos la configuración del framework
require_once( ABSPATH . 'src/Includes/settings.php' );

trait ConfigTrait{

    private static $settings;

    public static function getNamespaceName()
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

}