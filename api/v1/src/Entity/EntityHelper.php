<?php

namespace HappySoftware\Entity;

use HappySoftware\Entity\DatabaseHelper\Relation;

class EntityHelper{

    private $entityRelations = [];

    public function __construct()
    {
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
        $relaciones->readOnly = ( isset($tableRelation['readOnly']) ? $tableRelation['readOnly'] : $relaciones->readOnly);
        $relaciones->deleteOnCascade = ( isset($tableRelation['deleteOnCascade']) ? $tableRelation['deleteOnCascade'] : $relaciones->canReturnSchema);
        $relaciones->deleteMode = ( isset($tableRelation['deleteMode']) ? $tableRelation['deleteMode'] : $relaciones->canReturnSchema);

        $this->entityRelations[] = $relaciones;

        return $relaciones;

    }

}