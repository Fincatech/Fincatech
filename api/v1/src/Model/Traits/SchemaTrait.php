<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Controller\Traits\ConfigTrait;
use HappySoftware\Database\DatabaseCore;

trait SchemaTrait{

    use ConfigTrait;

    /**
     * Devuelve todo el esquema completo de la entidad principal y sus relaciones
     * @param bool $includeRelations Si es true (por defecto) devuelve el esquema completo si no devuelve el schema de la entidad principal asociada al modelo
     */
    public function getSchemaEntity($includeRelations = true)
    {
        $this->entitySchema = [];
        
        $this->entitySchema[$this->mainEntity] = $this->repositorio->getSchemaInfo( $this->mainEntity );

        //  Comprueba si tiene relaciones establecidas y se ha pedido que incluya todos los schemas
        if($includeRelations && $this->haveEntityRelations)
        {
            foreach($this->entityRelations as $relacion)
            {
                if($relacion->canReturnSchema)
                {
                  $this->entitySchema[$relacion->table] = $this->repositorio->getSchemaInfo($relacion->table);            
                }
            }
        }

        $this->entityData['schema'] = $this->entitySchema;

    }

    /**
     * Obtiene el schema de la entidad inicializada. Se utiliza para recuperar el schema desde el endpoint /{controller}/schema
     */
    public function getSchema()
    {
        $this->getSchemaEntity();
        return $this->entityData;
    }

    /** 
     * Obtiene la definición del esquema junto con la definición de cada columna de la entidad desde la base de datos 
     * @param string $tableName Nombre de la tabla que se va a recuperar desde la base de datos
    */
    public function getSchemaDefinition($tableName)
    {
        //  Lo primero que se valida es si este schema está configurado para poder devolverse al usuario

        // Consulta a la base de datos por una tabla y recupera el nombre del campo y su tipo
        $schemaInfo = $this->repositorio->getSchemaInfo($tableName);

        //  Comprueba si la tabla existe y en caso de no existir comprueba si se debe crear
        if(is_null($schemaInfo) && $this->repositorio->getCreateMissingTables() == true)
        {
            //  Creamos la tabla en base a la entidad, para ello creamos la instancia
            $entityName = ConfigTrait::getNamespaceName() . 'Entity\\' . $tableName;
            $entityCreate = new $entityName();

            //  Recuperamos la información de la nueva tabla creada
            $schemaInfo = $this->repositorio->getSchemaInfo($tableName);
        }
        return $schemaInfo;
    }

}