<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\DocumentalModel;
use Fincatech\Controller\FrontController;

use \Happysoftware\Controller\HelperController;
use \Happysoftware\Controller\Traits;

class DocumentalController extends FrontController{

    // use \Happysoftware\Controller\Traits\FilesTrait;
    private $documentalModel;

    public function __construct($params = null)
    {
        parent::__construct();
        $this->InitModel('Documental', $params);
    }

    /** Sube un requerimiento al tipo que corresponda */
    public function uploadRequerimiento($destino, $datos)
    {

        //  Validamos que no exista el requerimiento
            $relaciones = $this->DocumentalModel->getRelacionesTabla();

            $tablaDestino = $relaciones[ $datos['entidad'] ]['tabla'];
            $campoDestino = $relaciones[ $datos['entidad'] ]['campo'];

        //  Subimos el fichero a la plataforma
            $idFichero = $this->uploadFile($datos['fichero']['nombre'], $datos['fichero']['base64']);

            if($this->DocumentalModel->existeRequerimiento(
                $datos['idrequerimiento'], 
                $relaciones[ $datos['entidad'] ]['tabla'],
                $relaciones[ $datos['entidad'] ]['campo'], 
                $datos[ $relaciones[ $datos['entidad'] ]['campo'] ]))
            {
                return HelperController::successResponse($this->DocumentalModel->updateRequerimiento());
            }else{
                // Destino, idFichero, datos
                return HelperController::successResponse($this->DocumentalModel->createRequerimiento(
                    $idFichero,
                    $datos));
            }

    }

    /** Sube un requerimiento de RGPD a la tabla que corresponda */
    public function uploadRequerimientoRGPD($destino, $datos)
    {

        //  Subimos el fichero a la plataforma
            $idFichero = $this->uploadFile($datos['fichero']['nombre'], $datos['fichero']['base64']);

        //  TODO: Validar que no exista el requeririmiento en bbdd
        //  para eso analizamos el cuerpo de la petición para ver si viene informado el ID del requerimiento previamente subido

        //  Destino, idFichero, datos
            return HelperController::successResponse($this->DocumentalModel->createRequerimientoRGPD( $destino, $idFichero, $datos) );
    }

    public function getRepositorio()
    {
        return parent::GetHelperModel()->getRepositorio();
    }

    /** Actualiza el estado de un requerimiento */
    public function actualizarEstadoRequerimiento($datos)
    {
        $this->DocumentalModel->uploadRequerimiento($datos);
    }


    // public function Create($entidadPrincipal, $datos)
    // {
    //     //  Llamamos al método de crear
    //     return $this->DocumentalModel->Create($entidadPrincipal, $datos);
    // }

    // public function Update($entidadPrincipal, $datos, $usuarioId)
    // {
    //     return $this->DocumentalModel->Update($entidadPrincipal, $datos, $usuarioId); 
    // }

    // public function getSchemaEntity()
    // {
    //     return $this->DocumentalModel->getSchema();
    // }

    // public function Delete($id)
    // {
    //     return $this->DocumentalModel->Delete($id);
    // }

    // public function Get($id)
    // {
    //     return $this->DocumentalModel->Get($id);
    // }

    // public function List($params = null)
    // {
    //    return $this->DocumentalModel->List($params);
    // }

}