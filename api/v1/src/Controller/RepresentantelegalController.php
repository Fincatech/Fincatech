<?php

/*
    $this->EjemploModel hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RepresentantelegalModelModel;
use Fincatech\Controller\FrontController;
use HappySoftware\Controller\HelperController;
use Happysoftware\Controller\Traits;

class RepresentantelegalController extends FrontController{

    public $RepresentantelegalModel;

    public function __construct($params = null)
    {
        parent::__construct();
        $this->InitModel('Representantelegal', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {

        //  Comprobamos que vengan las 2 imágenes en el post
        if( trim($datos['frontImageBase64']) === '' && trim($datos['rearImageBase64']) === '')
        {
            //  Si no vienen, devolvemos el error
            return HelperController::errorResponse('error','Las imágenes no han sido adjuntadas', 200);
        }

        $frontDocumentId = $this->uploadFile($datos['frontImageName'], $datos['frontImageBase64'], true);
        $rearDocumentId = $this->uploadFile($datos['rearImageName'], $datos['rearImageBase64'], true);

        //  Llenamos el modelo
        $this->RepresentantelegalModel->SetAdministradorId($datos['administradorid'])
            ->SetNombre( $datos['nombre'] )
            ->SetApellido( $datos['primerapellido'] )
            ->SetApellido2( $datos['segundoapellido'] )
            ->SetEmail( $datos['email'] )
            ->SetDocumentoIdentificativo( $datos['documento'] )
            ->SetImagenFrontal( $frontDocumentId )
            ->SetImagenTrasera( $rearDocumentId )
            ->SetTelefono( $datos['telefono'] )
            ->SetObservaciones( $datos['observaciones'] )
            ->SetEstado( $datos['estado'] );

        return $this->RepresentantelegalModel->_Save();

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        //  Comprobamos si vienen las imágenes informadas, de ser así, hay que recuperar las anteriores
        //  eliminar el fichero y el registro y adjuntar la nueva
        return $this->RepresentantelegalModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RepresentantelegalModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RepresentantelegalModel->Delete($id);
    }

    public function Get($id)
    {
        $representanteLegal = $this->RepresentantelegalModel->Get($id);
        //  Recuperamos la imagen frontal
        //->SetImagenFrontal( $frontDocumentId )
        //->SetImagenTrasera( $rearDocumentId )
        //  Recuperamos la imagen trasera
        
        return $representanteLegal;
    }

    public function getRepositorio()
    {
        return parent::GetHelperModel()->getRepositorio();
    }

    public function List($params = null)
    {
       return $this->RepresentantelegalModel->List($params);
    }

    public function ListByAdministradorId($administradorId)
    {
        $datos['representantelegal'] = $this->RepresentantelegalModel->ListByAdministradorId($administradorId);
        return HelperController::successResponse($datos);
    }

}