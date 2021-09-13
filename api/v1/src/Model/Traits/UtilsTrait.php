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

    /** Devuelve el valor entre comillado según tipo */
    private function getFormatedKeyValue($tipo, $valor)
    {
        $tipo = trim(strtolower($tipo));

        if($tipo == "string" || $tipo == "varchar" || $tipo == "char" || $tipo == "text" ||
            $tipo == "datetime" || $tipo == "date")
        {
            //  Para los campos de fecha hay que comprobar que si viene vacío o null hay que establecer la fecha de hoy
            if(($valor == '' || $valor == null) && ($tipo == "datetime" || $tipo == "date") )
            {
                return "null";
            }

            return "'" . $this->getRepositorio()::PrepareDBString($valor) ."' ";
        }else{
            if($valor == "")
            {
                return "null";
            }else{
                return $valor;
            }
        }
    }

    protected function filterResults($datos, $entity, $key, $value)
    {

        if(is_array($datos))
        {  
            $x = 0;
            foreach ($datos[$entity] as $index => $object) 
            {
                if($object[$key] != $value)
                {
                    unset($datos[$entity][$index]);
                    // array_splice($datos, $x);
                }
                $x++;
            }
        }
        $datosTmp = $datos[$entity];
        // $datos[$entity] = [];
        // $datos[$entity] = [$datosTmp];
        return $datos;
    }

    /** Convierte los resultados devueltos por la conexión MySQLi a un objeto
     * @param $results MySQLi Object. Conjunto de registros que se va a mapear
     * @return Array Array asociativo con los resultados obtenidos. Null si está vacío el conjunto de resultados
     */
    private function mapMysqliResultsToObject($results)
    {

        $arrayResults = [];

        if(mysqli_num_rows($results) > 0)
        {
            while($row = mysqli_fetch_assoc($results))
            {
                $arrayResults[] = $row;
            }
        }

        return $arrayResults;

    }

}