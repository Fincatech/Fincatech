<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Database\DatabaseCore;

trait CrudTrait{

    /** Método de guardar entidad en bbdd */
    public function Save()
    {
        if($this->isInsertAction())
        {
            $this->constructInsertSQL();
            //  Ejecutamos sobre la propiedad SQL

                $this->repositorio->queryRaw( $this->getSQL() );
                $data['id'] = $this->repositorio->getLastID(strtolower($this->getEntidad())) - 1;
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

        //  Hay que inicializar los campos por si se van a procesar entidades relacionadas
            $this->fields = [];
            // $entidadPrincipal = strtolower($entidadPrincipal);
        //  TODO:   Evaluar si recorremos cada una de las subentidades
        //          o bien lo hacemos de forma independiente en el controller
        //          En el controller sería lo suyo para obtener el ID retornado de la inserción
            $this->setEntidad($entidadPrincipal);

        //  Recuperamos el esquema de la entidad y sus entidades relacionadas
            $this->getSchemaEntity();

        //  Preparamos el builder de SQL
            $this->createInsert($entidadPrincipal);

        //  Si hay fichero, guardamos el mismo en el almacén y quitamos la info del $data
        //  para evitar problemas a la hora de guardar los datos
        //  Comprobamos si hay fichero adjuntado a la petición
            $ficheroId = $this->getFileInfoFromPostData($data);
            if( $ficheroId != null)
            {
                $data['idfichero'] = $ficheroId;
             
            }
            unset($data['fichero']);

        //  Recuperamos todos los valores del post que hemos recibido
            $this->processJSONPostData($data);

        //  Auditoría
            date_default_timezone_set('Europe/Madrid');
            $this->addField("created", "'" . date('Y-m-d H:i:s') . "'");

        //  Ejecutamos sobre la propiedad SQL
            return $this->Save();

    }

    /** Convierte el JSON del post a un array asociativo arreglando los valores */
    public function processJSONPostData($data)
    {
        //  Si está vacío no procesamos
            if(!is_array($data))
                return;

        //  Recuperamos todos los valores del post que hemos recibido
            foreach($data as $key => $value)
            {
                    // echo 'Key: ' . $key . " - Value: " . $value . PHP_EOL . '<br>';
                    //  Validamos que exista la propiedad en la tabla
                    $value = $this->fixFieldValue($key, $value);
                    $this->addField($key, $value);
            }  
          
    }
    
    ////////////////////////////////////
    /////////  CRUD METHODS  ///////////
    ////////////////////////////////////

    /**
     * Elimina una entidad de la base de datos
     */
    public function Delete($id, $entityObject = null)
    {

        //$moveToHistorial = $this->getEntity()->tipoEliminacion == HIS
        $this->getRepositorio()->deleteSingle(strtolower($this->mainEntity), $id, $this->getEntity()->moveToHistorial, $this->getEntity()->primaryKey, true);

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

        $this->fields = [];

        $this->setEntidad($entidadPrincipal);

        //  Recuperamos el esquema de la entidad y sus entidades relacionadas
            $this->getSchemaEntity();        

        //  Preparamos el builder de SQL
            $this->createUpdate($entidadPrincipal);

        //  Comprobamos si hay fichero adjuntado a la petición
            $ficheroId = $this->getFileInfoFromPostData($data);

            if( $ficheroId != null)
            {
                $data['idfichero'] = $ficheroId;
            }
            unset($data['fichero']);

        //  Hay que meter el ID de la entidad principal
            $data['id'] = $entidadId;

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
        $singleRecord = $this->getById($id);
        // FIX
        //$this->Fill($this->mainEntity);
        return $singleRecord;
    }

    /**
     * Obtiene todos los registros para una entidad
     */
    public function getAll($mainAlias = null, $params = null, $useLoggedUserId = true)
    {

        //  Parámetros aceptados en params
        /*
            filter[{campo1:valor}]
            searchby[campo, valorBusqueda]
            orderby = campo
            order = tipo de ordenación: ASC | DESC
        */

        //  Comprobamos si los datos se recuperan desde la entidad principal o por el contrario
        //  hay que recuperar la información de una vista
            $this->queryToExecute = "select * from ";
            if(isset($params['view']))
            {
                $this->queryToExecute .= strtolower($params['view']) . " ";        
            }else{
                $this->queryToExecute .= strtolower($this->mainEntity) . " ";        
            }

        //  Nos traemos los datos de la entidad principal teniendo en cuenta los criterios de selección 
        //  que pudiera tener

        //  Comprobamos el destino del listado
        //  $isSelect significa que es para un combo 
            $isSelect = false;

            if(isset($params['target']))
            {
                $isSelect = ($params['target'] == 'cbo' ? true : false);
            }

        //  Comprobamos el rol que tiene el usuario
        //  FIXME: ¿ES LA MANERA CORRECTA DE COMPROBAR O LO PODEMOS AUTOMATIZAR?
            $JWTUserData = $this->getJWTUserData();
            if($JWTUserData['status'] != false)
            {

                $userData = get_object_vars( $this->getJWTUserData()['data']->userData );

                if($userData['role'] == 'ROLE_ADMINFINCAS')
                {
                    //  Hay que comprobar si el tipo de listado es de un select o bien de una entidad
                    //       ya que si no intenta hacer el acotado y eso no es correcto
                    if(!$isSelect )
                    {
                        if($useLoggedUserId)
                        {

                            // TOFIX: Hay que quitar el harcode este
                            if($this->mainEntity == 'Comunidad' || $this->mainEntity == 'InformeValoracionSeguimiento')
                            {
                                $this->queryToExecute .= " where usuarioId = " . $userData['id'] . " ";
                            }else{
                                $this->queryToExecute .= " where usercreate = " . $userData['id'] . " ";
                            }

                        }                
                    }
                }

            }
        //  Comprobamos si tiene algún filtro establecido para acotar campos
        /*
            filter[{campo1:valor}]
            filterfield = campo
            filtervalue = valorBusqueda
            search = valor a buscar ?¿
            orderby = campo
            order = tipo de ordenación: ASC | DESC
        */

        //  FIXME: Arreglar esta búsqueda ya que puede dar error
        //  NOTE: Habría que montar vistas para los listados
        if(isset($params['filterfield']) && isset($params['filtervalue']))
        {
            if(strpos($this->queryToExecute, "where") !== false)
            {
                $this->queryToExecute .= " AND ";
            }else{
                $this->queryToExecute .= " where ";
            }

            $params['filteroperator'] = (isset($params['filteroperator']) ? $params['filteroperator'] : ' = ');

            $this->queryToExecute .= ' ' . $params['filterfield'] . $params['filteroperator'] . $params['filtervalue'] . " ";
            
        }

        //  Procesamiento de la búsqueda desde los datatables
        if(isset($params['searchfields']) && @count($params['searchfields']) > 0)
        {
            if(strpos($this->queryToExecute, "where") !== false)
            {
                $this->queryToExecute .= " AND ";
            }else{
                $this->queryToExecute .= " where ";
            }

            $this->queryToExecute .= '(' . $this->processSearch($params['searchfields'], $params['searchvalue']) .')';
        }

        $queryWithoutFilter = $this->queryToExecute;

        //  Comprobamos si hay establecido orden
            $orderBy = (isset($params['orderby']) ? $params['orderby'] : null);
            $orderByType = (isset($params['order']) ? $params['order'] : null);

        //  ORDER BY
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
        
        // $this->entityData['total'] = $this->getTotalRows("select * from " . strtolower($this->mainEntity) . " ");
        $this->entityData['total'] = $this->getTotalRows($queryWithoutFilter);
        // $this->entityData['total'] = $this->getTotalRows("select * from " . strtolower($this->mainEntity) . " ");
        $this->entityData['filtered'] = $this->getTotalRows($this->queryToExecute);

        return $this->entityData;

    }

    /** Recupera todos los registros
     * @param array $params. Contiene la definición de los filtros establecidos para el listado incluyendo la paginación
     */
    public function List($params = null, $useLoggedUserId = true)
    {
        return $this->getAll(null, $params, $useLoggedUserId);
    }

    /** Recupera los registros de una entidad mediante campos de búsqueda $key => $value */
    public function Search($dataToSearch)
    {
        if(!is_array($dataToSearch))
        {
            return ['error' => 'Parámetros de búsqueda no válidos'];
        }else{

            //  Validamos que vengan al menos los campos informados
            if(!isset($dataToSearch['fields']))
                return ['error' => 'Parámetros de búsqueda no válidos'];

            $sql = "select * from " . strtolower($this->mainEntity) . " where ";
            $sql .= $this->ProcessFieldsToSearch($dataToSearch);
            $this->queryToExecute = $sql;
            $this->execute(false, null);
            return $this->entityData;
        }
    }

    public function ProcessFieldsToSearch($searchFields)
    {
        //  Estructura del JSON que espera recibir
        /*
            {
                "fields": [
                    {
                        "field": "email",
                        "search": "oscar@fincatech.es",
                        "type": "string",
                        "searchtype": "like"
                    },
                    {
                        "created": "oscar@fincatech.es",
                        "type": "date",
                        "searchtype": "eq"
                    }

                ],
                "order": [
                    {
                        "field": "created",
                        "type": "ASC"
                    }
                ],
                "resultsnumber": "-1"
            }           
        */
        $searchSQL = '';

        for($xField = 0; $xField < count($searchFields['fields']); $xField++)
        {
            //  Campo
            $searchSQL .= DatabaseCore::PrepareDBString($searchFields['fields'][$xField]['field']);

            //  Valor
            $valor = DatabaseCore::PrepareDBString($searchFields['fields'][$xField]['search']);
            // Tipo de dato
            $dataType = DatabaseCore::PrepareDBString($searchFields['fields'][$xField]['type']);    
            //  Tipo de búsqueda
            $searchType = DatabaseCore::PrepareDBString($searchFields['fields'][$xField]['searchtype']);
            
            //  Tipo de búsqueda
            switch($searchType)
            {
                case 'like': // Like
                    $searchSQL .= ' like ';
                    break;
                case 'eq': // Equals
                    $searchSQL .= ' = ';
                    break;
                case 'gt': // Greater than
                    $searchSQL .= ' >= ';
                    break;
                case 'lt': // Lower than
                    $searchSQL .= ' <= ';
                    break;
                case 'g': // Greater
                    $searchSQL .= ' > ';
                    break;
                case 'l': // Lower
                    $searchSQL .= ' < ';
                    break;
            }

            //  Valor
            if($searchType == 'like' && $dataType === 'string')
            {
                $searchSQL .= "'%" . $valor . "%'";
            }else{
                $searchSQL .= ($dataType === 'string' || $dataType === 'date') ? "'" . $valor . "'" : $valor;
            }

            if($xField + 1 < count($searchFields['fields']))
                $searchSQL .= " AND ";
        }

        //  Anulamos el último AND
        //$searchSQL = substr($searchSQL, 0, -4);

        //=========================================================
        //                  ORDEN de los resultados
        //=========================================================
        if(isset($searchFields['order']))
        {
            $searchSQL .= ' order by ';
            for($xOrder = 0; $xOrder < count($searchFields['order']); $xOrder++)
            {
                //  Campo
                $searchSQL .= $searchFields['order'][$xOrder]['field'] . ' ';
                //  Tipo
                $searchSQL .= $searchFields['order'][$xOrder]['type'];

                if($xOrder + 1 < count($searchFields['order']))
                    $searchSQL .= ", ";                
            }
        }

        //=========================================================
        //              Limite de los resultados
        //=========================================================
        if(isset($searchFields['resultsnumber']))
        {
            $searchSQL .= intval($searchFields['resultsnumber']) > 0 ? ' limit ' . $searchFields['resultsnumber'] : '';
        }

        return $searchSQL;
    }

}