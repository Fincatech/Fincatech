<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class RemesaDetalle extends EntityHelper{

    protected $tableName = 'remesadetalle';
    protected $primaryKey = 'id';
    protected $tipoEliminacion = DELETE_FISICO;
    protected $moveToHistorial = false;
    protected $orderBy = null;
    protected $orderType = null;
    
    //  Propiedades de la entidad
    //  ID
    public int     $id = -1;
    //  Id de la remeesa
    public int     $idremesa;
    //  Id de la factura asociada
    public int     $invoiceid;
    //  Concepto
    public string  $descripcion;
    //  Cantidad
    public float   $amount;
    //  Customer Name
    public string  $customername;
    //  Customer BIC
    public string  $customerbic;
    //  Customer IBAN
    public string  $customeriban;
    //  Nº de veces presentado
    public int     $presentado = 1;
    //  UniqueId
    public string  $uniqid;
    //  Estado
    public string  $estado = 'C'; // Por defecto se da como cobrada
    //  Fecha devolución
    public string|null  $datereturned;
    //  ID usuario creación
    public int          $usercreate = -1;
    //  Fecha de creación
    public string|null  $created = '';
    //  Fecha de actualización
    public string|null  $updated = '';

    /**
     * @var bool
     * Indica si se puede devolver el schema
     */
    protected $canReturnSchema;

    /** @var Relation */
    protected $relations = [];

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
        //  Entidad usuario_roles
        $this->relations[] = $this->addRelation([
            'table' => 'remesadevolucion',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la tabla desde la que se va a relacionar
            'targetColumn' => 'idremesadetalle',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            //  Indica si se debe eliminar el registro relacionado o no
            'deleteOnCascade' => false,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);
    }

}