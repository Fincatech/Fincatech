<?php

/** Fichero de settings generales de la aplicación */
//TODO: Montar an fichero YAML con un trait para recuperar elementos de esta configuración
// Configuración general
global $appSettings;

define("JWT_SECRET_KEY", 'U_97$5TwNA3N8$qYV4vKK_#k');

$appSettings["env"] = [];

$appSettings = [
    'env' => "dev", // Admite: "dev" para desarrollo o "prod" para producción,
    //  Modo mantenimiento del site
    'maintenance' => true,
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
        'templates'     => '/src/Views/templates/',
        'emailtemplates'=> '/src/Views/templates/mails/',
        'path'          => '/public/storage/',
        'facturas'      => '/public/storage/factura',
        'facturaszip'   => '/public/storage/facturazip',
        'remesas'       => '/public/storage/remesas',
        'certificados'  => '/public/storage/emailcertificados', 
        'private'       => '/public/storage/private', 
        'log'           => '/public/log/', 
    ],
    //  Servidores FTP
    'ftp_servers' =>[
        'facturacion'   => [
            'server_id' => '001',
            'server_url' => 'https://factura.fincatech.es/',
            'url'       => 'home400351608.1and1-data.host',
            'port'      => 22,
            'user'      => 'acc652662948',
            'password'  => 'e)EJ;2O!p94S',
            'use_ssl'   => true
        ]
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
