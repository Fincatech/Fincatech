<?php

namespace Fincatech\Controller;

use Fincatech\Controller;
use Fincatech\Controller\Traits;
use HappySoftware\Controller\HelperController;

class HistoricoController extends FrontController
{

    public function __construct($params = null){
        $this->InitModel('Historico', $params);
    }

    /** Genera una nueva entrada en el histórico del sistema */
    public function Create($entidadPrincipal, $datos)
    {
        //  Primero generamos el registro en el histórico general
        $idHistorico = $this->HistoricoModel->Create($entidadPrincipal, $datos);
        return $idHistorico;
    }

    /** TODO: Obtiene un registro del histórico */
    public function Get($datos)
    {
        $idHistorico = $datos['id'];
        $destinoHistorico = $datos['destino'];

    }

    /** TODO: Lista el histórico de la entidad que se solicite */
    public function List($filter = null)
    {
        return $this->HistoricoModel->List($filter);
    }

    /** Recupera el historial de un requerimiento por su ID de relación */
    public function GetHistoricoRequerimiento($idRelacionRequerimiento, $entidad)
    {
        return HelperController::successResponse($this->HistoricoModel->GetHistoricoRequerimiento($idRelacionRequerimiento, $entidad), 200);
    }

    /**
     * Comprueba si tiene histórico un requerimiento mediante su id de relación
     * @param int idRelacionRequerimiento
     * @param string entidad
     * @return boolean True | False
     */
    public function TieneHistorico($idRelacionRequerimiento, $entidad)
    {
        return $this->HistoricoModel->TieneHistorico($idRelacionRequerimiento, $entidad);
    }

}