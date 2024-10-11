<?php

namespace Fincatech\Model;

use Fincatech\Entity\Bank;

class BankModel extends \HappySoftware\Model\Model{

    //  ID
    public $id;
    public function Id(){
        return $this->id;
    }
    public function setId($value){
        $this->id = $value;
        return $this;
    }

    private $nombre;
    public function Nombre(){
        return $this->nombre;
    }
    public function setNombre($value){
        $this->nombre = $value;
        return $this;
    }

    private $bic;
    public function Bic(){
        return $this->bic;
    }
    public function setBic($value){
        $this->bic = $value;
        return $this;
    }

    //  Created
    public $created;
    public function Created(){
        return $this->created;
    }
    public function setCreated($value){
        $this->created = $value;
        return $this;
    } 

    //  Updated
    public $updated;
    public function Updated(){
        return $this->updated;
    }
    public function setUpdated($value){
        $this->updated = $value;
        return $this;
    } 

    //  UserCreate
    public $usercreate;
    public function UserCreate(){
        return $this->usercreate;
    }
    public function setUserCreate($value){
        $this->usercreate = $value;
        return $this;
    }     

    private $entidad = 'Bank';

    private $tablasSchema = array('bank');

    /**
     * @var \Fincatech\Entity\Bank
     */
    public $bank;

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
    //     $bank = parent::Get($id);

    //     //  Rellenamos el modelo
    //     return $bank;
    // }

    public function List($params = null, $useLoggedUserId = true)
    {
        return parent::List($params, $useLoggedUserId);
    }

    public function _Save()
    {

    }

    public function BicByIBAN(string $iban)
    {
        $sql = "select * from " . strtolower($this->entidad) . " where codigo = '" . $iban ."'";
        return $this->query($sql);
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}