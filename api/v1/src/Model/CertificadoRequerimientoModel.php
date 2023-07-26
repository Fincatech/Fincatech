<?php

namespace Fincatech\Model;

use Fincatech\Entity\Documental;
use HappySoftware\Controller\HelperController;

class CertificadoRequerimientoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Requerimiento';

    private $tablasSchema = array("usuario", "usuarioRol");

    private $_id;
    private $_idUsuario;
    private $_idComunidad;
    private $_idRequerimiento;
    private $_fechaSubida;
    private $_fechaCaducidad;
    private $_idFichero;
    private $_idEstado;
    private $_estado;
    private $_observaciones;
    private $_created;
    private $_updated;
    private $_usercreate;

    public function SetId($value){
        $this->_id = $value;
        return $this;
    }
    public function Id(){
        return $this->_id;
    }

    public function SetIdUsuario($value){
        $this->_idUsuario = $value;
        return $this;
    }

    public function IdUsuario(){
        return $this->_idUsuario;
    }

    public function IdComunidad(){
        return $this->_idComunidad;
    }
    public function SetIdComunidad($value){
        $this->_idComunidad = $value;
        return $this;
    }

    public function IdRequerimiento(){
        return $this->_idRequerimiento;
    }
    public function SetIdRequerimiento($value){
        $this->_idRequerimiento = $value;
        return $this;
    }

    public function FechaSubida(){
        return $this->_fechaSubida;
    }
    public function SetFechaSubida($value){
        $this->_fechaSubida = $value;
        return $this;
    }
    
    public function FechaCaducidad(){
        return $this->_fechaCaducidad;
    }
    public function SetFechaCaducidad($value){
        $this->_fechaCaducidad = $value;
        return $this;
    }
    
    public function IdFichero(){
        return $this->_idFichero;
    }
    public function SetIdFichero($value){
        $this->_idFichero = $value;
        return $this;
    }
    
    public function IdEstado(){
        return $this->_idEstado;
    }
    public function SetIdEstado($value){
        $this->_idEstado = $value;
        return $this;
    }
    
    public function Estado(){
        return $this->_estado;
    }
    public function SetEstado($value){
        $this->_estado = $value;
        return $this;
    }
    
    public function Observaciones(){
        return $this->_observaciones;
    }
    public function SetObservaciones($value){
        $this->_observaciones = $value;
        return $this;
    }
    
    public function Created(){
        return $this->_created;
    }
    public function SetCreated($value){
        $this->_created = $value;
        return $this;
    }
    
    public function Updated(){
        return $this->_updated;
    }
    public function SetUpdated($value){
        $this->_updated = $value;
        return $this;
    }
    
    public function Usercreate(){
        return $this->_usercreate;
    }

    public function SetUsercreate($value){
        $this->_usercreate = $value;
        return $this;
    }

    public function Insert(){

        $data = [];
        $data['idusuario'] = $this->IdUsuario();
        $data['idcomunidad'] = $this->IdComunidad();
        $data['idrequerimiento'] = $this->IdRequerimiento();
        $data['fechasubida'] = $this->FechaSubida();
        $data['fechacaducidad'] = $this->FechaCaducidad();
        $data['idfichero'] = $this->IdFichero();
        $data['idestado'] = $this->IdEstado();
        $data['estado'] = $this->Estado();
        $data['observaciones'] = $this->Observaciones();
        $data['usercreate'] = $this->Usercreate();

        return $this->Create('certificadorequerimiento', $data);

    }

    public function Update($entidadPrincipal, $datos, $usuarioId){
        $data = [];
        $data['id'] = $this->Id();
        $data['idusuario'] = $this->IdUsuario();
        $data['idcomunidad'] = $this->IdComunidad();
        $data['idrequerimiento'] = $this->IdRequerimiento();
        $data['fechasubida'] = $this->FechaSubida();
        $data['fechacaducidad'] = $this->FechaCaducidad();
        $data['idfichero'] = $this->IdFichero();
        $data['idestado'] = $this->IdEstado();
        $data['estado'] = $this->Estado();
        $data['observaciones'] = $this->Observaciones();
        $data['usercreate'] = $this->Usercreate();
        $data['updated'] = date('Y-m-d H:i:s');
        return $this->Update('certificadorequerimiento', $data, $this->getLoggedUserId());
    }

}