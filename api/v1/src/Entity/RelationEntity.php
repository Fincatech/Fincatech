<?php

namespace HappySoftware\Entity\DatabaseHelper;

use HappySoftware\Controller\ConfigController;

class Relation{

    // public $table;
    public $sourceColumn;
    public $targetColumn;
    public $fieldType;

    /** @var string
     * Admite [I]nside o [O]utside. Inside indica que es una relación de 1 a n y Outside que es una relación de n a 1
     */
    public $relationType;


    /** 
     * Indica si se puede devolver la infromación del schema de la entidad
     * @var boolean
     */
    public $canReturnSchema = true;
        
    /**
     * Indica si la entidad puede ser actualizada. Se establece a false cuando es una entidad de solo lectura por ejemplo
     * @var boolean
     */
    public $readOnly = false;

    /**
     * Indica si cuando se elimina un registro de la entidad principal se eliminarán los registros asociados por cada entidad
     */
    public $deleteOnCascade = true;

    /**
     * Indica si la eliminación es física o lógica. [L]: Lógico [P]: Físico
     */
    public $deleteMode = 'P';

    /**
     * Permite utilizar un alias para la entidad
     */
    public $alias;

    public function __construct()
    {
        
    }

    public function getDocTest()
    {

    }
}