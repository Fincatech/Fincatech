<?php

namespace HappySoftware\Controller\Traits;

trait UtilsTrait{

    /** Devuelve el valor para un parámetro del querystring, si no lo encuentra devuelve false */
    function getUriValue($queryParameter)
    {

        //  Si no existe el get devolvemos false
        if(!isset($_GET[$queryParameter]))
        {
            return false;
        }else{
            return $_GET[$queryParameter];
        }

    }

}