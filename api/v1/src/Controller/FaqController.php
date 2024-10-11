<?php

/*
  Controller: Certificados digitales
*/

namespace Fincatech\Controller;

use Fincatech\Controller\FrontController;
use Fincatech\Model\FaqModel;

use Happysoftware\Controller\HelperController;
use Happysoftware\Controller\Traits;
use Happysoftware\Controller\Traits\MailTrait;
use HappySoftware\Database\DatabaseCore;
use PHPUnit\TextUI\Help;

class FaqController extends FrontController{

    public $FaqModel;
    public $securityDisabled = true;
    public $_logMessage;

    public function __construct($params = null)
    {
        parent::__construct(); 

        $this->InitModel('Faq', $params);
    }
    /**
     * Create Certificado digital
     */
    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->FaqModel->Create($entidadPrincipal, $datos);
    }

    /**
     * Update
     */
    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->FaqModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->FaqModel->getSchema();
    }

    public function Delete($id)
    {
        //  Comprobamos que sea un usuario autenticado
        if($this->isLogged())
        {
            return $this->FaqModel->Delete($id);
        }else{
            return 'error';
        }
    }

    public function Get($id)
    {
        return $this->FaqModel->Get($id);
    }

    public function List($params = null)
    {
        $search['searchfields'] =[];
        $search['searchvalue'] = "'" . $params['type'] . "'";
        $search['searchfields'][0]['field'] = "tipo";
        $search['searchfields'][0]['operator'] = "=";

       return $this->FaqModel->List($search);
    }

}