<?php

namespace Fincatech\Model;

use Fincatech\Entity\Facturacion;

class InvoiceDetailModel extends \HappySoftware\Model\Model{

    private $tabla = 'invoicedetail';

    //  ID
    public function Id(){return $this->id;}
    public function setId($value){
        $this->id = $value;
        return $this;
    }

    public $id;
    private $idservicio;
    private $unit_price_tax_inc;
    private $unit_price_comunidad;
    private $detail;
    private $idinvoice;
    private $unit_price_tax_exc;
    private $quantity;
    private $tax_rate;
    private $total_taxes_exc;
    private $total_taxes_inc;
    public  $created;
    public  $updated;
    public  $usercreate;
    private $detailLines = [];

    /**
     * Sets ID Factura
     */
    public function SetIdInvoice($value)
    {
        $this->idinvoice = $value;
        return $this;
    }
    /**
     * ID de la factura
     */
    public function IdInvoice(){return $this->idinvoice;}   
    public function IdServicio(){return $this->idservicio;}
    public function SetIdServicio($value){
        $this->idservicio = $value;
        return $this;
    }
    
    public function Detail(){return $this->detail;}
    public function SetDetail($value){
        $this->detail = $value;
        return $this;
    }

    public function UnitPriceTaxExc(){return $this->unit_price_tax_exc;}
    public function SetUnitPriceTaxExc($value){
        $this->unit_price_tax_exc = $value;
        return $this;
    }

    public function UnitPriceTaxInc(){return $this->unit_price_tax_inc;}
    public function SetUnitPriceTaxInc($value){
        $this->unit_price_tax_inc = $value;
        return $this;
    }
    
    public function UnitPriceComunidad(){ return $this->unit_price_comunidad; }
    public function SetUnitPriceComunidad($value){
        $this->unit_price_comunidad = $value;
        return $this;
    }

    public function Quantity(){return $this->quantity;}
    public function SetQuantity($value){
        $this->quantity = $value;
        return $this;
    }

    public function TaxRate(){return $this->tax_rate;}
    public function SetTaxRate($value){
        $this->tax_rate = $value;
        return $this;
    }

    public function TotalTaxesExc(){return $this->total_taxes_exc;}
    public function SetTotalTaxesExc($value){
        $this->total_taxes_exc = $value;
        return $this;
    }

    public function TotalTaxesInc(){return $this->total_taxes_inc;}
    public function SetTotalTaxesInc($value){
        $this->total_taxes_inc = $value;
        return $this;
    }

    //  Created
    public function Created(){return $this->created;}
    public function setCreated($value){
        $this->created = $value;
        return $this;
    } 

    //  Updated
    public function Updated(){return $this->updated;}
    public function setUpdated($value){
        $this->updated = $value;
        return $this;
    } 

    //  UserCreate
    public function UserCreate(){return $this->usercreate;}
    public function setUserCreate($value){
        $this->usercreate = $value;
        return $this;
    }     


    /**
     * Líneas de detalle
     */
    public function AddDetailLine($value){$this->detailLines[] = $value;}
    public function DetailLines(){return $this->detailLines;}
    /**
     * Inicializa el detalle
     */
    public function ClearDetailLines(){
        $this->detailLines = [];
        return $this;
    }

    private $entidad = 'InvoiceDetail';

    private $tablasSchema = array('InvoiceDetail');

    /**
     * @var \Fincatech\Entity\Invoice
     */
    public $invoice;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function Get($id)
    {
        //  Recuperamos el Usuario
        $usuario = parent::Get($id);

        //  Rellenamos el modelo
        return $usuario;
    }

    /**
     * Guarda en bbdd el detalle de una línea de factura
     */
    public function _Save()
    {
        $data = [];
        $data['idinvoice'] = $this->IdInvoice();
        $data['idservicio'] = $this->IdServicio();
        $data['detail'] = $this->Detail();
        $data['unit_price_tax_inc'] = $this->UnitPriceTaxInc();
        $data['unit_price_tax_exc'] = $this->UnitPriceTaxExc();
        $data['quantity'] = $this->Quantity();
        $data['tax_rate'] = $this->TaxRate();
        $data['total_taxes_exc'] = $this->TotalTaxesExc();
        $data['total_taxes_inc'] = $this->TotalTaxesInc();
        $data['unit_price_comunidad'] = $this->UnitPriceComunidad();
        
        return $this->Create($this->entidad, $data);
    }


    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}