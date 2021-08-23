<?php
namespace App\Controller;

use App\Controller;
use App\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EntityHelperController
{

    public function __construct()
    {

    }

    /** Mapea una entidad a una clase */
    public static function mapearEntidad($jsonData, $nombreEntidad, &$modelo)
    {

        //  Recorremos todo el objeto que vamos a mapear
        foreach($jsonData as $key => $value)
        {
            $nombreMetodo = "set".ucfirst($key);
            //  Por cada clave rellenamos el modelo
            $modelo->$nombreMetodo($value);
        }

    }

}