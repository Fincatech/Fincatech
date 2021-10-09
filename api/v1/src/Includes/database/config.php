<?php

global $database;

$database = [
    'host' => "51.68.123.97", //"51.68.123.97", // Ionos: db5005122439.hosting-data.io
    'port' => "3306",
    'user' => "fincaroot", // Ionos: dbu1836072
    'password' => "2y*7P1qy", // Ionos: wemrep-ryrZij-2habpo
    'schema' => "fincatech", // Ionos: dbs4285272
    'config' => [
        'pageresults' => 30, // -1 indica que no tiene límite
        'createmissingtables' => true
    ],
];