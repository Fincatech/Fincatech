<?php

//  Define los puntos de acceso según el tipo de perfil de usuario
define("JWT_SECRET_KEY", 'U_97$5TwNA3N8$qYV4vKK_#k');

define("security", [
        "ROLE_SUDO"=>[
            "folder"=>"sudo",
            "titulo" => "Súper Admin",
            "menulateral"=> false,
            "js"=>[
                "administrador",
                "comunidades",
                "dpd",
                "documental",
                "empresa",
                "empleado",
                'informevaloracionseguimiento',
                "requerimiento",
                "servicios",
                "spa",
                "sudo",
                "usuario",
            ] ,
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],
         
        ],
        "ROLE_ADMIN"=>[
            "folder"=>"admin",
            "titulo" => "Admin",
            "menulateral"=> false,
            "create" => "all"
        ],
        "ROLE_DPD"=>[
            "folder"=>"dpd",
            "titulo" => "Control documental DPD",
            "menulateral"=> false,
            "js"=>[
                "dpd",
                "empresa",
                "empleado",
                'informevaloracionseguimiento',                
                "notasinformativas",
                "requerimiento",
            ],
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],            
        ],
        "ROLE_REVDOC"=>[
            "folder"=>"revdoc",
            "titulo" => "Revisión documental",
            "menulateral"=> false,
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],            
        ],
        "ROLE_ADMINFINCAS"=>[
            "folder"=>"adminfincas",
            "titulo" => "Administrador de fincas",
            "menulateral"=> true,
            "js"=>[
                "dashboard",
                "comunidades",
                "dpd",
                "empresa",
                "empleado",
                'informevaloracionseguimiento',
                "notasinformativas",
                "documental",
                "requerimiento",
                "servicios",
            ],
            "permissions" => [
                "create" => ['comunidad', 'dpd'],
                "delete" => ["comunidad"],
                "update" => "all"
            ],            
        ],
        "ROLE_CONTRATISTA"=>[
            "folder"=>"contratista"
        ],
        "ROLE_EMPLEADO"=>[
            "folder"=>"empleado",
            "menulateral"=> false
        ],
        "ROLE_LOGIN"=>[
            "folder"=>"login",
            "menulateral"=> false,
            "titulo" => "Login Fincatech"
        ]
    ]
);