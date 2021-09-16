<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EstadoModel;

class EstadoController extends FrontController{

    private $estadoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Estado', $params);
    }

    public function Get($id)
    {
        return $this->EstadoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->EstadoModel->List($params);
    }

}