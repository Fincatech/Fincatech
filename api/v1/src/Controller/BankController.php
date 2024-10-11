<?php

namespace  Fincatech\Controller;


use Fincatech\Model\BankModel;
use HappySoftware\Controller\HelperController;
use stdClass;

class BankController extends FrontController{

    public $BankModel;

    private $bank;

    public function __construct($params = null)
    {
        $this->InitModel('Bank', $params);
    }

    /**
     * Create user
     * @param string $entidadPrincipal. Entity Name
     * @param json $datos. JSON Object with values to create
     */
    public function Create($entidadPrincipal, $datos)
    {
        if(isset($datos['iban'])){
            $datos['iban'] = HelperController::NormalizeIBAN($datos['iban']);
        }
        return $this->BankModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        if(isset($datos['iban'])){
            $datos['iban'] = HelperController::NormalizeIBAN($datos['iban']);
        }
        return $this->BankModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->BankModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->BankModel->Delete($id);
    }

    public function Get($id)
    {
        $this->bank = $this->BankModel->Get($id);
        return $this->bank;
    }

    public function List($params = null)
    {
       return $this->BankModel->List($params);
    }

    public function GetBicByIBAN($iban)
    {
        $entidad = substr($iban, 0,4);
        $banco = $this->BankModel->BicByIBAN($iban);
        //  Validamos que sea correcto y haya encontrado algo
        
    }

}