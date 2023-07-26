<?php

namespace HappySoftware\Model\Traits;

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

    /**
     * @param Array $fields Array con el nombre de los campos
     */
    public function processSearch($fieldsToSearch, $valueToSearch)
    {

        $filterQuery = '';

        if( is_array($fieldsToSearch) )
        {

            for($iField = 0; $iField < count($fieldsToSearch); $iField++)
            {

                $filterQuery .= $fieldsToSearch[$iField]['field'];

                if($fieldsToSearch[$iField]['operator'] == "%")
                {
                    $filterQuery .= " like '" . '%' . $valueToSearch . '%' . "'";
                }else{
                    $filterQuery .= " = " . $valueToSearch;
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

}