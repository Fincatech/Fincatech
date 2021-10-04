<?php

global $database;

$database = [
    'host' => "localhost", // Ionos: db5005122439.hosting-data.io
    'port' => "",
    'user' => "root", // Ionos: dbu1836072
    'password' => "root", // Ionos: wemrep-ryrZij-2habpo
    'schema' => "fincatech", // Ionos: dbs4285272
    'config' => [
        'pageresults' => 30, // -1 indica que no tiene límite
        'createmissingtables' => true
    ],
];