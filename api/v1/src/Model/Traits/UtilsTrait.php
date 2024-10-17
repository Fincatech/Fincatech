<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Database\DatabaseCore;

trait UtilsTrait{


    /**
     * Establece la paginación con sus límites
     * @param int $page Número de página de resultados
     * @param int $resultsLimit Número de resultados a devolver
     */
    public function setPagination($page, $resultsLimit)
    {
        $this->pagination = true;
        $this->pageResultsLimit = $resultsLimit;
        $this->pageResults = $page;
    }

    public function getRepositorio()
    {
        return $this->repositorio;
    }

    /** Filtra los resultados según el campo y valor deseado para una entidad */
    public function filterResults($datos, $entity, $key, $value, $relEntity = null)
    {

        if(is_array($datos))
        {  
            //  Comprobamos si la entidad sobre la que se va a comprobar es un array
            if( is_null($relEntity))
            {
                /* TOFIX: Hay veces que da error y hay que revisar por qué
                if(!is_null($datos[$entity]))
                {
                    foreach ($datos[$entity] as $index => $object) 
                    {
                        if($object[$key] != $value)
                        {
                            unset($datos[$entity][$index]);
                        }
                    }
                    $datos[$entity] = array_values($datos[$entity]);
                }else{
                    $datos[$entity] = null;
                }
                */
                foreach ($datos[$entity] as $index => $object) 
                {
                    if($object[$key] != $value)
                    {
                        unset($datos[$entity][$index]);
                    }
                }
    
                $datos[$entity] = array_values($datos[$entity]);

            }else{

                $datosFiltrados = [];

                foreach ($datos[$entity] as $index => $object) 
                {
                    $indiceTemporal = $index;
                    $eliminar = false;

                    foreach($object[$relEntity] as $relIndex => $relObject)
                    {

                        if($relObject[$key] == $value)
                        {
                            $datosFiltrados[] = $object;
                            break;
                        }
                    }
                }
                return $datosFiltrados;
            }

        }

        return $datos;

    }

    /** Convierte los resultados devueltos por la conexión MySQLi a un objeto
     * @param $results MySQLi Object. Conjunto de registros que se va a mapear
     * @return Array Array asociativo con los resultados obtenidos. Null si está vacío el conjunto de resultados
     */
    public function mapMysqliResultsToObject($results)
    {

        $arrayResults = [];

        if(!$results)
            return $arrayResults;

        if(mysqli_num_rows($results) > 0)
        {
            $arrayResults = mysqli_fetch_all($results, MYSQLI_ASSOC);            
            //  Liberamos el conjunto de resultados
            mysqli_free_result($results);
        }


        //  FIX: Mirar a ver si es mejor devolver un array cuando solo tiene un registro
        //       o bien detectar si es un listado o un registro único
        //       o bien detectar si es la entidad principal para no asignarlo como array

        return $arrayResults;

    }

    /** TOFIX: Revisar bien la parte de construcción de la búsqueda
     * @param Array $fields Array con el nombre de los campos
     */
    public function processSearch($fieldsToSearch, $valueToSearch)
    {


        /////////////////////////////////////////////////////////////////////
        ///
        ///                          PROPIEDADES
        ///
        /////////////////////////////////////////////////////////////////////
        ///
        /// value
        /// operator
        /// condition
        ///
        /////////////////////////////////////////////////////////////////////

        $filterQuery = '';

        if( is_array($fieldsToSearch) )
        {

            for($iField = 0; $iField < count($fieldsToSearch); $iField++)
            {

                //  Por cada campo de búsqueda hay que comprobar si tiene definido un valor
                //  ya que si lo tiene definido cogemos ese, si no, cogemos el que viene por defecto
                $_valueToSearch = $valueToSearch;
                if(isset($fieldsToSearch[$iField]['value'])){
                    $_valueToSearch = $fieldsToSearch[$iField]['value'];
                }

                //  Campo donde se va a buscar
                $filterQuery .= $fieldsToSearch[$iField]['field'];

                //  Comprobamos si viene informado el tipo de operación
                //  Por defecto es =
                $_searchOperator = isset( $fieldsToSearch[$iField]['operator'] ) ? $fieldsToSearch[$iField]['operator'] : '=';

                if($_searchOperator == "%")
                {
                    $filterQuery .= " like '" . '%' . $_valueToSearch . '%' . "'";
                }else{
                    $filterQuery .= " = " . $_valueToSearch;
                }

                //  Tipo de condición lógica
                if(count($fieldsToSearch) > 1)
                {
                    $filterQuery .= isset($fieldsToSearch[$iField+1]['condition']) ? ' ' . $fieldsToSearch[$iField+1]['condition'] . ' ' : ' and ';
                }else{
                    $filterQuery .= ' and ';
                }

            }
            $filterQuery = substr( $filterQuery, 0, strlen($filterQuery) - 4);
        }

        return $filterQuery;

    }


    /**
     * 
     */
    public function processFilterFields($filters)
    {
        //  Query que se va a filtrar
        $filterQuery = '';
        //  Control de si tiene múltiples filtros para aplicar
        $multipleFilter = false;

        //  Comprobamos primero si son varios filtros los que hay que establecer
        if(isset($filters['filters']))
            $multipleFilter = true;

        //  Filtros por array
        if($multipleFilter)
        {
            //  Inicializamos la variable de la query que se va a montar
            $filterSQL = '';

            //  iteramos sobre todos los campos           
            foreach($filters['filters'] as $filter)
            {

                //  Valor
                $value =  DatabaseCore::PrepareDBString($filter['filtervalue']);
                //  Si no tiene valor, continuamos 
                if($value == ''){
                    continue;
                }

                //  Campo de búsqueda
                $field = $filter['filterfield'];

                //  Tipo de filtro. Por defecto es =
                $filterOperator = isset($filter['filteroperator']) ? $filter['filteroperator'] : 'eq';

                //  Tipo de operador
                switch($filterOperator)
                {
                    case 'like': // Like
                        $filterCondition = ' like ';
                        break;
                    case 'eq': // Equals
                        $filterCondition = ' = ';
                        break;
                    case 'gt': // Greater than
                        $filterCondition = ' >= ';
                        break;
                    case 'lt': // Lower than
                        $filterCondition = ' <= ';
                        break;
                    case 'g': // Greater
                        $filterCondition = ' > ';
                        break;
                    case 'l': // Lower
                        $filterCondition = ' < ';
                        break;
                }

                //  Construimos la query temporal con el campo y el tipo
                $filterSQL .= $field . $filterCondition . ' ';
                //  Comprobamos el tipo de dato para terminar de construir la query
                $filterType = isset($filter['filtertype']) ? $filter['filtertype'] : "string";
                switch ($filterType)
                {
                    case 'string':
                    case 'str':
                    case 'date':
                        $filterSQL .= "'" . $value . "' ";
                        break;
                    case 'int':
                    case 'float':
                    case 'double':
                        $filterSQL .= $value . ' ';
                        break;
                }

                $filterSQL .= ' and ';

            }

            //  Si tiene filtro detectado lo inyectamos en la query
            if($filterSQL != '')
            {
                if(strpos($this->queryToExecute, "where") !== false)
                {
                    $filterQuery .= " AND ";
                }else{
                    $filterQuery .= " where ";
                }

                $filterSQL = substr($filterSQL, 0, -4);
                //  Extraemos el último AND del string
                $this->queryToExecute .= $filterQuery . $filterSQL;

            }

        }


        //  Filtro simple
        if(!$multipleFilter && isset($filters['filterfield']) && isset($filters['filtervalue']))
        {

            if(strpos($this->queryToExecute, "where") !== false)
            {
                $filterQuery .= " AND ";
            }else{
                $filterQuery .= " where ";
            }

            $filters['filteroperator'] = (isset($filters['filteroperator']) ? $filters['filteroperator'] : ' = ');

            $filterQuery.= ' ' . $filters['filterfield'] . $filters['filteroperator'] . $filters['filtervalue'] . " ";
            $this->queryToExecute .= $filterQuery;
        }

        return $filterQuery;

    }

}