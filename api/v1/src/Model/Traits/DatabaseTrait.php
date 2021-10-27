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

        // if($this->getEntidad() == 'empleadoempresa')
        // {
            // echo('-----------antes de fields');
            // print_r($this->fields);
            // die('----------fields');
            // die('3s');
        // }

       //  si ya viene informado el campo usercreate cogemos el valor y 
        //  luego lo utilizamos
        $userCreateId = null;
        
        if(isset($this->fields['usercreate']))
        {
            $userCreateId = $this->fields['usercreate'];
            unset($this->fields['usercreate']);
        }

        //  Añadimos los nombres de los campos
        $this->builder( implode(",", array_keys($this->fields)) );

        $this->builder( ", usercreate) values (" );

        //  Añadimos los valores de los campos
        $this->builder( implode(",", $this->fields) );

        //  Si no viene informado tomamos el del usuario autenticado
        if(is_null($userCreateId))
        {
            $userCreateId = $this->getLoggedUserId(); 
        }

        //  Añadimos el usuario para la auditoria
        $this->builder( ',' . $userCreateId . ')' );

        // Establecemos la sentencia para ejecutar
        $this->setSQL( $this->sqlBuilder );

        //  Devolvemos la sentencia creada
        if($this->getEntidad() == 'comunidad')
        {
            // print_r($this->fields);
            // die($this->getSQL());
        } 
        return $this->getSQL();
    }

    /** Construye la sentencia de actualización de la entidad principal */
    private function constructUpdateSQL()
    {
        //  Recorremos todo el objeto y vamos asignando los valores
        foreach($this->fields as $key=>$value)
            $this->builder($key . "=" . $value . ", ");
        
        // Arreglo para quitar la última coma
        $this->sqlBuilder = substr($this->sqlBuilder, 0,-2);

        //  Establecemos el campo por el que vamos a actualizar
        $this->builder(" WHERE id=". $this->fields['id']);

        // Establecemos la sentencia para ejecutar
        $this->setSQL( $this->sqlBuilder );

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
        $table = strtolower($table);
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

            if($valor == 'now()')
            {
                return $valor;
            }else{
                return "'" . $this->getRepositorio()::PrepareDBString($valor) ."' ";
            }

        }else{
            if($valor === '')
            {
                return "null";
            }else{
                return $valor;
            }
        }
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