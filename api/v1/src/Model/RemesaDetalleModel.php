<?php

namespace Fincatech\Model;

use Fincatech\Entity\RemesaDetalle;
use HappySoftware\Controller\HelperController;
use HappySoftware\Model\Model;

class RemesaDetalleModel extends \HappySoftware\Model\Model{

    private $entidad = 'RemesaDetalle';
    private $tablasSchema = array("remesadetalle", 'remesa');

    /**
     * @var \Fincatech\Entity\Remesa
     */
    public $remesa;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function Get($id){
        $data = parent::Get($id);
        //  Comprobamos si tiene información, si la tiene, rellenamos el modelo

    }

    public function CreateDetalleRemesa(RemesaDetalle $remesaDetalle){
        //  Construimos el almacenamiento
        $data = [];
        $data['idremesa'] = $remesaDetalle->IdRemesa();
        $data['invoiceid'] = $remesaDetalle->InvoiceId();
        $data['descripcion'] = $remesaDetalle->Description();
        $data['amount'] = $remesaDetalle->Amount();
        $data['customername'] = $remesaDetalle->CustomerName();
        $data['customerbic'] = $remesaDetalle->CustomerBIC();
        $data['customeriban'] = $remesaDetalle->CustomerIBAN();
        $data['uniqid'] = $remesaDetalle->UniqueId();
        $data['estado'] = $remesaDetalle->Estado();

        $id = parent::Create($this->entidad, $data);
        //  Validamos que haya insertado
        if(count($id) > 0){
            $id = $id['id'];
        }else{
            return false;
        }

        //  Validamos que haya insertado
        if(intval($id) > 0){
            //  Seteamos el ID y devolvemos la entidad 
            $remesaDetalle->SetId($id);
        }else{
            return false;
        }

        return $remesaDetalle;
    }

    public function _Update($remesa){

    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}