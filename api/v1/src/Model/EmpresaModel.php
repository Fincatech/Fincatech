<?php

namespace Fincatech\Model;

use Fincatech\Entity\Empresa;

class EmpresaModel extends \HappySoftware\Model\Model{

    private $entidad = 'Empresa';

    private $tablasSchema = array("Empresa");

    /**
     * @var \Fincatech\Entity\Empresa
     */
    public $empresa;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function List($params =  null, $useUserLogged = false)
    {
        $data = [];
        $data = parent::List($params, $useUserLogged);

        //  Recuperamos las comunidades asociadas a esta empresa
            for($x = 0; $x < count($data['Empresa']); $x++)
            {
                //  Por cada una de las empresas dadas de alta en el sistema buscamos todas las comunidades asociadas
                    $sql = "select * from view_comunidadesempresa where idempresa = " . $data['Empresa'][$x]['id'];
                    $data['Empresa'][$x]['comunidades'] = $this->query($sql);

                //  Recuperamos los documentos asociados a CAE de empresa y su estado
                    $sql = "SELECT * FROM fincatech.view_documentoscaeempresa where @IDEMPRESAREQUERIMIENTO:=" . $data['Empresa'][$x]['id']; 
                    // echo($sql . ' ' );
                    $data['Empresa'][$x]['documentacioncae'] = $this->query($sql);
            }

        //  TODO: Validar si hay registros de empresas para crear las subentidades vacías
        if(count($data['Empresa']) <= 0)
        {
        }
// die();
        return $data;
    }

}