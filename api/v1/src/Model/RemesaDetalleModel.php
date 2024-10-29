<?php

namespace Fincatech\Model;

use Fincatech\Entity\RemesaDetalle;
use HappySoftware\Controller\HelperController;
use HappySoftware\Database\DatabaseCore;
use HappySoftware\Model\Model;

class RemesaDetalleModel extends \HappySoftware\Model\Model{

    private $entidad = 'RemesaDetalle';
    private $tablasSchema = array("remesadetalle", 'remesa');

    /**
     * @var \Fincatech\Entity\RemesaDetalle
     */
    public $remesa;

    public $remesaDetalle;

    public function __construct($params = null, $id = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

        if(!is_null($id)){
            $this->Get($id);
        }

    }

    /**
     * Recupera el detalle por su ID
     */
    public function Get($id): RemesaDetalle
    {

        $remesa = parent::Get($id);
        //  Comprobamos si tiene información, si la tiene, rellenamos el modelo
        $this->remesa = new RemesaDetalle();
        if(count($remesa['RemesaDetalle']) > 0)
        {
            $remesa = $remesa['RemesaDetalle'][0];
            $this->remesa->id = $id;
            $this->remesa->idremesa = (int)$remesa['idremesa'];
            $this->remesa->invoiceid = (int)$remesa['invoiceid'];
            $this->remesa->uniqid = $remesa['uniqid'];
            $this->remesa->descripcion = $remesa['descripcion'];
            $this->remesa->amount = $remesa['amount'];
            $this->remesa->customername = $remesa['customername'];
            $this->remesa->customerbic = $remesa['customerbic'];
            $this->remesa->customeriban = $remesa['customeriban'];
            $this->remesa->presentado = $remesa['presentado'];
            $this->remesa->estado = $remesa['estado'];
            $this->remesa->datereturned = $remesa['datereturned'];
            $this->remesa->updated = $remesa['updated'];
            $this->remesa->usercreate = $remesa['usercreate'];
            $this->remesa->created = $remesa['created'];
        }

        return $this->remesa;

    }

    /**
     * Devuelve el id de un recibo por su uniqueid
     */
    public function GetIdByUniqueId($uniqueId)
    {
        $uniqueId = DatabaseCore::PrepareDBString($uniqueId);
        $sql = "select id from " . strtolower($this->entidad) . " where uniqid = '" . $uniqueId . "'";
        return $this->query($sql);
    }

    /**
     * Crea el detalle de una remesa en bbdd
     */
    public function CreateDetalleRemesa(RemesaDetalle $remesaDetalle){

        $data = get_object_vars($remesaDetalle);

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
            $remesaDetalle->id = $id;
        }else{
            return false;
        }

        return $remesaDetalle;
    }

    public function _Update($remesa){

    }

    public function ListByRemesaId()
    {

    }

    /**
     * Recibos Devueltos
     */
    public function RecibosDevueltos()
    {
        $sql = "select 
            r.referencia, r.customername, 
            rd.id, rd.idremesa, rd.invoiceid, rd.descripcion, rd.amount, rd.customername as comunidad, rd.customeriban, rd.presentado, rd.datereturned, rd.created,
            rdev.codigo, rdev.message, rdev.datereturned as fechadevolucionbanco
        from 
	        remesa r, remesadetalle rd, remesadevolucion rdev
        where 
	        rd.idremesa = r.id and rd.estado = 'D' and rdev.idremesadetalle = rd.id";
        return $this->query($sql);
    }

    /**
     * Recibos cobrados
     */
    public function RecibosCobrados()
    {
        $sql = "select 
            r.referencia, r.customername, 
            rd.id, rd.idremesa, rd.invoiceid, rd.descripcion, rd.amount, rd.customername as comunidad, rd.customeriban, rd.presentado, rd.datereturned, rd.created
        from 
	        remesa r, remesadetalle rd
        where 
	        rd.idremesa = r.id and rd.estado = 'C' ";
        return $this->query($sql);
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}