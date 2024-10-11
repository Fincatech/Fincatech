<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class LiquidacionDetalle extends EntityHelper{

    private $tableName = 'liquidaciondetalle';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
    //  Propiedades de la entidad
    //  ID
    private int $_id = -1;
    //  ID liquidacion asociada
    private int $_idLiquidacion = -1;
    //  ID Factura
    private int $_idInvoice = -1;
    //  Id Comunidad
    private int $_idComunidad = -1;
    //  Nombre de la comunidad
    private string $_comunidad = '';
    //  ID del servicio liquidado
    private int $_idServicio = -1;
    //  PVP Comunidad
    private float $_pvpComunidad = 0;
    //  PVP Retorno
    private float $_pvpRetorno = 0;
    //  Total Taxes Exc
    private float $_totalTaxesExc = 0;
    //  Total Taxes Inc
    private float $_totalTaxesInc = 0;
    //  ID usuario creación
    private int $_usercreate;
    //  Fecha de creación
    private string $_created;
    //  Fecha de actualización
    private string $_updated;

    /**
     * ID del detalle
     */
    public function Id(){return $this->_id;}
    public function SetId(int|null $value){
        $this->_id = (int)$value; return $this;
    }

    /**
     * ID Liquidacion
     */
    public function IdLiquidacion(){ return $this->_idLiquidacion;}
    public function SetIdLiquidacion(int $value){
        $this->_idLiquidacion = $value;
        return $this;
    }

    /**
     * ID Invoice
     */
    public function IdInvoice(){ return $this->_idInvoice;}
    public function SetIdInvoice(int $value){
        $this->_idInvoice = $value;
        return $this;
    }

    /**
     * ID comunidad
     */
    public function IdComunidad(){return $this->_idComunidad;}
    public function SetIdComunidad(string $value){
        $this->_idComunidad = $value;
        return $this;
    } 

    /**
     * Comunidad
     */
    public function Comunidad(){return $this->_comunidad;}
    public function SetComunidad($value){
        $this->_comunidad = $value;
        return $this;
    }

    /**
     * ID Servicio
     */
    public function IdServicio(){return $this->_idServicio;}
    public function SetIdServicio($value){
        $this->_idServicio = $value;
        return $this;
    }

    /**
     * PVP Comunidad
     */
    public function PVPComunidad(){ return $this->_pvpComunidad;}
    public function SetPVPComunidad($value){
        $this->_pvpComunidad = $value;
        return $this;
    }

    /**
     * PVP Retorno
     */
    public function PVPRetorno(){return $this->_pvpRetorno;}
    public function SetPVPRetorno($value){
        $this->_pvpRetorno = $value;
        return $this;
    }

    /**
     * Total Taxes Exc
     */
    public function TotalTaxesExc(){return $this->_totalTaxesExc;}
    public function SetTotalTaxesExc($value){
        $this->_totalTaxesExc = $value;
        return $this;
    }

    /**
     * Total Taxes Inc
     */
    public function TotalTaxesInc(){return $this->_totalTaxesInc;}
    public function SetTotalTaxesInc($value){
        $this->_totalTaxesInc = $value;
        return $this;
    }

    /**
     * Fecha de creación
     */
    public function Created(){return $this->_created;}   
    public function SetCreated($value){
        $this->_created = $value; return $this;
    }

    /**
     * Fecha de actualización
     */
    public function Updated(){ return $this->_updated;}
    public function SetUpdated($value){
        $this->_updated = $value; return $this;
    }

    /**
     * ID usuario creación
    */
    public function UserCreate(){return $this->_usercreate;}
    public function SetUserCreate(int $value){
        $this->_usercreate = $value; return $this;        
    }

    /**
     * @var bool
     * Indica si se puede devolver el schema
     */
    public $canReturnSchema;

    /** @var Relation */
    private $relations = [];

    public function __construct()
    {

        //  Inicializamos el entity helper
        $this->InitEntity();

        //  Establecemos las relaciones
        $this->setRelations();

    }

    /** Establece las relaciones de entidades con esta entidad */
    private function setRelations()
    {       

    }

}