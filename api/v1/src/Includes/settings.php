<?php

/** Fichero de settings generales de la aplicación */

// Configuración general
global $appSettings;

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
