<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\ServiciosModel;
use HappySoftware\Controller\HelperController;

class ServiciosController extends FrontController{

    private $serviciosModel;

    public function __construct($params = null)
    {
        $this->InitModel('Servicios', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->ServiciosModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        if(isset($datos['type']))
        {
            if($datos['type'] === 'bulk')
            {
                $this->processBulkUpdate($datos['servicesdata']);
                return 'ok';
            }
        }else{
            return $this->ServiciosModel->Update($entidadPrincipal, $datos, $usuarioId); 
        }
    }

    /** Comprueba si una comunidad ya tiene un servicio asociado */
    private function comunidadTieneServicio($idComunidad, $idServicio)
    {
        return ($this->ServiciosModel->getRepositorio()->ExisteRegistro('comunidadservicioscontratados', "idcomunidad = $idComunidad and idservicio = $idServicio"));
    }

    /** Construye el cuerpo que se va a enviar para actualizar o insertar
     * @param object $data Dato original que se va a procesar
     * @return object Devuelve un array asociativo con los datos ya procesados
     */
    private function constructServiceDataToSave($data)
    {

        $serviceData = [];

        $serviceData['idcomunidad'] = $data['idcomunidad'];
        $serviceData['id'] = $data['id'];
        $serviceData['idservicio'] = $data['idtiposervicio'];
        $serviceData['contratado'] = isset($data['contratado']) ? ($data['contratado'] === true ? 1 : 0) : 0;
        $serviceData['precio'] = isset($data['precio']) ? $data['precio'] : 0;
        $serviceData['preciocomunidad'] = isset($data['preciocomunidad']) ? $data['preciocomunidad'] : 0;
        $serviceData['mesfacturacion'] = isset($data['mesfacturacion']) ? ($data['mesfacturacion'] !== '' ? $data['mesfacturacion'] : 12) : 12;

        return $serviceData;

    }

    private function processBulkUpdate($bulkData)
    {
        if(is_array($bulkData) && @count($bulkData) > 0)
        {
            //  Por cada uno de los servicios actualizamos la información
            foreach($bulkData as $servicio)
            {

                //  Si el id es 0 quiere decir que no está presente en la bbdd y por tanto hay que añadirlo
                    $servicioComunidad = $this->constructServiceDataToSave($servicio);

                //  Comprobamos si existe para actualizar o insertar

                if(($this->comunidadTieneServicio($servicioComunidad['idcomunidad'], $servicioComunidad['idservicio'])))
                {
                    //  Hay que recuperar el ID
                    $this->ServiciosModel->Update('comunidadservicioscontratados', $servicioComunidad, $servicioComunidad['id']);
                }else{
                    //  Hay que quitar el ID
                    unset($servicioComunidad['id']);
                    $this->ServiciosModel->Create('comunidadservicioscontratados', $servicioComunidad);
                }

            }

        }

        return 'ok';
    }

    public function getSchemaEntity()
    {
        return $this->ServiciosModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ServiciosModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ServiciosModel->Get($id);
    }

    public function List($params = null, $useLoggedUserId = false)
    {
       return $this->ServiciosModel->List($params, $useLoggedUserId);
    }

}