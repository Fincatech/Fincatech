<?php

namespace Fincatech\Model;
use HappySoftware\Database\DatabaseCore;
use Fincatech\Entity\Representantelegal;

class RepresentantelegalModel extends \HappySoftware\Model\Model{

    private $entidad = 'Representantelegal';

    private $tablasSchema = array("usuario", "usuarioRol");

    /**
     * @var \Fincatech\Entity\Usuario\Representantelegal
     */
    public $representantelegal;

    /** ID  
     * @var int
    */
    private $_id;
    public function Id(){ return $this->_id; }
    public function SetId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Administrador ID
     * @var int
    */
    private $_administradorId;
    public function AdministradorId(){ return $this->_administradorId; }
    public function SetAdministradorId($value)
    {
        $this->_administradorId = $value;
        return $this;
    }
    /** Nombre
     * @var string(100)
     */
    private $_nombre;
    public function Nombre(){ return $this->_nombre; }
    public function SetNombre($value){
        $this->_nombre = $value;
        return $this;
    }

    /** Apellido 
     * @var string(100)
     */
    private $_apellido;
    public function Apellido(){return $this->_apellido;}
    public function SetApellido($value)
    {
        $this->_apellido = $value;
        return $this;
    }

    /** Apellido 2
     * @var string(100)
     */
    private $_apellido2;
    public function Apellido2(){ return $this->_apellido2;}
    public function SetApellido2($value)
    {
        $this->_apellido2 = $value;
        return $this;
    }

    /**
     * Email
     * @var string(255)
     */
    private $_email;
    public function Email(){ return $this->_email;}
    public function SetEmail($value)
    {
        $this->_email = $value;
        return $this;
    }

    /**
     * Documento
     * @var string(20)
     */
    private $_documento;
    public function DocumentoIdentificativo(){ return $this->_documento; }
    public function SetDocumentoIdentificativo($value)
    {
        $this->_documento = $value;
        return $this;
    }

    /**
     * Imagen Frontal
     * @var int
     */
    private $_imagenfrontal;
    public function ImagenFrontal(){return $this->_imagenfrontal;}
    public function SetImagenFrontal($value)
    {
        $this->_imagenfrontal = $value;
        return $this;
    }

    /**
     * Imagen anverso
     * @var int
     */
    private $_imagentrasera;
    public function ImagenTrasera(){return $this->_imagentrasera;}
    public function SetImagenTrasera($value)
    {
        $this->_imagentrasera = $value;
        return $this;
    }

    /**
     * Telefono
     * @var string(15)
     */
    private $_telefono;
    public function Telefono(){return $this->_telefono;}
    public function SetTelefono($value)
    {
        $this->_telefono = $value;
        return $this;
    }

    /**
     * Observaciones
     * @var text
     */
    private $_observaciones;
    public function Observaciones(){ return $this->_observaciones;}
    public function SetObservaciones($value)
    {
        $this->_observaciones = $value;
        return $this;
    }

    /**
     * Estado
     * @var string(1)
     */
    private $_estado;
    public function Estado(){ return $this->_estado;}
    public function SetEstado($value)
    {
        $this->_estado = $value;
        return $this;
    }

    /**
     * Fecha de creación
     * @var datetime
     */
    private $_created;
    public function Created(){ return $this->_created;}
    public function SetCreated($value){
        $this->_created = $value;
        return $this;
    }

    /**
     * Fecha de actualización
     * @var datetime
     */
    private $_updated;
    public function Updated(){ return $this->_updated;}
    public function SetUpdated($value)
    {
        $this->_updated = $value;
        return $this;
    }

    /**
     * Usuario de creación
     * @var int
     */
    private $_usercreate;
    public function UserCreate(){ return $this->_usercreate;}
    public function SetUserCreate($value)
    {
        $this->_usercreate = $value;
        return $this;
    }

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /**
     * Recupera los representantes legales mediante el id del administrador
     * @param int $administradorId ID del administrador
     * @return json Objeto con el listado en formato json
     */
    public function ListByAdministradorId($administradorId)
    {
        $sql = "
            SELECT 
                r.id, r.administradorid,
                CONCAT(r.nombre, ' ', r.apellido, ' ', r.apellido2) AS representante,
                r.nombre, r.apellido, r.apellido2,
                r.email, r.documento, r.imagenfrontal,
                r.imagentrasera, r.telefono, r.observaciones,
                r.estado, r.created, 
                f.nombre imagentraseranombre, f.nombrestorage imagentraseranombrestorage, f.created imagentraseracreated,
                f1.nombre imagenfrontalnombre, f1.nombrestorage imagenfrontalnombrestorage, f1.created imagenfrontalcreated
            FROM
                representantelegal r left join ficheroscomunes f on f.id = r.imagentrasera
                left join ficheroscomunes f1 on f1.id = r.imagenfrontal
            WHERE
                r.administradorid = " . DatabaseCore::PrepareDBString($administradorId) . "
            ORDER BY r.nombre ASC , r.apellido ASC , r.apellido2 ASC";
        $datos = $this->query($sql);
        return $datos;
    }

    /**
     * Guarda el modelo en base de datos con la información seteada
     */
    public function _Save()
    {
        $datos = [];
        $datos['administradorid'] = $this->AdministradorId();
        $datos['nombre'] = $this->Nombre();
        $datos['apellido'] = $this->Apellido();
        $datos['apellido2'] = $this->Apellido2();
        $datos['email'] = $this->Email();
        $datos['documento'] = $this->DocumentoIdentificativo();
        $datos['imagenfrontal'] = $this->ImagenFrontal();
        $datos['imagentrasera'] = $this->ImagenTrasera();
        $datos['telefono'] = $this->Telefono();
        $datos['observaciones'] = $this->Observaciones();
        $datos['estado'] = $this->Estado();

        //  Comprobamos si es una actualización o una inserción
        if($this->Id() !== '')
        {
            //  Lanzamos el update
            return $this->Create('representantelegal', $datos);
        }else{
            //  Lanzamos el insert
            return $this->Update($this->entidad, $datos, $this->Id());
        }
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}