<?php

namespace Fincatech\Model;

use Fincatech\Entity\Documental;

class DocumentalModel extends \HappySoftware\Model\Model{

    private $entidad = 'Requerimiento';

    private $tablasSchema = array("usuario, usuarioRol");

    /**
     * @var \Fincatech\Entity\Requerimiento
     */
    public $documental;

    public function __construct($params = null)
    {
        parent::__construct();
        //  Inicializamos la entidad
        $this->InitEntity('Requerimiento');

        //  Inicializamos el modelo
        $this->InitModel('Requerimiento', $params, $this->tablasSchema);

    }

    public function getRelacionesTabla()
    {
        return [
            'comunidad' => [
                'tabla' => 'comunidadrequerimiento',
                'campo' => 'idcomunidad'
            ],
            'empleado'  => [
                'tabla' => 'empleadorequerimiento',
                'campo' => 'idempleado'
            ],
            'empresa'   => [
                'tabla' => 'empresarequerimiento',
                'campo' => 'idempresa'
            ]
        ];
    }

    /** Asigna un nuevo documento al tipo seleccionado */
    public function createRequerimiento($idFichero, $datos)
    {
    
            $tablasRelaciones = $this->getRelacionesTabla();
            $destino = $datos['entidad'];
        //  Generamos el registro en la base de datos
            $sql = "insert into " . $tablasRelaciones[$destino]['tabla'] . "(idrequerimiento, idfichero, " . $tablasRelaciones[$destino]['campo'] .", created) ";
            $sql .=" values (";

        //  ID requerimiento
            $sql .= $datos['idrequerimiento'] . ", " ;

        //  ID Fichero
            $sql .= $idFichero .", ";

        //  ID Campo Destino
            $sql .= $datos[$tablasRelaciones[$destino]['campo']] . ", ";

        //  Created
            $sql .= "now() ";

        //  Cierre de consulta
            $sql .= " ) ";

            $this->getRepositorio()->queryRaw( $sql );

        //  Devolvemos un estado ok
            return 'ok';

    }

    public function cambiarEstadoRequerimiento($idrequerimiento, $destino, $nuevoEtado)
    {
    
    }

    public function actualizarRequerimiento($id, $datos)
    {
    
    }

    /** Comprueba si un requerimiento ya existe en el sistema */
    public function existeRequerimiento($idrequerimiento, $tablaDestino, $idkey, $id)
    {
        $tablasRelaciones = $this->getRelacionesTabla();
        return $this->getRepositorio()->ExisteRegistro($tablaDestino, " idrequerimiento=$idrequerimiento and $idkey = $id");
    }

}