<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Database\DatabaseCore;

trait CrudTrait{

    //  método de guardar entidad en bbdd
    public function Save()
    {
        if($this->isInsertAction())
        {
            //  Ejecutamos sobre la propiedad SQL
            $this->repositorio->queryRaw( $this->constructInsertSQL() );
            $data['id'] = $this->repositorio->getLastID($this->getEntidad()) - 1;
            return $data;
        }else{
            //  Ejecutamos sobre la propiedad SQL
            $this->repositorio->queryRaw( $this->constructUpdateSQL() );
            //$data['id'] = $this->repositorio->getLastID($this->getEntidad());
            return [];
        }

    }

    /** Inserta un registro en la base de datos 
     * @param string $entidadPrincipal. Nombre de la entidad que se va a crear
     * @param string $data. JSON con la información a almacenar
    */
    public function Create($entidadPrincipal, $data)
    {
        $this->setEntidad($entidadPrincipal);

        //  Recuperamos el esquema de la entidad y sus entidades relacionadas
        $this->getSchemaEntity();

        //  Preparamos el builder de SQL
        $this->createInsert($entidadPrincipal);

        //  Recuperamos todos los valores del post que hemos recibido
        $this->processJSONPostData($data);

        //  Auditoría
        $this->addField("created", "now()");

        //  Ejecutamos sobre la propiedad SQL
        return $this->Save();

    }

    /** Convierte el JSON del post a un array asociativo arreglando los valores */
    private function processJSONPostData($data)
    {
        //  Recuperamos todos los valores del post que hemos recibido
        foreach($data as $key => $value)
        {
                //  Validamos que exista la propiedad en la tabla
                $value = $this->fixFieldValue($key, $value);
                $this->addField($key, $value);

        }    
    }

    //  CRUD METHODS
    /**
     * Elimina una entidad de la base de datos
     */
    public function Delete($id, $entityObject = null)
    {
        //$moveToHistorial = $this->getEntity()->tipoEliminacion == HIS
        $this->getRepositorio()->deleteSingle($this->mainEntity, $id, $this->getEntity()->moveToHistorial, $this->getEntity()->primaryKey, true);

        //  Eliminamos los registros de las entidades relacionadas
        if($this->haveEntityRelations)
        {
            //  Comprobamos si las entidades relacionadas tienen propiedad de escritura y comprobamos si deben eliminarse en cascada los datos
            foreach($this->getRelations() as $relationTable)
            {
                //echo 'tabla: ' . $relationTable->table . '<br>';
                if($relationTable->readOnly == false && $relationTable->deleteOnCascade == true)
                {
                    if($relationTable->deleteMode == DELETE_FISICO)
                    {
                        //  LANZAMOS DELETE SOBRE LA TABLA
                        $this->getRepositorio()->deleteSingle($relationTable->table, $id, false, $relationTable->sourceColumn, true);
                    }else{
                        //  LANZAMOS UPDATE SOBRE LA TABLA DE LA RELACIÓN
                        //echo 'delete lógico: ' . $this->getRepositorio()->getLastID($relationTable->table) . '<br>';
                    }
                }else{
                    //echo 'no se elimina<br>';
                }
            }
            //  Se comprueba también si debe pasarse al historial la información
            
            //  Se comprueba también si la eliminación es física o lógica
            
        }
        
        //return true;
        
        return 'ok';
        
    }

    //  TODO:
    public function Update($entidadPrincipal, $data, $entidadId)
    {

        $this->setEntidad($entidadPrincipal);
        //  Recuperamos el esquema de la entidad y sus entidades relacionadas
        $this->getSchemaEntity();        

        //  Preparamos el builder de SQL
        $this->createUpdate($entidadPrincipal);

        //  Recuperamos todos los valores del post que hemos recibido
        $this->processJSONPostData($data);        
 
        //  Guardamos
        return $this->Save();
    }

    /**
     * Recupera un único registro
     */
    public function Get($id)
    {
        return $this->getById($id);
    }

    /**
     * Obtiene todos los registros para una entidad
     */
    public function getAll($mainAlias = null, $criteria = null)
    {
        // Primero nos traemos los datos de la entidad principal teniendo en cuenta los criterios de selección que pudiera tener
        $this->queryToExecute = "select * from " . $this->mainEntity . " ";

        if(!is_null($criteria))
        {
            if(isset($criteria['start']) && isset($criteria['length']))
            {
                $this->queryToExecute .= " limit " . $criteria['start'] . "," . ($criteria['start'] + $criteria['length']);
            }
        }

        $this->execute(true,false);

        // TODO: Si tiene criterios para acotar, los establecemos
        $this->entityData['total'] = $this->getTotalRows("select * from " . $this->mainEntity . " ");
        $this->entityData['filtered'] = $this->getTotalRows($this->queryToExecute);
        return $this->entityData;

    }

    /** Recupera todos los registros
     * @param array $params. Contiene la definición de los filtros establecidos para el listado incluyendo la paginación
     */
    public function List($params = null)
    {
        return $this->getAll('u', $params);
    }

}