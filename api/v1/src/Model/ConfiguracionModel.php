<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Usuario;
use HappySoftware\Database\DatabaseCore;

class ConfiguracionModel extends \HappySoftware\Model\Model{

    private $entidad = 'Configuracion';
    private $tablasSchema = array("configuracion");

    /**
     * @var array
     */
    public $configuracion = Array();


    public function SetId($value){

    }
    public function Id(){

    }

    // private $_key;
    // public function SetKey($value){
    //     $this->_key = $value;
    //     return $this;
    // }
    // public function Key(){
    //     return $this->_key;
    // }

    private $_value;
    public function SetValue($key, $value){
        // $this->_value = $value;
        $this->$key = $value;
        return $this;
    }
    public function Value(){
        return $this->_value;
    }

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

        //  Cargamos los valores de la ocnfiguración
        $this->GetConfiguracion();
    }

    /**
     * Carga toda la información relativa a la configuración
     */
    private function GetConfiguracion()
    {
        $sql = "select * from configuracion";
        $result = $this->query($sql);
        $this->configuracion = $result;
    }

    /**
     * Devuelve un valor de configuración
     */
    public function GetValue($key)
    {
        if(isset($this->$key))
        {   
            return $this->$key;
        }else{
            return null;
        }
    }

    /**
     * Actualiza el valor de una clave de configuración
     */
    public function UpdateValue($key, $newValue)
    {
        //  Lanzamos sobre la bbdd la actualización
        $sql = "update " . strtolower($this->entidad) . " set valor = '" . $newValue . "' where `name` = '" . $key . "'";
        $this->queryRaw($sql);
        //  Establecemos el nuevo valor
        $this->$key = $newValue;
    }


    public function Exists($key, $value)
    {
        return $this->ExistsByFieldAndValue($key, $value);
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}