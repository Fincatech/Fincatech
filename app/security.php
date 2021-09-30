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
                "empresa",
                "empleado",
                'informevaloracionseguimiento',
                "requerimiento",
                "servicios",
                "spa",
                "sudo",
                "usuario",
            ]            
        ],
        "ROLE_ADMIN"=>[
            "folder"=>"admin",
            "titulo" => "Admin",
            "menulateral"=> false
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
            ] 
        ],
        "ROLE_REVDOC"=>[
            "folder"=>"revdoc",
            "titulo" => "Revisión documental",
            "menulateral"=> false
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
            ]
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