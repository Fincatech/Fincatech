<?php

namespace HappySoftware\Entity;

use HappySoftware\Entity\DatabaseHelper\Relation;

class EntityHelper{

    private $entityRelations = [];

    private $_orderBy = null;
    private $_orderType = null;

    public function __construct()
    {
    }

    public function __get($nombre) {
        // Usamos get_object_vars para acceder a las propiedades de la clase hija
        $props = get_object_vars($this); // Devuelve las propiedades de la instancia actual
        return $props[$nombre] ?? null;
    }

    public function __set($nombre, $valor) {
        // Verificamos si la propiedad existe en la entidad hija
        if (property_exists($this, $nombre)) {
            $this->$nombre = $valor;
        }
    }

    public function setOrderType($value)
    {
        $this->_orderType = $value;
        return $this;
    }
    public function getOrderType()
    {
        return $this->_orderType;
    }

    /** Establece el orden por defecto */
    public function setOrderBy($value)
    {
        $this->_orderBy = $value;
        return $this;
    }

    /** Obtiene el valor por defecto */
    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    public function InitEntity()
    {
        include_once(ABSPATH.'src/Entity/RelationEntity.php');
    }

    public static function serializeData($data)
    {
        return json_encode($data);
    }

    public function getRelations()
    {
        return $this->entityRelations;
    }

    /** Agrega la relación de la tabla que se pasa por parámetro  */
    public function addRelation($tableRelation)
    {
        /** @var HappySoftware\Entity\DatabaseHelper\Relation $relaciones */
        $relaciones = new Relation();
        $relaciones->table = $tableRelation['table'];
        $relaciones->fieldType = $tableRelation['fieldType'];
        $relaciones->sourceColumn = $tableRelation['sourceColumn'];
        $relaciones->targetColumn = $tableRelation['targetColumn'];
        $relaciones->relationType = $tableRelation['relationType'];

        // Validamos las propiedades opcionales y establecemos las mismas si se han especificado en cada entidad
        //  o bien cogemos las propiedades por defecto de la relación que esté establecida en DataaseHelperEntity
        $relaciones->canReturnSchema = ( isset($tableRelation['canReturnSchema']) ? $tableRelation['canReturnSchema'] : $relaciones->canReturnSchema);
        //  Sólo lectura
        $relaciones->readOnly = ( isset($tableRelation['readOnly']) ? $tableRelation['readOnly'] : $relaciones->readOnly);
        //  Eliminación en cascada
        $relaciones->deleteOnCascade = ( isset($tableRelation['deleteOnCascade']) ? $tableRelation['deleteOnCascade'] : $relaciones->canReturnSchema);
        //  Modo de eliminación
        $relaciones->deleteMode = ( isset($tableRelation['deleteMode']) ? $tableRelation['deleteMode'] : $relaciones->canReturnSchema);
        //  Alias de la tabla
        $relaciones->alias = ( isset($tableRelation['alias']) ? $tableRelation['alias'] : null);
        //  Tipo de relación de registros
        $relaciones->resultsRelationType = ( isset($tableRelation['resultsRelationType']) ? $tableRelation['resultsRelationType'] : ONE_TO_MANY);

        $this->entityRelations[] = $relaciones;

        return $relaciones;

    }

}