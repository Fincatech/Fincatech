<?php

/** Fichero de settings generales de la aplicación */

// Configuración general
global $appSettings;

define("JWT_SECRET_KEY", 'U_97$5TwNA3N8$qYV4vKK_#k');

$appSettings["env"] = [];

$appSettings = [
    'env' => "dev", // Admite: "dev" para desarrollo o "prod" para producción
    //  Configuración auxiliar para la base de datos
    'database' => [
        'checkintegrity' => ($appSettings[ENVIRONMENT] == "dev" ? true : false)
    ],
    //  Configuración del proyecto
    'project' => [
        'namespace' => '\Fincatech\\'
    ],
    //  Configuración de la ruta de almacén de ficheros
    'storage' => [
        'path' => '/public/storage/',
    ],
    //  Configuración para el deploy directo en el servidor
    'deploy' => [
        'ftp' => [
            'url' => '',
            'username' => '',
            'password' => '',
            'destinationFolder' => ''
        ]
    ],

    
]; 
