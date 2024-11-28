<?php

namespace Fincatech\Controller;

use \HappySoftware\Controller\HelperController;
use \HappySoftware\Database\DatabaseCore;

use Fincatech\Model\RemesaDevolucionModel;

use Fincatech\Entity\RemesaDevolucion;
use Fincatech\Entity\RemesaDetalle;


class RemesaDevolucionController extends FrontController{

    //  Modelo
    public $RemesaDevolucioneModel;

    private $remesaDetalleEntity;

    //  Entidad
    public RemesaDevolucionModel $RemesaDevolucion;

    public function __construct($params = null, $id = null)
    {
        $this->RemesaDevolucioneModel = new RemesaDevolucionModel($params);
        if(!is_null($id)){
            $this->Get($id);
        }
    }

    /**
     * Crea el detalle de la remesa asociándolo a la remesa principal
     */
    public function CreateDevolucion(RemesaDevolucion $data)
    {
        $remesaDevolucionId = -1;
        $dataToSave = get_object_vars($data);
        $entidad = strtolower($this->RemesaDevolucioneModel->getEntidad());

        //  Creamos el registro en bbdd
        $id = $this->RemesaDevolucioneModel->Create($entidad, $dataToSave);

        if($id['id'] > 0){
            $this->RemesaDevolucioneModel->RemesaDevolucion->id = $id['id'];
            $data->id = $id['id'];
            $remesaDevolucionId = $data->id;
        }
        
        return $remesaDevolucionId;
    }

    public function Create($entidadPrincipal, $datos){}

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RemesaDevolucioneModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RemesaDevolucioneModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RemesaDevolucioneModel->Delete($id);
    }

    public function Get($id)
    {
        $this->RemesaDevolucioneModel = new RemesaDevolucionModel();
        $data = $this->RemesaDevolucioneModel->Get($id);
        $data = [];

       
        // return $data;
    }

    // public function GetByUniqueId($uniqueId)
    // {
    //     $remesaDetalleId = $this->RemesaDevolucioneModel->GetIdByUniqueId($uniqueId);
    //     if(count($remesaDetalleId) > 0){
    //         $this->Get($remesaDetalleId[0]['id']);            
    //     }
    // }

    public function List($params = null)
    {
       return $this->RemesaDevolucioneModel->List($params);
    }

    /**
     * Comprueba si existe ya una devolución realizada para el recibo y remesa asociada
     */
    public function ExistsByIdRemesaAndIdRecibo(RemesaDetalle $remesaDetalle):bool
    {
        $this->RemesaDevolucioneModel->RemesaDevolucion->idremesa = $remesaDetalle->idremesa;
        $this->RemesaDevolucioneModel->RemesaDevolucion->idremesadetalle = $remesaDetalle->id;
        $devolucion = $this->RemesaDevolucioneModel->GetByIdRemesaAndIdRemesaDetalle();
        if(count($devolucion) > 0){
            return true;
        }else{
            return false;
        }

    }

}