<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Sms;

class SmsModel extends \HappySoftware\Model\Model{

    private $entidad = 'Sms';

    private $tablasSchema = array("sms");

    private $_id;
    private $_idUsuario;
    private $_phone;
    private $_message;
    private $_created;
    private $_updated;
    private $_usercreate;
    private $_contractFileId;
    private $_mensajeCertificadoId;
    private $_contrato;
    
    public function Id(){
        return $this->_id;
    }
    private function setId($value){
        $this->_id = $value['id'];
        return $this;
    }

    public function setIdUsuario($value){
        $this->_idUsuario = $value;
        return $this;
    }
    public function IdUsuario(){
        return $this->_idUsuario;
    }

    public function setPhone($value){
        $this->_phone = $value;
        return $this;
    }
    public function Phone(){
        return $this->_phone;
    }

    public function setMessage($value){
        $this->_message = $value;
        return $this;
    }
    public function Message(){
        return $this->_message;
    }

    public function setCreated($value){
        $this->_created = $value;
        return $this;
    }
    public function Created($value){
        return $this->_created;
    }

    public function setUpdated($value){
        $this->_updated = $value;
        return $this;
    }
    public function Updated(){
        return $this->_updated;
    }

    public function setUserCreate($value){
        $this->_usercreate = $value;
        return $this;
    }
    public function UserCreate(){
        return $this->_usercreate;
    }

    public function setContractFileId($value){
        $this->_contractFileId = $value;
        return $this;
    }
    public function ContractFileId(){
        return $this->_contractFileId;
    }

    public function setMensajeCertificadoId($value){
        $this->_mensajeCertificadoId = $value;
        return $this;
    }
    public function MensajeCertificadoId(){
        return $this->_mensajeCertificadoId;
    }

    public function setContrato($value){
        $this->_contrato = $value;
        return $this;
    }

    public function Contrato(){
        return $this->_contrato;
    }

    /**
     * @var \Fincatech\Entity\Sms
     */
    public $sms;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function _Save(){
        $datos['idusuario'] = $this->IdUsuario();
        $datos['phone'] = $this->Phone();
        $datos['message'] = $this->Message();
        // $datos['contractfileid'] = $this->ContractFileId();
        $datos['mensajecertificadoid'] = $this->MensajeCertificadoId();
        $datos['contrato'] = $this->Contrato();
        $this->setId($this->Create('sms', $datos));
    }

    /** Recupera todos los sms enviados junto con los certificados de estado */
    public function List($params = null, $useLoggedUserId = true)
    {

        $sql = "
            SELECT 
                a.id, a.idusuario, a.message, a.phone, a.mensajecertificadoid, a.storagefileid, a.created, a.usercreate,
                b.idmensaje, b.fechacertificacion, b.filename
            FROM
                sms a LEFT JOIN emailscertificados b ON b.idmensaje = a.mensajecertificadoid
            WHERE
                a.usercreate = " . $this->getLoggedUserId();

        //  SMS Contrato
        if(isset($params['contrato']))
        {
            $sql .= " and a.contrato = 1";
        }

        $result = $this->query($sql);
        return $result;
    }

}