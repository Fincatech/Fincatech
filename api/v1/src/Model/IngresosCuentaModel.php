<?php

namespace Fincatech\Model;

use Fincatech\Entity\IngresosCuenta;

class IngresosCuentaModel extends \HappySoftware\Model\Model{


    private $entidad = 'IngresosCuenta';
    private $tablasSchema = array('IngresosCuenta');

    /**
     * @var \Fincatech\Entity\IngresosCuenta
     */
    public $ingresoscuenta;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // public function Get($id)
    // {
    //     //  Recuperamos el Usuario
    //     $usuario = parent::Get($id);

    //     //  Rellenamos el modelo
    //     return $usuario;
    // }

    /**
     * Guarda en base de datos la información según los datos del modelo
     */
    public function _Save(IngresosCuenta $data)
    {

        $datos = [];
        $datos['concepto'] = $data->Concepto();
        $datos['idadministrador'] = $data->IdAdministrador();
        $datos['procesado'] = $data->Procesado();
        $datos['total'] = $data->Total();
        $datos['fechaingreso'] = $data->FechaIngreso();
        $datos['observaciones'] = $data->Observaciones();
        //  Si es una inserción
        if( $data->Id() <= 0 ){
            $this->Insert($datos, $data);
        }else{
            $this->Update($this->entidad, $data, $data->Id());
        }

    }

    private function Insert(array $datos, IngresosCuenta $data){
        $result = $this->Create($this->entidad, $datos);
        if((int)$result['id'] > 0){
            $data->SetId($result['id']);
        }else{
            $data->SetId(-1);
        }
    }

    /**
     * Método de búsqueda en el repositorio de Liquidaciones
     */
    public function Search($dataToSearch = null)
    {
        if(is_null($dataToSearch)){
            return parent::Search($this->SearchFields());
        }else{
            return parent::Search($dataToSearch);
        }
    }

}