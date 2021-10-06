<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EmpresaModel;
use HappySoftware\Controller\HelperController;

class EmpresaController extends FrontController{

    private $empresaModel;

    public function __construct($params = null)
    {
        $this->InitModel('Empresa', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
            $result = $this->EmpresaModel->Create('Empresa', $datos);
            
        //  Tenemos que generar un nuevo usuario

        //  Para ello debemos instanciar el controller de usuario para pasarle los datos


        return $result;
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->EmpresaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->EmpresaModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->EmpresaModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->EmpresaModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->EmpresaModel->List($params);
    }

}