<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RolModel;

class RolController extends FrontController{

    private $rolModel;

    public function __construct($params = null)
    {
        $this->InitModel('Rol', $params);
    }

    public function Get($id)
    {
        return $this->RolModel->Get($id);
    }

    public function List($params = null)
    {

       return $this->RolModel->List($params);
    }

}