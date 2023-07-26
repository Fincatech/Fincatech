<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Usuario;

class HistoricoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Historico';

    private $_historicoRequerimiento = 'ficheroshistorico';

    private $tablasSchema = array("ficheroshistorico");

    /**
     * @var \Fincatech\Entity\Historico
     */
    public $historico;

    public function __construct($params = null)
    {

        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function CreateHistoricoEntidad($idHistorico, $datos)
    {
        //  Desde el ID de fichero recuperamos
        $datosHistorico = [];
        $datosHistorico['idhistorico'] = $datos[''];
        $datosHistorico['entidadorigen'] = $datos[''];
        $datosHistorico['idcomunidad'] = $datos[''];
        $datosHistorico['idempleado'] = $datos[''];
        $datosHistorico['idempresa'] = $datos[''];
        $datosHistorico['idrequerimiento'] = $datos[''];
        $datosHistorico['fechacaducidad'] = $datos[''];
        $datosHistorico['idfichero'] = $datos['idfichero'];

        $result = $this->Create($this->_historicoRequerimiento, $datosHistorico);
        //  Ahora eliminamos el registro antiguo

    }

    /** Recupera el Histórico de un requerimiento en base a su relación */
    public function GetHistoricoRequerimiento($idRelacionRequerimiento, $entidad)
    {
        $params = [];
        $params['view'] = 'ficheroshistorico';

        $params['filterfield'] = 'idrelacionrequerimiento';
        $params['filtervalue'] = $idRelacionRequerimiento;
        $params['filteroperator'] = '=';

        $params['orderby'] = 'created';
        $params['order'] = 'DESC';

        $historicoRequerimiento = $this->getAll(null, $params, false);
        $historicoRequerimiento = $this->filterResults($historicoRequerimiento, 'Historico', 'entidad', $entidad);

        return $historicoRequerimiento;

    }

    public function TieneHistorico($idRelacionRequerimiento, $entidad)
    {
        //  Hacemos un count sobre la entidad correspondiente en la tabla de histórico
        $ficherosHistorico = $this->getRepositorio()->selectCount( $this->_historicoRequerimiento, " idrelacionrequerimiento = $idRelacionRequerimiento and entidad ", "=", "'$entidad'");
        return ($ficherosHistorico > 0);
    }

    /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = true)
    {
       return parent::List($params, false);
    }

}