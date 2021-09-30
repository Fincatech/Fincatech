<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EmpresatipoModel;

class EmpresatipoController extends FrontController{

    private $empresatipoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Empresatipo', $params);
    }

    public function Get($id)
    {
        return $this->EmpresatipoModel->Get($id);
    }

    public function List($params = null)
    {

       return $this->EmpresatipoModel->List($params);
    }

}