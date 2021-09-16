<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\ProvinciaModel;

class ProvinciaController extends FrontController{

    private $provinciaModel;

    public function __construct($params = null)
    {
        $this->InitModel('Provincia', $params);
    }

    public function Get($id)
    {
        return $this->ProvinciaModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ProvinciaModel->List($params);
    }

}