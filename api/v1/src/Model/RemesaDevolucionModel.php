<?php

namespace Fincatech\Model;
use HappySoftware\Model\Model;

use Fincatech\Entity\RemesaDevolucion;
use HappySoftware\Controller\HelperController;

class RemesaDevolucionModel extends \HappySoftware\Model\Model{

    private $entidad = 'RemesaDevolucion';
    private $tablasSchema = array("remesadevolucion", 'remesa');

    /**
     * @var \Fincatech\Entity\RemesaDevolucion
     */
    public $RemesaDevolucion;

    public function __construct($params = null, $id = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

        $this->RemesaDevolucion  = new RemesaDevolucion();

        if(!is_null($id)){
            $this->Get($id);
        }
    }

    /**
     * Recupera el detalle de la devolución por su ID
     */
    public function Get($id)
    {

        $remesa = parent::Get($id);
        //  Comprobamos si tiene información, si la tiene, rellenamos el modelo
        $this->RemesaDevolucion = new RemesaDevolucion();

        if(count($remesa['RemesaDevolucion']) > 0)
        {
            $remesa = $remesa['RemesaDevolucion'][0];
            $this->RemesaDevolucion->id = $id;
            $this->RemesaDevolucion->idRemesaDetalle = (int)$remesa['idremesadetalle'];
            $this->RemesaDevolucion->dateReturned = ((int)$remesa['']);
            $this->RemesaDevolucion->codigo = $remesa['codigo'];
            $this->RemesaDevolucion->message = $remesa['message'];
            $this->RemesaDevolucion->amount = $remesa['amount'];
            $this->RemesaDevolucion->created = $remesa['created'];
            $this->RemesaDevolucion->usercreate = $remesa['usercreate'];
        }

    }

    public function Create($entidadPrincipal, $data)
    {
        return parent::Create($this->entidad, $data);
    }

    public function CreateRemesaDevolucion(RemesaDevolucion $remesaDevolucion){
        //  Construimos el almacenamiento


        // $id = parent::Create($this->entidad, $data);
        // //  Validamos que haya insertado
        // if(count($id) > 0){
        //     $id = $id['id'];
        // }else{
        //     return false;
        // }

        // //  Validamos que haya insertado
        // if(intval($id) > 0){
        //     //  Seteamos el ID y devolvemos la entidad 
        //     $remesaDevolucion->id = $id;
        // }
    }

    public function _Update(){

    }

    /**
     * Recupera la devolución por el id de la remesa y el id del detalle
     */
    public function GetByIdRemesaAndIdRemesaDetalle()
    {
        $sql = "select * from " . strtolower($this->entidad) . " where idremesa = " . $this->RemesaDevolucion->idremesa . " and idremesadetalle = " . $this->RemesaDevolucion->idremesadetalle;
        return $this->query($sql);
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}