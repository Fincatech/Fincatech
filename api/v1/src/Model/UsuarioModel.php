<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Usuario;

class UsuarioModel extends \HappySoftware\Model\Model{

    //  ID
    public $id;
    public function Id(){
        return $this->id;
    }
    public function setId($value){
        $this->id = $value;
        return $this;
    }

    //  Código
    public $codigo;
    public function Codigo(){
        return $this->codigo;
    }
    public function setCodigo($value){
        $this->codigo = $value;
        return $this;
    }

    //  Nombre
    public $nombre;
    public function Nombre(){
        return $this->nombre;
    }
    public function setNombre($value){
        $this->nombre = $value;
        return $this;
    }

    //  Cif
    public $cif;
    public function Cif(){
        return $this->cif;
    }
    public function setCif($value){
        $this->cif = $value;
        return $this;
    }

    //  Direccion
    public $direccion;
    public function Direccion(){
        return $this->direccion;
    }
    public function setDireccion($value){
        $this->direccion = $value;
        return $this;
    }

    //  Localidad
    public $localidad;
    public function Localidad(){
        return $this->localidad;
    }
    public function setLocalidad($value){
        $this->localidad = $value;
        return $this;
    }

    //  Localidad ID
    public $localidadId;
    public function LocalidadId(){
        return $this->localidadId;
    }
    public function setLocalidadId($value){
        $this->localidadId = $value;
        return $this;
    }

    //  ProvinciaID
    public $provinciaId;
    public function ProvinciaId(){
        return $this->provinciaId;
    }
    public function setProvinciaId($value){
        $this->provinciaId = $value;
        return $this;
    }

    //  Código postal
    public $codpostal;
    public function CodPostal(){
        return $this->codpostal;
    }
    public function setCodPostal($value){
        $this->codpostal = $value;
        return $this;
    }    

    //  Teléfono
    public $telefono;
    public function Telefono(){
        return $this->telefono;
    }
    public function setTelefono($value){
        $this->telefono = $value;
        return $this;
    }  

    //  Móvil
    public $movil;
    public function Movil(){
        return $this->movil;
    }
    public function setMovil($value){
        $this->movil = $value;
        return $this;
    }  

    //  Email contacto
    public $emailcontacto;
    public function EmailContacto(){
        return $this->emailcontacto;
    }
    public function setEmailContacto($value){
        $this->emailcontacto = $value;
        return $this;
    }  

    //  Email
    public $email;
    public function Email(){
        return $this->emailcontacto;
    }
    public function setEmail($value){
        $this->emailcontacto = $value;
        return $this;
    }  

    //  RolId
    public $rolid;
    public function RolId(){
        return $this->rolid;
    }
    public function setRolId($value){
        $this->rolid = $value;
        return $this;
    }  

    //  Password
    public $password;
    public function Password(){
        return $this->password;
    }
    public function setPassword($value){
        $this->password = $value;
        return $this;
    }  

    //  Salt
    public $salt;
    public function Salt(){
        return $this->salt;
    }
    public function setSalt($value){
        $this->salt = $value;
        return $this;
    }  

    //  Token
    public $token;
    public function Token(){
        return $this->token;
    }
    public function setToken($value){
        $this->token = $value;
        return $this;
    }  

    //  RGPD
    public $rgpd;
    public function Rgpd(){
        return $this->rgpd;
    }
    public function setRgpd($value){
        $this->rgpd = $value;
        return $this;
    }  

    //  Nombre administrador
    public $administradornombre;
    public function AdministradorNombre(){
        return $this->administradornombre;
    }
    public function setAdministradorNombre($value){
        $this->administradornombre = $value;
        return $this;
    } 

    //  Email administrador
    public $administradoremail;
    public function AdministradorEmail(){
        return $this->administradoremail;
    }
    public function setAdministradorEmail($value){
        $this->administradoremail = $value;
        return $this;
    } 

    //  Móvil Administrador
    public $administradormovil;
    public function AdministradorMovil(){
        return $this->administradormovil;
    }
    public function setAdministradorMovil($value){
        $this->administradormovil = $value;
        return $this;
    } 

    //  Contacto Nombre
    public $contactonombre;
    public function ContactoNombre(){
        return $this->contactonombre;
    }
    public function setContactoNombre($value){
        $this->contactonombre = $value;
        return $this;
    } 

    //  Contacto teléfono
    public $contactotelefono;
    public function ContactoTelefono(){
        return $this->contactotelefono;
    }
    public function setContactoTelefono($value){
        $this->contactotelefono = $value;
        return $this;
    } 

    //  Contacto Email
    public $contactoemail;
    public function ContactoEmail(){
        return $this->contactoemail;
    }
    public function setContactoEmail($value){
        $this->contactoemail = $value;
        return $this;
    } 

    //  Observaciones
    public $observaciones;
    public function Observaciones(){
        return $this->observaciones;
    }
    public function setObservaciones($value){
        $this->observaciones = $value;
        return $this;
    } 

    //  Estado
    public $estado;
    public function Estado(){
        return $this->estado;
    }
    public function setEstado($value){
        $this->estado = $value;
        return $this;
    } 

    //  LastLogin
    public $lastlogin;
    public function LastLogin(){
        return $this->lastlogin;
    }
    public function setLastLogin($value){
        $this->lastlogin = $value;
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

    //  V1
    public $v1;
    public function V1(){
        return $this->v1;
    }
    public function setV1($value){
        $this->v1 = $value;
        return $this;
    }     

    private $entidad = 'Usuario';

    private $tablasSchema = array('usuario', 'usuarioRol');

    /**
     * @var \Fincatech\Entity\Usuario\Usuario
     */
    public $usuario;

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

    public function _Save()
    {

    }

    public function ListByRolId($rolId)
    {
        $datos = parent::getEntityByField('usuario', 'rolId', $rolId);
        $datos['total'] = count($datos["Usuario"]);
        return $datos;
    }

    /** Valida que el e-mail no exista para un usuario */
    public function ValidateUserEmail($email)
    {
        $queryFields = [];
        $queryFields['getfields'] = '*';
        $queryFields['fields'] = [
            'email' => ' = ' . "'".$email."'"
        ];
        $existe = $this->getByFields($queryFields, 'usuario');
        return (count($existe) === 0 ? false : true);
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}