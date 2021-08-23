<?php

global $database;

$database = [
    'host' => "localhost",
    'port' => "",
    'user' => "root",
    'password' => "root",
    'schema' => "fincatech",
    'config' => [
        'pageresults' => 30, // -1 indica que no tiene límite
        'createmissingtables' => true
    ],
];