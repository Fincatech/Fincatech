<?php

namespace Fincatech\Model;

use Fincatech\Entity\Servicios;

class ServiciosModel extends \HappySoftware\Model\Model{

    private $entidad = 'Tiposservicios';
    private $tablaComunidadServiciosContratados = '';
    private $tablaComunidad = '';
    private $tablaTiposServicios = '';

    private $tablasSchema = array("servicios, usuarioRol");

    private $_contratado;
    public function Contratado(){return $this->_contratado;}
    public function SetContratado($value){
        $this->_contratado = $value;
        return $this;
    }

    private $_idComunidad;
    public function IdComunidad(){return $this->_idComunidad;}
    public function SetIdComunidad($value){
        $this->_idComunidad = $value;
        return $this;
    } 
   
    private $_id;
    public function Id(){return $this->_id;}
    public function SetId($value){
        $this->_id = $value;
        return $this;
    } 

    private $_precio;
    public function Precio(){return $this->_precio;}
    public function SetPrecio($value){
        $this->_precio = $value;
        return $this;
    } 

    private $_precioComunidad;
    public function PrecioComunidad(){return $this->_precioComunidad;}
    public function SetPrecioComunidad($value){
        $this->_precioComunidad = $value;
        return $this;
    } 

    private $_mesFacturacion;
    public function MesFacturacion(){return $this->_mesFacturacion;}
    public function SetMesFacturacion($value){
        $this->_mesFacturacion = $value;
        return $this;
    } 

    /**
     * @var \Fincatech\Entity\Servicios
     */
    public $servicios;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = false)
    {
        return parent::List($params, $useLoggedUserId);
    }

    /**
     * Actualiza los precios de servicio de una comunidad
     */
    public function UpdateServicioComunidad()
    {
        $sql = 'update comunidadservicioscontratados set contratado = ' . $this->Contratado() . ', ';
        $sql .= 'precio = ' . $this->Precio() . ', ';
        $sql .= 'preciocomunidad = ' . $this->PrecioComunidad() . ', ';
        $sql .= 'mesfacturacion = ' . $this->MesFacturacion() . ', ';
        $sql .= 'contratado = ' . $this->Contratado() . ' ';
        $sql .= ' where id = ' . $this->Id();
        $this->getRepositorio()->queryRaw($sql);
    }

}