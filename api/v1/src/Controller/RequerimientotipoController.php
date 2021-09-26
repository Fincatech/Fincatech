<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RequerimientotipoModel;

class RequerimientotipoController extends FrontController{

    private $requerimientotipoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Requerimientotipo', $params);
    }

    public function Get($id)
    {
        return $this->RequerimientotipoModel->Get($id);
    }

    public function List($params = null)
    {

       return $this->RequerimientotipoModel->List($params);
    }

}