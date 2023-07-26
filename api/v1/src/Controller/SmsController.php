<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\SmsModel;

class SmsController extends FrontController{

    private $smsModel;
    public $SmsModel;

    public function __construct($params = null)
    {
        $this->InitModel('sms', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->SmsModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->SmsModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->SmsModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->SmsModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->SmsModel->Get($id);
    }

    public function List($params = null, $useLoggedUserId = false)
    {
        return $this->SmsModel->List($params, true);
    }

}