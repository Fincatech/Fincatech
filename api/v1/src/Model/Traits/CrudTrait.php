<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Database\DatabaseCore;

trait CrudTrait{

    //  método de guardar entidad en bbdd
    public function Save()
    {
        if($this->isInsertAction())
        {
            $this->constructInsertSQL();

            // die($this->getSQL());

            //  Ejecutamos sobre la propiedad SQL
            $this->repositorio->queryRaw( $this->getSQL() );
            $data['id'] = $this->repositorio->getLastID($this->getEntidad()) - 1;

            //  TODO: Debemos comprobar si hay fichero adjunto para poder realizar la subida de los ficheros y generar
            //  los registros correspondientes en base de datos para poder actualizar la tabla de relación de ficheros

            return $data;
        }else{
            //  Ejecutamos sobre la propiedad SQL
            $this->repositorio->queryRaw( $this->constructUpdateSQL() );
            return [];
        }

    }

    /** Inserta un registro en la base de datos 
     * @param string $entidadPrincipal. Nombre de la entidad que se va a crear
     * @param string $data. JSON con la información a almacenar
    */
    public function Create($entidadPrincipal, $data)
    {
        // print_r($data);

            $this->setEntidad($entidadPrincipal);

        //  Recuperamos el esquema de la entidad y sus entidades relacionadas
            $this->getSchemaEntity();

        //  Preparamos el builder de SQL
            $this->createInsert($entidadPrincipal);

        //  Recuperamos todos los valores del post que hemos recibido
            $this->processJSONPostData($data);

        // print_r($this->fields);
        // die();

        //  Auditoría
        $this->addField("created", "now()");

        //  Ejecutamos sobre la propiedad SQL
        return $this->Save();

    }

    /** Convierte el JSON del post a un array asociativo arreglando los valores */
    public function processJSONPostData($data)
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
    public function getAll($mainAlias = null, $params = null)
    {

        // Primero nos traemos los datos de la entidad principal teniendo en cuenta los criterios de selección que pudiera tener
        $this->queryToExecute = "select * from " . $this->mainEntity . " ";

        //  Comprobamos el destino del listado
        $isSelect = false;
        if(isset($params['target']))
        {
            $isSelect = ($params['target'] == 'cbo' ? true : false);
        }

        //  Comprobamos el rol que tiene el usuario
        //  FIX: ¿ES LA MANERA CORRECTA DE COMPROBAR O LO PODEMOS AUTOMATIZAR?
            $userData = get_object_vars( $this->getJWTUserData()['data']->userData );
            if($userData['role'] == 'ROLE_ADMINFINCAS')
            {
                //  Hay que comprobar si el tipo de listado es de un select o bien de una entidad
                //       ya que si no intenta hacer el acotado y eso no es correcto
                if(!$isSelect )
                    $this->queryToExecute .= " where usuarioId = " . $userData['id'] . " ";
            }

        //  Comprobamos si hay establecido orden
            $orderBy = (isset($params['orderby']) ? $params['orderby'] : null);
            $orderByType = (isset($params['order']) ? $params['order'] : null);

        //  Validación de propiedad del tipo de orden definido en el querystring
            if(!is_null($orderBy) && !is_null($orderByType) )
            {
                $this->queryToExecute .= " order by $orderBy $orderByType ";
            }else{
                //  Validación de propiedad del tipo de orden en la definición de la entidad
                if( isset($this->entity->orderBy) )
                {
                    if( !is_null($this->entity->orderBy) )
                        $this->queryToExecute .= " order by " . $this->entity->orderBy . " ";

                    if( isset($this->entity->orderType) )
                    {
                        $this->queryToExecute .= ( is_null($this->entity->orderType) ? "" : $this->entity->orderType);
                    }else{
                        //  Por defecto es ASC
                        $this->queryToExecute .= " " . ORDER_BY_ASC;
                    }
                    
                }
            }

        //  Parámetros de paginación
            $limitStart = (isset($params['start']) ? $params['start'] : null);
            $limitLength = (isset($params['length']) ? $params['length'] : null);

        if(!is_null($limitStart) && !is_null($limitLength))
            $this->queryToExecute .= " limit " . $params['start'] . "," . $params['length'];

        // die($this->queryToExecute);

        $this->execute(true,false);

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