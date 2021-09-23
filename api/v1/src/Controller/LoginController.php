<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use HappySoftware\Controller\HelperController;

class LoginController extends FrontController{

    // public $model;

    public function __construct($params = null)
    {
        $this->InstantiateHelperModel();
    }

    public function checkLogin($params)
    {
        
        $userName = $this->model->getRepositorio()::PrepareDBString( $params['email'] );
        $userPassword = $this->model->getRepositorio()::PrepareDBString( $params['password'] );
        $userRemember = $this->model->getRepositorio()::PrepareDBString( $params['recordar'] );

        $sql = "
            SELECT 
                u.id, u.nombre, u.email, u.rolid, concat('ROLE_',r.alias) as rol
            FROM
                usuario u,
                rol r
            WHERE
                u.email = '$userName'
                    AND u.password = MD5('$userPassword')
                    AND r.id = u.rolid
                    and u.estado = 'A'
        ";

        $resultado = false;

        $result = $this->model->query($sql);

        $data = [];

        if(is_array($result))
        {
            if(count($result) > 0)
            {
                $resultado = true;
                $data['id'] = $result[0]['id'];
                $data['nombre'] = $result[0]['nombre'];
                $data['email'] = $result[0]['email'];
                $data['rolid'] = $result[0]['rolid'];
                $data['rol'] = strtoupper($result[0]['rol']);
            }
        }
        
        $data['check'] = $resultado;
        return HelperController::successResponse($data);
        
    }

}