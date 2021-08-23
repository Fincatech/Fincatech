<?php
namespace App\Controller;

use App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HelperController{

    public static function construirMensajeRespuestaError($mensajeError): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'mensaje' => $mensajeError, 'statuscode' => Response::HTTP_OK]);
    }

    public static function construirMensajeRespuestaOK($mensaje): JsonResponse
    {
        return new JsonResponse(['status' => $mensaje, 'statuscode' => Response::HTTP_OK ]);
    }

    public static function construirMensajeRespuestaDatos($datos): JsonResponse
    {
        return new JsonResponse(['status' => 'ok', 'data' => $datos, 'statuscode' => Response::HTTP_OK ], Response::HTTP_OK);
    }

    //  Recuperamos el usuario en sesión
    public static function getIdUsuarioActual(): ?int
    {
        $idUsuario = null;

        return $idUsuario;
    }

}