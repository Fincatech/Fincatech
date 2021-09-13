<?php

//  Define los puntos de acceso según el tipo de perfil de usuario

define("JWT_SECRET_KEY", 'U_97$5TwNA3N8$qYV4vKK_#k');

define("security", [
        "ROLE_SUDO"=>[
            "dashboard"=>"sudo",
            "titulo" => "Súper Admin",
            "menulateral"=> false,
            "js"=>[
                "comunidades",
                "administrador",
                "usuario",
                "sudo"
            ]            
        ],
        "ROLE_ADMIN"=>[
            "dashboard"=>"admin",
            "titulo" => "Admin",
            "menulateral"=> false
        ],
        "ROLE_DPD"=>[
            "dashboard"=>"dpd",
            "titulo" => "Control documental DPD",
            "menulateral"=> false,
            "js"=>[
                "dashboard",
                "comunidades",
                "dpd",
            ] 
        ],
        "ROLE_REVDOC"=>[
            "dashboard"=>"revdoc",
            "titulo" => "Revisión documental",
            "menulateral"=> false
        ],
        "ROLE_ADMINFINCAS"=>[
            "dashboard"=>"adminfincas",
            "titulo" => "",
            "menulateral"=> true,
            "js"=>[
                "dashboard",
                "comunidades"
            ]
        ],
        "ROLE_CONTRATISTA"=>[
            "dashboard"=>"contratista"
        ],
        "ROLE_EMPLEADO"=>[
            "dashboard"=>"empleado",
            "menulateral"=> false
        ]
    ]
);