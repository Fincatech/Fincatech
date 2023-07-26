<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Autorizado;

class AutorizadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Usuario';

    private $tablasSchema = array("usuario", "usuarioRol");

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

    public function DeleteAutorizado($idAutorizado)
    {
        $idAutorizado = $this->getRepositorio()::PrepareDBString($idAutorizado);
        $sql = "delete from usuario where id = $idAutorizado ";
        $this->queryRaw($sql);
        return 'ok';
    }

    public function EliminarComunidadesAsignadasAutorizado($idAutorizado)
    {
        $idAutorizado = $this->getRepositorio()::PrepareDBString($idAutorizado);
        $sqlDelete = "delete from comunidadautorizado where idautorizado = $idAutorizado";
        $this->queryRaw($sqlDelete);
    }

    public function ComunidadesAsignadas($idAutorizado)
    {
        //  Recupera los ids de las comunidades que tiene asignadas un usuario de tipo autorizado
        $idAutorizado = $this->getRepositorio()::PrepareDBString($idAutorizado);
        return $this->query("select * from comunidadautorizado where idautorizado = $idAutorizado");

    }

    /** Crea la asignación de una comunidad a un usuario autorizado */
    public function InsertarComunidadAsignada($idAutorizado, $idComunidad)
    {
        $idAutorizado = $this->getRepositorio()::PrepareDBString($idAutorizado);
        $idComunidad = $this->getRepositorio()::PrepareDBString($idComunidad);
        //  Validamos que no exista para evitar duplicidades
        if(!$this->TieneComunidadAsignada($idAutorizado, $idComunidad))
        {
            $sql = "insert into comunidadautorizado(idcomunidad, idautorizado, created) values ($idComunidad, $idAutorizado, now())";
            $this->queryRaw($sql);
        }
    }

    private function TieneComunidadAsignada($idAutorizado, $idComunidad)
    {
        $resultado = $this->getRepositorio()->selectCount('comunidadautorizado', 'idautorizado', '=', $idAutorizado . " and idcomunidad = $idComunidad");
        return ($resultado > 0 ? true : false);
    }

    /** Elimina la asignación de una comunidad a un usuario autorizado */
    public function EliminarComunidadAsignada($idAutorizado, $idComunidad)
    {
        $idAutorizado = $this->getRepositorio()::PrepareDBString($idAutorizado);
        $idComunidad = $this->getRepositorio()::PrepareDBString($idComunidad);
        $sql = "delete from comunidadautorizado where idcomunidad = $idComunidad and idautorizado = $idAutorizado";
        $this->queryRaw($sql);
    }

}