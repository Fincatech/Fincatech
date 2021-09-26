<?php

namespace HappySoftware\Model\Traits;

use HappySoftware\Controller\Traits\ConfigTrait;

trait EntityTrait{


    /** Inicializa la entidad y las posibles relaciones que tenga establecidas
     * @param string $entity Nombre de la entidad
     */
    protected function InitEntity($entity)
    {

        $entityNameSpace = ConfigTrait::getNamespaceName() . 'Entity\\' . $entity;

        include_once(ABSPATH . 'src/Entity/'. $entity . '.php');
        
        $this->entity = new $entityNameSpace();

        // Como previamente debe estar establecida las relaciones por medio de la entidad comprobamos y asignamos si es el caso
        if($this->entity->getRelations() != null)
        {
            $this->haveEntityRelations = true;
            $this->entityRelations = $this->entity->getRelations();
        }else{
            $this->entityRelations = null;
            $this->haveEntityRelations = false;
        }        

    }


    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($name, $class)
    {
        //  Se incluye la entidad
        include_once(ABSPATH .'src/Entity/'.$name.'.php');
        //  Establecemos la entidad principal para el modelo
        $this->$name = new $class();
        
    }

    /** Devuelve el valor de EntityRelations que contiene la información de las entidades relacionadas con la entidad principal
     * @return Array EntityRelations
     */
    public function getRelations()
    {
        return $this->entityRelations;
    }

    public function setRelations($relations)
    {
        $this->entityRelations = $relations;
        $this->haveEntityRelations = true;
        return $this;
    }

    /**
     * Recupera un registro por su ID principal y campo ID. Únicamente recupera la entidad principal. El ID en base de datos debe ser
     * un valor de tipo INT.
     * TODO: Se deberá comprobar el tipo de clave principal por si tiene otro tipo definido en la entidad
     */
    public function getSingleEntityById($value)
    {
        $resultData = $this->repositorio->queryRaw("select * from $this->mainEntity where id = $value");
        $this->execute(false);
        return $this->entityData;
    }

    /**
     * Obtiene una entidad cualquiera mediante el par campo-valor indicado en los parámetros
     * @param $entity String. Nombre de la entidad
     * @param $field String. Nombre del campo que se va a utilizar para la consulta
     * @param $value String. Valor que se va a utilizar para la búsqueda
     */
    public function getSingleEntityByField($entity, $field, $value)
    {
        $resultData = $this->repositorio->queryRaw("select * from $entity where $field = $value");
        $this->execute(false);
        return $this->entityData;    
    }

    public function getEntityByField($entity, $field, $value)
    {
        $this->queryToExecute = "select * from $entity where $field = $value";
        $this->execute(false);
        return $this->entityData;    
    }

    /** Devuelve el tipo de eliminación de la entidad principal */
    public function getTipoEliminacionEntidadPrincipal()
    {
        return $this->$this->mainEntity->tipoEliminacion;
    }

   /** Obtiene los datos relacionados con la entidad que se está consultando */
   private function getDataRelations($includeSchemaDefinition = false)
   {
       // Si no hay registros salimos directamente o si no tiene relaciones
       if(count( $this->entityData[$this->mainEntity] ) == 0 || $this->haveEntityRelations == false)
           return;

           //  Iteramos sobre todos los resultados de la entidad principal para poder realizar las relaciones correspondientes
           //  en base a las definiciones de relación establecidas desde la entidad correspondiente
           for($x = 0; $x < count( $this->entityData[$this->mainEntity] ); $x++)
           {
               
               // Comprobamos si hay relaciones establecidas para poder procesarlas
               if($this->haveEntityRelations)
               {
                   $deb = false;   //debug
                   //  Recuperamos los datos y la información de cada una de las entidades relacionadas con la principal
                   foreach($this->entityRelations as $relacion)
                   {
                       $valor = $this->entityData[$this->mainEntity][$x][$relacion->targetColumn];
                       $relationQuery = null;

                       //  Generamos la query para la relación y nos traemos los datos contra la entidad principal
                       //  Comprobamos primero el tipo de relación, si es desde la entidad principal hacia fuera o a la inversa
                       switch($relacion->relationType)
                       {
                            //  Busca en la tabla relacionada estableciendo la relación entre
                            //  sourcecolumn (Campo de la entidad principal) y targetcolumn (Campo de la Tabla que se va a relacionar)
                           case RELACION_INSIDE:
                               //  inside
                               $relationQuery = "
                               select 
                                   rel.* 
                               from 
                                   " . $relacion->table . " rel, " . $this->mainEntity . " pri
                               where 
                                   pri." . $relacion->sourceColumn . " = rel." . $relacion->targetColumn;

                               //  Tenemos que acotar la búsqueda al campo de la relación principal                                
                               $relationQuery .= " and pri." . $relacion->sourceColumn . " = ";
                               break;
                           case RELACION_OUTSIDE:
                               //  outside
                               $relationQuery = "
                               select 
                                   relacionada.* 
                               from 
                               " . $relacion->table . " relacionada, " . $this->mainEntity . " principal
                               where 
                                   principal." . $relacion->targetColumn . " = relacionada." . $relacion->sourceColumn;                            

                               //  Tenemos que acotar la búsqueda al campo de la relación principal
                               $relationQuery .= " and principal." . $relacion->targetColumn . " = ";   
                               break;
                           case RELACION_INVERSA:
                               //  INVERSA
                               if ( !is_null($this->entityData[$this->mainEntity][$x][$relacion->sourceColumn]) && $this->entityData[$this->mainEntity][$x][$relacion->sourceColumn] != "")
                               {
                                    $relationQuery = "
                                    select 
                                        rel.* 
                                    from 
                                        " . $relacion->table . " rel
                                    where 
                                        rel." . $relacion->targetColumn . " = " . $this->entityData[$this->mainEntity][$x][$relacion->sourceColumn];
                               }

                               break;
                            //  TODO se utiliza para poder especificar los alias para salir del estándar
                            case RELACION_CUSTOM:
                                if ( !is_null($this->entityData[$this->mainEntity][$x][$relacion->sourceColumn]) && $this->entityData[$this->mainEntity][$x][$relacion->sourceColumn] != "")
                                {
                                    $relationQuery = "
                                    select 
                                        rel.* 
                                    from 
                                        " . $relacion->table . " rel
                                    where 
                                        rel." . $relacion->targetColumn . " = " . $this->entityData[$this->mainEntity][$x][$relacion->sourceColumn];
                                }
                                break;
                       }

                       if($relacion->relationType != RELACION_INVERSA)
                           $relationQuery .= $this->getFormatedKeyValue($relacion->fieldType, $valor);


                       //   Ejecutamos la consulta
                       if( !is_null($relationQuery) )
                       {
                           $resultado = $this->repositorio->queryRaw($relationQuery);
                       }else{
                           $resultado = null;
                       }
                       
                       if(!$deb)
                       {
                          // echo $relationQuery . '<br>';
                          $this->entityData[$this->mainEntity][$x][$relacion->table] = $this->mapMysqliResultsToObject( $resultado );
                       }else{
                           echo $relationQuery . '<br>';
                       }

                   }

               }

           } 

           if($deb)
           {
               print_r( $this->entityData);
               die();
           }

       if($includeSchemaDefinition)
           $this->getSchemaEntity($includeSchemaDefinition);

   }

   /** TODO: */
   public function isEntityName()
   {

   }

}