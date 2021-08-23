<?php

namespace HappySoftware\Controller\Traits;

use Firebase\JWT\JWT;

trait SecurityTrait{

    public function checkSecurity($jwt = null)
    {
        
        $tokenContent = $this->createDebToken();
        $tokenJWT = JWT::encode($tokenContent, JWT_SECRET_KEY);

        $validation = [];
        $validation['status'] = "";
        $validation['error'] = "";
        $validation['data'] = null;

        try{
            $dataObject = JWT::decode($tokenJWT, JWT_SECRET_KEY, array('HS256'));
            $validation['data'] = $dataObject;
            $validation['status'] = true;
        }catch(\Firebase\JWT\SignatureInvalidException $e){
            $validation['status'] = false;
            $validation['error'] = "invalidkey";
        }catch(\Firebase\JWT\ExpiredException $e){
            $validation['status'] = false;
            $validation['error'] = "expiredtoken";
        }

        return $validation;

    }

    public function createDebToken()
    {
        $currentTime = time();
        $limitTime = $currentTime + 3600;

        //  Para debug
        $tokenContent = array(
            'iat' => $currentTime, // Tiempo que inició el token
            'exp' => $limitTime, // Tiempo que expirará el token (+1 hora)
            'userData' => [ // información del usuario
                'id' => 1,
                'login' => 'admin',
                'email' => 'admin@fincatech.es',
                'role' => 'ROLE_ADMINFINCAS'
            ]
        );        

        return $tokenContent;

    }

}