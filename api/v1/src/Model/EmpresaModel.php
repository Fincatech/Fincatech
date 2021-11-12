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

    public function Create($entidad, $datos)
    {
                //  Generamos en primer lugar el objeto usuario de tipo contratista
        //  FIXME: Esto hay que meterlo en el objeto del modelo de usuario y luego guardar
        $datosNuevoUsuario = [];
        $datosNuevoUsuario['nombre'] = $datos['razonsocial'];
        $datosNuevoUsuario['cif'] = $datos['cif'];
        $datosNuevoUsuario['direccion'] = $datos['direccion'];
        $datosNuevoUsuario['localidad'] = $datos['localidad'];
        $datosNuevoUsuario['codpostal'] = $datos['codpostal'];
        $datosNuevoUsuario['telefono'] = $datos['telefono'];
        $datosNuevoUsuario['emailcontacto'] = $datos['email'];
        $datosNuevoUsuario['email'] = $datos['email'];
        $datosNuevoUsuario['rolid'] = 6;
        $datosNuevoUsuario['password'] = md5('finca123456');
        $datosNuevoUsuario['estado'] = 'A';
        $datosNuevoUsuario['salt'] = '';

        //  Recuperamos el ID del usuario para poder asignarlo a la hora de crear la empresa
            $idNuevoUsuario = parent::Create('usuario', $datosNuevoUsuario);

        $datos['idusuario'] = $idNuevoUsuario['id'];

        //TODO: $this->createRelationBetweenEmpresaAndComunidad($idComunidad, $idEmpresa);

        return parent::Create($entidad, $datos);
    }

    // TODO: Recuperar ID cuando hace la llamada desde el front
    private function createRelationBetweenEmpresaAndComunidad($idComunidad, $idEmpresa)
    {
        $sql = "insert into comunidadempresa(idcomunidad, idempresa, activa, created, usercreate) values (";
        $sql .= $idComunidad . ", ";
        $sql .= $idEmpresa . ", 1,  now(), ";
        $sql .= $this->getLoggedUserId() . " ";
        $sql .= " ) ";

        $this->getRepositorio()->queryRaw($sql);  
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
                    $sql = "SELECT * FROM fincatech.view_documentoscaeempresa where @p1:=" . $data['Empresa'][$x]['id']; 
                    // echo($sql . ' ' );
                    $data['Empresa'][$x]['documentacioncae'] = $this->query($sql);
            }

        //  TODO: Validar si hay registros de empresas para crear las subentidades vacías
        if(count($data['Empresa']) <= 0)
        {
        }

        return $data;
    }

    /** Recupera las comunidades para las que trabaja una empresa */
    public function GetComunidades($idEmpresa)
    {
        
        //  Instanciamos el controller de comunidad para recuperar el listado
            $this->InitController('Comunidad');

        //  Listamos todas las comunidades
            $listadoComunidades = $this->ComunidadController->List(null, true);
            $listadoComunidades = $this->filterResults($listadoComunidades, "Comunidad", 'idusuario', $idEmpresa, "view_empresascomunidad");
            return $listadoComunidades;

    }

}