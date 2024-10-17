<?php

namespace HappySoftware\Controller\Traits;

use Firebase\JWT\JWT;

trait SecurityTrait{

    public function getJWTUserData()
    {
        return $this->checkSecurity();
    }

    /**
     * Devuelve el ID del usuario autenticado
     */
    public function getLoggedUserId()
    {
        $usuarioId = get_object_vars( $this->getJWTUserData()['data']->userData )['id'];
        return $usuarioId;
    }

    /**
     * Devuelve el rol que tiene asignado el usuario autenticado
     */
    public function getLoggedUserRole()
    {
        $usuarioRol = get_object_vars( $this->getJWTUserData()['data']->userData )['role'];
        return $usuarioRol;
    }

    /**
     * Comprueba si es un usuario autorizado
     */
    public function isAuhorizedUser()
    {
        $usuarioAutorizado = true;

        $authorized = get_object_vars( $this->getJWTUserData()['data']->userData );

        if(isset($authorized['authorized']))
        {
            $authorized = $authorized['authorized'];
            $usuarioAutorizado = ($authorized === -1 ? false : true);
        }

        return ($usuarioAutorizado);  
    }

    /** Comprueba si un usuario está autenticado en el sistema */
    public function checkSecurity()
    {
        $validation = [];
        if(!$this->isLogged())
        {
            $validation['status'] = false;
            $validation['error'] = "expiredtoken";
        }else{
            // $tokenContent = $this->createDebToken();
            // $tokenJWT = JWT::encode($tokenContent, JWT_SECRET_KEY);
            $tokenJWT = $this->getJWTToken();

            try{
                $dataObject = JWT::decode($tokenJWT, JWT_SECRET_KEY, array('HS512'));
                $validation['status'] = true;
                $validation['data'] = $dataObject;
            }catch(\Firebase\JWT\SignatureInvalidException $e){
                $validation['status'] = false;
                $validation['error'] = "invalidkey";
            }catch(\Firebase\JWT\ExpiredException $e){
                $validation['status'] = false;
                $validation['error'] = "expiredtoken";
            }        
        }

        return $validation;

    }

    /**
     * Recupera el JWTToken desde la cookie
     */
    private function getJWTToken()
    {
        return $_COOKIE['FINCATECHTOKEN'];
    }

    /** Comprueba si el usuario está autenticado en el sistema */
    public function isLogged()
    {
        //  Comprobamos si ya está autenticado en el sistema
        if(isset($_COOKIE['FINCATECHTOKEN']))
        {
            if($_COOKIE['FINCATECHTOKEN'] == '')
            {
            //  Borramos la cookie
                setcookie('FINCATECHTOKEN', time() - 3600);
                return false;
            }else{
                return  true;
            }
        }else{
            return false;
        }

    }

    public function createDebToken()
    {
        $currentTime = time();
        $limitTime = $currentTime - 3600;

        //  Para debug
        $tokenContent = array(
            'iat' => $currentTime, // Tiempo que inició el token
            'exp' => $limitTime, // Tiempo que expirará el token (+1 hora)
            'userData' => [ // información del usuario
                'id' => 1,
                'login' => 'admin',
                'email' => 'info@fincatech.es',
                'role' => 'ROLE_SUDO',
            ]
        );        

        return $tokenContent;

    }

    /** Devuelve si el usuario autenticado tiene permiso para crear */
    public function userCanCreate()
    {
        //  Recuperamos desde las settings si el rol tiene permiso
        //  para crear entidad, de ser así, renderizamos el botón de crear

    }

    /**
     * Devuelve la IP de conexión
     */
    public function GetUserIP() {
        // Comprueba si el usuario está detrás de un proxy
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP desde proxy compartido
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP pasada por el proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // IP directa del cliente
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    
        // En caso de múltiples IPs separadas por comas, toma la primera
        return explode(',', $ip)[0];
    }

    /*
        ROLE_SUDO
        ROLE_ADMIN
        ROLE_DPD
        ROLE_REVDOC
        ROLE_ADMINFINCAS
        ROLE_CONTRATISTA
        ROLE_EMPLEADO
    */

}