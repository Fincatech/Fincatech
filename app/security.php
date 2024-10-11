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
                "certificadodigital",
                "comunidades",
                "contratista",
                "dpd",
                "documental",
                "empresa",
                "empleado",
                "facturacion",
                'informevaloracionseguimiento',
                'notasinformativas',
                'proveedor',
                "requerimiento",
                "rgpd",
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
                "administrador",
                "comunidades",
                "dpd",
                "empresa",
                "empleado",
                'informevaloracionseguimiento',                
                "notasinformativas",
                "documental",
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
            "js" =>[
                "documental",
                "tecnico",
            ],
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],            
        ],
        "ROLE_REVCERT"=>[
            "folder"=>"revcert",
            "titulo" => "Revisión documental",
            "menulateral"=> false,
            "js" =>[
                "documental",
                "tecnicocertificado",
                "certificadodigital",
            ],
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
                "adminfincas",
                "dashboard",
                "certificadodigital",
                "comunidades",
                "dpd",
                "documental",
                "empresa",
                "empleado",
                'informevaloracionseguimiento',
                "notasinformativas",
                "requerimiento",
                "rgpd",
                "servicios",
                "usuario",
            ],
            "permissions" => [
                "create" => ['comunidad', 'dpd'],
                "delete" => ["comunidad"],
                "update" => "all"
            ],            
        ],
        "ROLE_CONTRATISTA"=>[
            "folder"=>"contratista",
            "titulo" => "Empresa contratista",
            "menulateral" => true,
            "js" =>[
                "contratista",
                "documental",
                "empresa",
                "empleado",
                'servicios',
                'comunidades',
            ]
        ],
        "ROLE_EMPLEADO"=>[
            "folder"=>"empleado",
            "menulateral"=> false
        ],
        "ROLE_REVCAE"=>[
            "folder"=>"revcae",
            "titulo" => "Revisión documental cae",
            "menulateral"=> false,
            "js" =>[
                "tecnicocae",
            ],
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],            
        ],    
        "ROLE_SEGPROV"=>[
            "folder"=>"segprov",
            "titulo" => "Seguimiento Proveedores",
            "menulateral"=> false,
            "js" =>[
                "seguimientoproveedores",
            ],
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],            
        ],              
        "ROLE_LOGIN"=>[
            "folder"=>"login",
            "menulateral"=> false,
            "titulo" => "Login Fincatech"
        ],
        "ROLE_FACTURACION" => [
            "folder" => "facturacion",
            "titulo" => "Facturación",
            "menulateral" => false,
            "js" => [
                "administrador",
                "comunidades",
                "facturacion",
                "sudo",
            ],
            "permissions" => [
                "create" => "all",
                "delete" => "all",
                "update" => "all"
            ],                        
        ]
    ]
);