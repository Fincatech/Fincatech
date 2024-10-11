<?php

global $database;
global $appSettings;

if($appSettings['env'] == 'dev')
{
    $database = [
        'host' => "192.168.0.21", //"51.68.123.97", // Ionos: db5005122439.hosting-data.io
        'port' => "3306",
        'user' => "developer", // Ionos: dbu1836072
        'password' => "Eleggua@79@#!", // Ionos: wemrep-ryrZij-2habpo
        'schema' => "dbs4285272", // Ionos: dbs4285272
        'config' => [
            'pageresults' => 30, // -1 indica que no tiene límite
            'createmissingtables' => true
        ],
    ];
}else{

}

// $database = [
//     'host' => "db5005122439.hosting-data.io", //"51.68.123.97", // Ionos: db5005122439.hosting-data.io
//     'port' => "3306",
//     'user' => "dbu1836072", // Ionos: dbu1836072
//     'password' => "wemrep-ryrZij-2habpo", // Ionos: wemrep-ryrZij-2habpo
//     'schema' => "dbs4285272", // Ionos: dbs4285272
//     'config' => [
//         'pageresults' => 30, // -1 indica que no tiene límite
//         'createmissingtables' => true
//     ],
// ];