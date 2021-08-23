<?php

//  Define los puntos de acceso según el tipo de perfil de usuario

define("JWT_SECRET_KEY", 'U_97$5TwNA3N8$qYV4vKK_#k');

define("security", [
        "ROLE_SUDO"=>[
            "dashboard"=>"sudo"
        ],
        "ROLE_ADMIN"=>[
            "dashboard"=>"admin"
        ],
        "ROLE_DPD"=>[
            "dashboard"=>"dpd"
        ],
        "ROLE_REVDOC"=>[
            "dashboard"=>"revdoc"
        ],
        "ROLE_ADMINFINCAS"=>[
            "dashboard"=>"adminfincas",
            "js"=>[
                "dashboard",
                "comunidades"
            ]
        ],
        "ROLE_CONTRATISTA"=>[
            "dashboard"=>"contratista"
        ],
        "ROLE_EMPLEADO"=>[
            "dashboard"=>"empleado"
        ]
    ]
);