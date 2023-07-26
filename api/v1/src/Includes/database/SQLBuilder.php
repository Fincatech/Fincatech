<?php

namespace HappySoftware\Database;

class SQLBuilder{

    private $select;
    private $where;
    private $orderby;
    private $leftjoin;
    private $outerjoin;
    private $rightjoin;

    private function getSelect(){
        return $this->select;
    }
    private function setSelect($value){
        $this->select = $value;
        return $this;
    }

    private function getWhere(){
        return $this->where;
    }
    private function setWhere($value){
        $this->where = $value;
        return $this;
    }

    private function getOrderBy(){
        return $this->orderby;
    }
    private function setOrderBy($value){
        $this->orderby = $value;
        return $this;
    }

    public function __construct(){

    }

    /**
     * @param Array $tables. Ejemplo: ['nombretabla','nombretabla2']
     */
    public function Select($tables){
        if(!is_array($tables))
        {
            throw new \Exception('$tables debe ser un Array. Select SQLBuilder');
        }else{
            $this->setSelect("SELECT " . $tables);
            return $this;
        }
    }


    public function Where($condition)
    {
        $this->setWhere($condition);
        return $this;
    }


}