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
// die($sql);
            $this->getRepositorio()->queryRaw( $sql );

        //  Devolvemos un estado ok
            return 'ok';

    }

    public function createRequerimientoRGPD($destino, $idFichero, $datos)
    {
        $tablaDestino = '';

        switch ($destino)
        {
            case 'camarasseguridad':
                $tablaDestino = 'camarasseguridad';
                break;
            case 'contratoscesion':
                $tablaDestino = 'contratoscesion';
                break;
            default:
                return 'ok';
                break;
        }

        //  Generamos el registro en la base de datos
            $sql = "insert into " . $tablaDestino . "(titulo, descripcion, idfichero, idcomunidad, created) ";
            $sql .=" values (";

        //  Título
            $sql .= "'" . $this->getRepositorio()::PrepareDBString( $datos['titulo'] ) . "', ";

        //  Descripción
            $sql .= "'" . $this->getRepositorio()::PrepareDBString( $datos['observaciones'] ) . "', ";

        //  ID Fichero
            $sql .= $idFichero . ", ";

        //  ID Comunidad
            $sql .= $datos['idcomunidad'] . ", ";

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

    /** Recupera el listado de requerimientos según su tipología e id de comunidad */
    public function ListRequerimientoRGPD($destino, $idcomunidad)
    {

        //  FIXME: Buscar forma más eficiente y menos peligrosa que esta
            $destino = ($destino == 'camarasseguridad' ? 'CamarasSeguridad' : 'ContratosCesion');

       //   Instanciamos el controller correspondiente
            $this->InitController( $destino );
            $modelo = $destino.'Controller';

        //  Recuperamos el listado general
        //  FIXME: Utilizar el filtro de resultados por idcomunidad
            $datos = $this->$modelo->List(null, false);
       
       //   Filtramos los datos por idcomunidad
            $datos = $this->filterResults($datos, $destino, 'idcomunidad', $idcomunidad);

        //  NOTE: ???Para aliviar la respuesta quitamos los datos de la comunidad ya que no los vamos a necesitar
        //    unset($datos[$destino]['comunidad']);

            return $datos;

    }

}