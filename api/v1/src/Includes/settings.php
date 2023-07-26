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
        'certificados' => '/public/storage/emailcertificados', 
        'private' => '/public/storage/private', 
        'log' => '/public/log/', 
    ],
    'uanataca' => [
        'validity_time' => '1825',
        'credentials' =>[
            'user' => '1097882',
            'pass' => 'C3ba3Cm_',
            'pin' => 'belorado74'
        ],
        'dev' => [
            'apihost' => 'https://api.sandbox.uanataca.com/api/v1/',
            'autoridadregistroid'=>'59',
            'certpath' => 'src/Includes/certificates/sandbox/',
            'raoid' => '56'
        ],
        'prod' => [
            'apihost' => 'https://api.uanataca.com/api/v1/',
            'autoridadregistroid' =>'1277',
            'certpath' => 'src/Includes/certificates/prod/',
            'raoid' => '1277'
        ],
        'urldev' => '',
        'urlprod' => '',
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
