<?php

namespace HappySoftware\Model\Traits;

trait DatabaseTrait{

    /** Propiedad que se utiliza para ir almacenando la query creada */
    private $sqlBuilder;

    private $sqlToExecute;

    /** Se utiliza para almacenar los valores de los campos que se van a insertar en la base de datos 
     *  Es un array asociativo
    */
    private $fields = [];

    private $insert;
    private $update;
    private $delete;

    /** Return SQL Created */
    public function getSQL()
    {
        if($this->sqlBuilder != "")
        {
            //  Comprobamos si es un update o un insert

        }

        return $this->sqlToExecute;
    }

    /** Set SQL */
    public function setSQL($value)
    {
        $this->sqlToExecute = $value;
    }

    /** Devuelve la sentencia de insert formada para poder ejecutarse */
    private function constructInsertSQL()
    {
        //  Añadimos los nombres de los campos
        $this->builder( implode(",", array_keys($this->fields)) );

        $this->builder( ") values (" );

        //  Añadimos los valores de los campos
        $this->builder( implode(",", $this->fields) . ")");

        // Establecemos la sentencia para ejecutar
        $this->setSQL( $this->sqlBuilder );

        //  Devolvemos la sentencia creada
        return $this->getSQL();
    }

    /** Construye la sentencia de actualización de la entidad principal */
    private function constructUpdateSQL()
    {
        //  Recorremos todo el objeto y vamos asignando los valores

        //  Añadimos los nombres y valores de los campos
        //$this->builder( implode(",", array_keys($this->fields)) );

        foreach($this->fields as $key=>$value)
        {
            $this->builder($key . "=" . $value . ", ");
        }
        
        // Arreglo para quitar la última coma
        $this->sqlBuilder = substr($this->sqlBuilder, 0,-2);

        //  Establecemos el campo por el que vamos a actualizar
        $this->builder(" WHERE id=". $this->fields['id']);

        // Establecemos la sentencia para ejecutar
        $this->setSQL( $this->sqlBuilder );

        //die($this->getSQL());

        //  Devolvemos la sentencia creada
        return $this->getSQL();
    }

    /** Constructor de la sentencia SQL. Va concatenando el string que se le pasa por parámetro
     * @param string $value Valor a concatenar
     * @return $this
     */
    private function builder($value)
    {
        $this->sqlBuilder .= $value;
        return $this;
    }

    /** Devuelve si es una inserción
     * @return boolean Estado
     */
    private function isInsertAction()
    {
        return $this->insert;
    }

    /** Devuelve si es una actualización
     * @return boolean Estado
     */    
    private function isUpdateAction()
    {
        return $this->update;
    }

    /** Devuelve si es una eliminación
     * @return boolean Estado
     */
    private function isDeleteAction()
    {
        return $this->delete;
    }

    /** Establece la acción que se va a realizar para la entidad */
    private function setAction($_insert, $_update, $_delete)
    {
        $this->sqlBuilder = "";
        $this->sqlToExecute = "";
        $this->insert = $_insert;
        $this->update = $_update;
        $this->delete = $_delete;
        return $this;
    }

    /** Inicializa la sentencia de insert */
    public function createInsert($table)
    {
        $this->setAction(true, false, false); 
        $this->builder("INSERT INTO $table(");
    }

    /** Inicializa la sentencia de update */
    public function createUpdate($table)
    {
        $this->setAction(false, true, false);
        $this->builder("UPDATE $table SET ");
    }

    /** 
     * Agrega un campo
     */
    public function addField($field, $value)
    {
        $this->fields[$field] = $value;
        return $this;
    }

    /**
     * Arregla el valor del dato para meterle comillas en el caso que sea un string ya que por el post json no lo sabemos
     */
    public function fixFieldValue($field, $value)
    {       
        //  Accedemos a la entidad
        if( isset( $this->entityData['schema'][$this->mainEntity]['definitions'][$field] ) )
        {
             return $this->getFormatedKeyValue( $this->entityData['schema'][$this->mainEntity]['definitions'][$field]->fieldType, $value);
        }else{
             return "null";
        }
    }

    /** Devuelve el total de registros de una entidad */
    public function getTotalRows($entity)
    {
        return $this->executeCount($entity);
    }

}