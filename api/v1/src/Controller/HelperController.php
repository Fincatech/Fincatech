<?php

namespace HappySoftware\Controller;

use Firebase\JWT\JWT;
use HappySoftware\Controller\Traits;
use DI\ContainerBuilder;

class HelperController
{

    use \HappySoftware\Controller\Traits\SecurityTrait;

    public static function getLoggedUserInfo()
    {
        $userData = null;
        if(isset( $_COOKIE['FINCATECHTOKEN'] ) )
        {
            $tokenJWT = $_COOKIE['FINCATECHTOKEN'];
            $userData = get_object_vars(JWT::decode($tokenJWT, JWT_SECRET_KEY, array('HS512')))['userData'];
        }else{
            $userData = [];
            $userData['id'] = null;
            $userData['login'] = null;
            $userData['nombre'] = null;
            $userData['email'] = null;
            $userData['role'] = null;
        }

        return $userData; //['user'];//['userdata'];

    } 

    public static function successResponse($data, $codeResponse = 200)
    {
        // self::getLoggedUserInfo();
        // HelperController::getLoggedUserInfo();
        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['user'] = self::getLoggedUserInfo();
        $responseData['status']['response'] = 'ok';
        $responseData['status']['code'] = $codeResponse;

        if(isset($responseData['data']['total']))
        {
          //  $responseData['draw'] = 1; // Draw count para prevenir XSS ataques
            $responseData['recordsTotal'] = $responseData['data']['total'];
            $responseData['recordsFiltered'] = $responseData['data']['total'];
        }

        return json_encode($responseData);
    }

    public static function errorResponse($data, $mensaje, $codeResponse)
    {
        $responseData['data'] = $data;
        $responseData['status'] = [];
        $responseData['status']['response'] = 'error';
        $responseData['status']['error'] = $mensaje;
        $responseData['status']['code'] = $codeResponse;
        return json_encode($responseData);
    }

    /** Genera un password aleatorio de n caracteres */
    public static function GenerateRandomPassword($longitud = 8)
    {

        $comb = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $shfl = str_shuffle($comb);
        $pwd = substr($shfl, 0, $longitud);
        return $pwd;
    }

    /** Devuelve la fecha actual basándonos en el timezone de españa */
    public static function DateNow($mysqlFormat = false)
    {
        date_default_timezone_set('Europe/Madrid');
        if($mysqlFormat)
        {
            return date('Y-m-d H:i:s');
        }else{
            return date('d-m-Y H:i:s');
        }
            
    }

}