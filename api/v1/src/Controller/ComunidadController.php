<?php
namespace Fincatech\Controller;


use Fincatech\Model\ComunidadModel;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\SecurityTrait;

class ComunidadController extends FrontController{

    use SecurityTrait;

    private $comunidadModel;

    public function __construct($params = null)
    {
        $this->InitModel('Comunidad', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        //  Si no está informado, cogemos el usuario autenticado en el sistema
        if(!isset($datos['usuarioId']))
            $datos['usuarioId'] = $this->getLoggedUserId();
        
        //  Si es el usuario sudo el que ha dado de alta la comunidad
        //  forzamos que se guarde en estado 'A' (Activada) para que lo apruebe el admin del sistema
        if($this->getLoggedUserRole() == 'ROLE_SUDO')
            $datos['estado'] = 'A';

        return $this->ComunidadModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->ComunidadModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->ComunidadModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ComunidadModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ComunidadModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ComunidadModel->List($params);
    }

    public function GetTable($params)
    {
        return $this->ComunidadModel->GetTable($params);
    }

}