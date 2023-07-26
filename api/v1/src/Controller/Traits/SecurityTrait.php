<?php

namespace HappySoftware\Controller\Traits;

use Firebase\JWT\JWT;

trait SecurityTrait{

    /** Comprueba si el usuario autenticado es de tipo sudo */
    public function isSudo()
    {
        return ( $this->getLoggedUserRole() == 'ROLE_SUDO' );
    }

    /** Comprueba si el usuario autenticado es un admin de fincas */
    public function isAdminFincas()
    {
        return ( $this->getLoggedUserRole() == 'ROLE_ADMINFINCAS' );
    }

    public function isContratista()
    {
        return ( $this->getLoggedUserRole() == 'ROLE_CONTRATISTA' );
    }

    /** Comprueba si es un técnico de certificados digitales el usuario que está en sesión */
    public function isTecnicoRao()
    {
        return ( $this->getLoggedUserRole() == 'ROLE_REVCERT' );
    }

    // TODO:
    public function isDPD()
    {
        return ( $this->getLoggedUserRole() == 'ROLE_DPD' );
    }

    public function getJWTUserData()
    {
        return $this->checkSecurity();
    }

    public function getLoggedUserRole()
    {
        if($this->isLogged())
        {
            $usuarioRol = get_object_vars( $this->getJWTUserData()['data']->userData )['role'];
            return $usuarioRol;
        }else{
            throw new \Exception('No tiene acceso');
            die();
        }
    }

    /** Devuelve el nombre del usuario autenticado en el sistema */
    public function getLoggedUserName()
    {
        if($this->isLogged()){
            $userName = get_object_vars( $this->getJWTUserData()['data']->userData )['nombre'];
        }else{
            $userName = "Master Fincatech"; // Se establece el SUDO como usuario
        }
        return $userName;
    }

    public function getLoggedUserId()
    {
        if($this->isLogged()){
            $usuarioId = get_object_vars( $this->getJWTUserData()['data']->userData )['id'];
        }else{
            $usuarioId = -1; // Se establece el SUDO como usuario
        }
        return $usuarioId;
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
                'email' => 'admin@fincatech.es',
                'role' => 'ROLE_SUDO'
            ]
        );        

        return $tokenContent;

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