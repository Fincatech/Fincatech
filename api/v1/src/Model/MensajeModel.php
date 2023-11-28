<?php

namespace Fincatech\Model;

use Fincatech\Entity\Mensaje;

class MensajeModel extends \HappySoftware\Model\Model{

    private $entidad = 'Mensaje';

    private $tablasSchema = array("mensaje", "usuarioRol");

    private $body;
    private $subject;
    private $recipient;
    private $mensajeCertificadoId;
    private $destinatarionombre;

    //  Número de envíos realizados
    private $numeroEnvio;

    //  E-mails certificados
    private $idmensaje;

    /**
     * @var \Fincatech\Entity\Mensaje
     */
    public $mensaje;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

        if(isset($params['email'])){
            $this->setRecipient($params['email']);
        }

        if(isset($params['body'])){
            $this->setBody($params['body']);
        }

        if(isset($params['subject'])){
            $this->setSubject($params['subject']);
        }

        if(isset($params['idmensaje'])){
            $this->setIdMensaje($params['idmensaje']);
        }

        if(isset($params['mensajecertificadoid'])){
            $this->setMensajeCertificadoId($params['mensajecertificadoid']);
        }

        if(isset($params['destinatarionombre'])){
            $this->setDestinatarioNombre($params['destinatarionombre']);
        }

        if(isset($params['numeroenvio'])){
            $this->setNumeroEnvio($params['numeroenvio']);
        }else{
            $this->setNumeroEnvio(1);
        }

    }

    public function getMensajeCertificadoId(){
        return $this->mensajeCertificadoId;
    }

    public function getBody(){
        return $this->body;
    }
    public function getSubject(){
        return $this->subject;
    }
    public function getRecipient(){
        return $this->recipient;
    }
    public function getIdMensaje(){
        return $this->idmensaje;
    }

    public function getDestinatarioNombre(){
        return $this->destinatarionombre;
    }

    public function getNumeroEnvio(){
        return $this->numeroEnvio;
    }

    public function setMensajeCertificadoId($value){
        $this->mensajeCertificadoId = strval($value);
        //return $this;
    }

    public function setDestinatarioNombre($value)
    {
        $this->destinatarionombre = $value;
        return $this;
    }

    public function setBody($value)
    {
        $this->body = $value;
        return $this;
    }
    public function setSubject($value){
        $this->subject = $value;
        return $this;
    }
    public function setRecipient($value)
    {
        $this->recipient = $value;
        return $this;
    }
    public function setIdMensaje($value)
    {
        $this->idmensaje = $value;
        return $this;
    }
    public function setNumeroEnvio($value){
        $this->numeroEnvio = $value;
        return $this;
    }

    public function _Save()
    {
        $datos['email'] = htmlentities($this->getRecipient(), ENT_QUOTES);
        $datos['subject'] = htmlentities($this->getSubject(), ENT_QUOTES);
        $datos['body'] = htmlentities($this->getBody(), ENT_QUOTES);
        $datos['destinatarionombre'] = htmlentities($this->getDestinatarioNombre(), ENT_QUOTES);
        $datos['numeroenvio'] = $this->numeroEnvio;

        //  TODO: id del mensaje certificado
        if(!is_null($this->getMensajeCertificadoId()))
            $datos['mensajecertificadoid'] = $this->getMensajeCertificadoId();

        if($this->isLogged())
        {
            $datos['usercreate'] = $this->getLoggedUserId();
        }

        return $this->Create('mensaje',$datos);
    }

    /** Recupera el ID del e-mail enviado a una empresa tras su invitación por parte de un administrador */
    public function GetEmailRegistroByEmail($email)
    {
        $filter = [];
        $filter['getfields'] = 'id';
        $filter['fields'] = [
            'email' =>  " = '$email' ",
            'subject' => " = 'Fincatech - Alta en la plataforma'"
        ];

        $resultado = $this->getByFields($filter, 'mensaje');

        if(is_null($resultado) || @count($resultado) == 0)
        {
            return -1;
        }else{
            return $resultado[0]['id'];
        }
    }

    /** Almacena la información del e-mail certificado enviado por cada uno de los usuarios */
    public function saveCertificado($idMensaje, $idComunidad, $idEmpresa)
    {
        $sql = "insert into emailscertificados(idmensaje, idcomunidad, idempresa, created) values(";
        $sql .= "'" . $idMensaje . "', $idComunidad, $idEmpresa, now())";
        $this->queryRaw($sql);
    }

    /** Actualiza el nombre del fichero para un mensaje para el cual ya hay zip descargado de mensatek */
    public function updateCertificado($idMensaje, $nombreFichero)
    {
        $sql = "update emailscertificados set filename='$nombreFichero', fechacertificacion = now() where idmensaje='$idMensaje'";
        $this->queryRaw($sql);
    }

    /**
     * Actualiza el número de reenvíos realizados sobre un e-mail
     */
    public function UpdateSendNumber($idMensaje)
    {
        $sql = "update mensaje set numeroenvio = (numeroenvio + 1), created = now() where id = " . $idMensaje;
        $this->queryRaw($sql);
    }

    /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = false)
    {
        return parent::List($params, $useLoggedUserId);
    }

    /**  Recupera los emails certificados que ha enviado un administrador */
    public function EmailsCertificadosAdministrador()
    {
        $sql = "
            SELECT 
            a.id, a.subject, a.email, a.body, a.created, a.usercreate, a.mensajecertificadoid, a.destinatarionombre,
            b.idmensaje, b.destinatarioemail, b.fechacertificacion, b.filename
        FROM
            mensaje a
                LEFT JOIN
            emailscertificados b ON b.idmensaje = a.mensajecertificadoid
        WHERE
            b.idcomunidad = -1
            and a.usercreate = " . $this->getLoggedUserId();
        $result = $this->query($sql);
        return $result;
    }

}