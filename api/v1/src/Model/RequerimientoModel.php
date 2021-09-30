<?php

namespace Fincatech\Model;

use Fincatech\Entity\Requerimiento;

class RequerimientoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Requerimiento';

    private $tablasSchema = array("requerimiento", "comunidad", "comunidadrequerimientos", "ficheroscomunes");

    /**
     * @var \Fincatech\Entity\Requerimiento
     */
    public $requerimiento;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function ListRequerimientosComunidad($comunidadId)
    {

        //  Recuperamos 
        $sql = "
            SELECT 
                req.*
            FROM
                requerimientos req
                LEFT JOIN comunidadrequerimientos comreq on comreq.idrequerimiento = req.id and comreq.idcomunidad = $comunidadId
            WHERE
                req.tipo = 'COM'";

    }

    /** Devuelve un listado de entidad requerimiento por idtipo */
    public function ListRequerimientoByIdTipo($idTipo)
    {
        $data = $this->List(null, false);
        $data = $this->filterResults($data, 'Requerimiento', 'idrequerimientotipo', $idTipo);
        return $data;
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}