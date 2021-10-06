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

      //    Llamamos al método de crear
            $result = $this->EmpresaModel->Create('Empresa', $datos);

        // //  Generamos en primer lugar el objeto usuario de tipo contratista
        // //  FIXME: Esto hay que meterlo en el objeto del modelo de usuario y luego guardar
        //     $datosNuevoUsuario = [];
        //     $datosNuevoUsuario['nombre'] = $datos['razonsocial'];
        //     $datosNuevoUsuario['cif'] = $datos['cif'];
        //     $datosNuevoUsuario['direccion'] = $datos['direccion'];
        //     $datosNuevoUsuario['localidad'] = $datos['localidad'];
        //     $datosNuevoUsuario['codpostal'] = $datos['codpostal'];
        //     $datosNuevoUsuario['telefono'] = $datos['telefono'];
        //     $datosNuevoUsuario['emailcontacto'] = $datos['email'];
        //     $datosNuevoUsuario['email'] = $datos['email'];
        //     $datosNuevoUsuario['rolid'] = 6;
        //     $datosNuevoUsuario['password'] = md5('finca123456');
        //     $datosNuevoUsuario['estado'] = $datos[''];
        //     $datosNuevoUsuario['salt'] = '';

        // //  Recuperamos el ID del usuario para poder asignarlo a la hora de crear la empresa
        //     $idNuevoUsuario = $this->empresaModel->Create('Usuario', $datosNuevoUsuario);
        //     $datos['idusuario'] = $idNuevoUsuario['id'];

  

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