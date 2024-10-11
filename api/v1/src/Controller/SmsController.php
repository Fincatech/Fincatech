<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// use Fincatech\Model\SmsModel;
use HappySoftware\Model\Model;
use \Fincatech\Model\SmsModel;
use HappySoftware\Controller;

class SmsController extends FrontController{

    protected SmsModel $SmsModel;
    
    public function __construct($params = null)
    {
       $this->InitModel('sms', $params);
    }

    public function Create($entidadPrincipal, $data)
    {
        //  Llamamos al método de crear
        $this->SmsModel->setIdUsuario($data['idusuario'])
            ->setPhone($data['phone'])
            ->setMessage($data['message'])
            ->setContractFileId(null)
            ->setMensajeCertificadoId($data['mensajecertificadoid'])
            ->setContrato(intval($data['contrato']));

        $this->SmsModel->_Save();
        return $this->SmsModel->Id();
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
        $data['Sms'] = $this->SmsModel->List($params, true);
        return $data;
    }

}