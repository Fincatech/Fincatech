<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de log de errores y varios
*
*
**/
namespace HappySoftware\Controller\Traits;
// use HappySoftware\Database\DatabaseCore;

trait LogTrait{

    public function WriteToLog($_logName, $_functionName, $_texto)
    {

        global $appSettings;

        $logFileName = ROOT_DIR . $appSettings['storage']['log'] . 'log_' . $_logName . '.log';

        $content = '--- [' . date('d-m-Y h:i') . '] --- ( ' . $_functionName . ' ) --- ' . PHP_EOL . PHP_EOL . $_texto . PHP_EOL . PHP_EOL;
        
        $fileHanddler = fopen($logFileName, "a");

        fwrite($fileHanddler, $content);
        fclose( $fileHanddler );

    }

    public static function AddToLog($_logName, $_functionName, $_texto)
    {
        global $appSettings;

        $logFileName = ROOT_DIR . $appSettings['storage']['log'] . 'log_' . $_logName . '.log';

        $content = '--- [' . date('d-m-Y h:i') . '] --- ( ' . $_functionName . ' ) --- ' . PHP_EOL . PHP_EOL . $_texto . PHP_EOL . PHP_EOL;
        
        $fileHanddler = fopen($logFileName, "a");

        fwrite($fileHanddler, $content);
        fclose( $fileHanddler );
    }

}