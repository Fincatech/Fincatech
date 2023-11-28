<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use \PHPMailer\PHPMailer\PHPMailer;

use Fincatech\Controller;
use Fincatech\Controller\FrontController;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\ConfigTrait;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });    

    /** Realiza el logout en el sistema */
    $app->get('/logout', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('login');

        $response->getBody()->write( $frontController->context->logout() );

        return $response;  

    });

    //  Seguridad de la aplicación
    $app->post('/checklogin', function(Request $request, Response $response, array $params ): Response
    {

        $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        //  Instanciamos el controller del login
        $frontController->Init( 'login', $data );

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->checkLogin( $data ) );
        
        return $response;

    });  

    //  Cambio de password para el usuario actual
    $app->post('/changepassword', function(Request $request, Response $response, array $params ): Response
    {

        //$data = $request->getParsedBody();
            $body= file_get_contents("php://input"); 
            $data = json_decode($body, true);

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        //  Instanciamos el controller del login
            $frontController->Init( 'login', $data );



        $response->getBody()->write( $frontController->context->changePassword( $data ) );
        
        return $response;

    });  

    //  Reset password
    $app->post('/resetpassword', function(Request $request, Response $response, array $params ): Response
    {

        //$data = $request->getParsedBody();
            $body= file_get_contents("php://input"); 
            $data = json_decode($body, true);

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        
        //  Instanciamos el controller del login
            $frontController->Init( 'login', $data );



        $response->getBody()->write( $frontController->context->resetPassword( $data ) );
        
        return $response;

    });  

    //  Renderizado de vistas
    $app->post('/getview', function(Request $request, Response $response, array $params ): Response
    {

        $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        
        $response->getBody()->write( $frontController->renderView( $data ) );
        return $response;

    });    

    /** Comprueba si un usuario tiene RGPD aceptado o no */
    $app->get('/user/{idusuario}/rgpd', function(Request $request, Response $response, array $params )
    {

        $idUsuario = $params['idusuario'];

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Usuario');
        $usuario = $frontController->context->Get($idUsuario);

        $data = [];
        $data['rgpd'] = $usuario['Usuario'][0]['rgpd'];

        $response->getBody()->write( HelperController::successResponse( $data  ) );

        return $response;  
    
    });

    $app->get('/userinfo', function(Request $request, Response $response, array $params )
    {
        // Instanciamos el controller principal
        //$frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        //$frontController = new $frontControllerName();
        //$frontController->Init('Login');

        $response->getBody()->write( HelperController::successResponse( null ) );

        return $response;  

    
    });

    //  EVALUAR QUE LOS LISTADOS SE HAGAN POR POST Y/O POR GET TAMBIÉN
    //  CUANDO ES POST ES PORQUE TIENE PAGINACIÓN Y DEMÁS FILTROS
    $app->post('/{controller}/create', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->Create($params['controller'], $data));
        return $response;

    });

    //  EVALUAR QUE LOS LISTADOS SE HAGAN POR POST Y/O POR GET TAMBIÉN
    //  CUANDO ES POST ES PORQUE TIENE PAGINACIÓN Y DEMÁS FILTROS
    $app->put('/{controller}/{id}', function(Request $request, Response $response, array $params ): Response
    {

        $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        //  Validamos si ha proporcionado el id
        if(!isset($params['id']) && @empty($params['id']) && @!is_numeric($params['id']))
        {
            $response->getBody()->write( HelperController::errorResponse("", "No existe el ID proporcionado", 403) );
        }else{
            $response->getBody()->write( $frontController->Update( $params['controller'], $data, $params['id']));
        }

        return $response;

    });

    //  Punto de entrada para get con ID
    $app->get('/{controller}/{id:[0-9]+}', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init($params['controller']);
        
        $response->getBody()->write( $frontController->Get($params['id']) );
        return $response;  

    });

    /** Punto de entrada para delete. Se utiliza para eliminar una entidad y sus relaciones */
    $app->delete('/{controller}/{id}', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        
        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->Delete($params['id']) );

        return $response->withHeader('Content-Type', 'application/json');  

    });

    /** Recupera el schema para una entidad y sus posibles entidades relacionadas */
    $app->get('/{controller}/schema', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->GetSchemaAction() );

        return $response;  

    });

    /** Punto de entrada para get. Se utiliza para listar todo */
    $app->get('/{controller}/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);
        $queryString = $request->getQueryParams();
        
        $response->getBody()->write( $frontController->List($queryString) );

        return $response;  

    });

    /**
     * Búsqueda de registros para el controller especificado
     */
    $app->post('/{controller}/search', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->Search($data));
        return $response;

    });

    $app->get('/comunidad/listmenu', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');
        $queryString = $request->getQueryParams();
        
        $response->getBody()->write( HelperController::successResponse($frontController->context->ListComunidadesMenu($queryString)) );

        return $response;  

    });

    //  Endpoint para devolver tablas ya construidas
    $app->post('/{controller}/list', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->List($data) );
        return $response->withHeader('Content-Type', 'application/json');

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                USUARIOS AUTORIZADOS                     
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $app->get('/autorizado/comunidad/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Autorizado');
        
        $response->getBody()->write( HelperController::successResponse($frontController->context->ListComunidades() ,200));

        return $response;  

    });

    $app->get('/autorizado/{id:[0-9]+}/comunidad/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Autorizado');
        
        $response->getBody()->write( HelperController::successResponse($frontController->context->ListComunidades( $params['id'] ),200) );

        return $response;  

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                                                         FACTURACION
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //  Fichero de prefacturación en formato Excel
    $app->post('/facturacion/prefacturacion/{id:[0-9]+}', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Administrador');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $idAdministrador = $params['id'];

        $response->getBody()->write( $frontController->context->GetExcelPrefacturacion($idAdministrador, $data) );
        return $response;

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                                                  ALMACÉN DOCUMENTAL
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $app->post('/documental/requerimientos', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->ListadoRequerimientos($data) );
        return $response;

    }); 

    $app->get('/documental/requerimientos/cae/comunidades', function (Request $request, Response $response, array $params)
    {
    
        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');
        
        $response->getBody()->write( HelperController::successResponse($frontController->context->GetRequerimientosPendientesCAEGeneral()) );

        return $response;  
    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                                                         RGPD
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /** Recupera la documentación básica dada de alta en el sistema */
    $app->get('/rgpd/documentacionbasica', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('Requerimiento');

        $response->getBody()->write( HelperController::successResponse( $frontController->context->ListRequerimientoByIdTipo(1)  ));
        return $response->withHeader('Content-Type', 'application/json');  

    });

    /** Recupera las notas informativas asociadas a un administrador */
    $app->get('/rgpd/notasinformativas', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('Notasinformativas');

        $response->getBody()->write( $frontController->context->List($params)  );
        return $response->withHeader('Content-Type', 'application/json');  

    });

    /** Recupera los informes de valoración y seguimiento asignados a un administrador */
    $app->get('/rgpd/informevaloracionseguimiento', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('InformeValoracionSeguimiento');

        $response->getBody()->write( $frontController->context->List($params)  );
        return $response->withHeader('Content-Type', 'application/json');  

    });

    /** Asigna un documento a un requerimiento según el destino: Empresa, Comunidad, Empleado */
    $app->post('/rgpd/{destino}/{idcomunidad}/create', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimientoRGPD($destinoDocumento, $data) );
        return $response;

    }); 

    /** Asigna un contrato de confidencialidad a un empleado */
    $app->post('/rgpd/empleados/contratos/confidencialidad/create', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = 'rgpdempleado';

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimientoRGPD($destinoDocumento, $data) );
        return $response;

    }); 

    /** Recupera los documentos relativos al rgpd para una comunidad */
    $app->get('/rgpd/documentacion/{destino}/{idcomunidad}/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];
        $idComunidad = $params['idcomunidad'];

        $response->getBody()->write( $frontController->context->ListRequerimientoRGPD($destinoDocumento, $idComunidad) );
        return $response;

    }); 
    
    /** Recupera los tipos de documentos disponibles para descargar según el tipo de RGPD */
    $app->get('/rgpd/requerimiento/{idtipo}/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Requerimiento');
        $idTipo = $params['idtipo'];
        
        $response->getBody()->write( HelperController::successResponse( $frontController->context->ListRequerimientoByIdTipo($idTipo) ));

        return $response;  

    });

    /** Devuelve la información del contrato entre administrador y comunidad */
    $app->get('/rgpd/comunidad/{idcomunidad}/administrador/{idadministrador}/contratoadministracion', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $idComunidad = $params['idcomunidad'];
        $idAdministrador = $params['idadministrador'];
        
        $response->getBody()->write( $frontController->context->CheckContratoAdministradorComunidad($idComunidad, $idAdministrador) );

        return $response;  

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                                                 COMUNIDAD
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->post('/comunidad/{idcomunidad}/camaraseguridad', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $idComunidad = $params['idcomunidad'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $estado = $data['seleccionada'];

        $response->getBody()->write( $frontController->context->createRequerimientoCamara($idComunidad, $estado) );
        return $response;

    }); 

    //  Empresas asociadas a una comunidad
    $app->get('/comunidad/{id}/empresas', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->getEmpresasByComunidadId($id) );

        return $response;  

    });    

    //  Empleados de una comunidad
    $app->get('/comunidad/{id}/empleados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListEmpleadosByComunidadId($id) );

        return $response;  

    });    

    //  Servicios contratados por una comunidad
    $app->get('/comunidad/{id}/servicioscontratados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListServiciosContratadosByComunidadId($id) );

        return $response;  

    });

    //  Servicios contratados por una comunidad
    $app->get('/comunidad/servicioscontratados/list', function (Request $request, Response $response, array $params): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);
        $params = $request->getQueryParams();

        $response->getBody()->write( $frontController->context->ListServiciosContratadosComunidades($params) );
        // $response->getBody()->write( HelperController::successResponse($result) );

        return $response;  

    });

    //  Documentación de comunidad
    $app->get('/comunidad/{id}/documentacioncomunidad', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->getDocumentacionByComunidadId($id) );

        return $response;  

    });

    //  Documentación de comunidad
    $app->get('/comunidad/{id}/documentacioncertificado', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( HelperController::successResponse($frontController->context->getDocumentacionCertificadoDigitalByComunidadId($id), 200) );

        return $response;  

    });

    //  Solicitud de certificado digital de comunidad para un representante legal
    $app->get('/comunidad/{id}/solicitarcertificado/{idrepresentantelegal}', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $idComunidad = $params['id'];
        $idRepresentanteLegal = $params['idrepresentantelegal'];

        $resultado  = $frontController->context->SolicitarCertificadoIndividual($idComunidad, $idRepresentanteLegal);
        if($resultado === true)
        {
            $response->getBody()->write( HelperController::successResponse('ok', 200 ));
        }else{
            $response->getBody()->write( HelperController::errorResponse('error', $resultado,200));
        }

        return $response;  

    });

    $app->post('/comunidad/{idcomunidad}/empleado/{idempleado}/asignar', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $idComunidad = $params['idcomunidad'];
        $idEmpleado = $params['idempleado'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->AsignarComunidad($idEmpleado, $idComunidad ) );
        return $response;

    });  

    // Asigna una empresa a una comunidad
    $app->post('/comunidad/{idcomunidad}/empresa/{idempresa}/asignar', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $idComunidad = $params['idcomunidad'];
        $idEmpresa = $params['idempresa'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->asignarEmpresa($idComunidad, $idEmpresa) );
        return $response;

    }); 

    //  Elimina la relación entre empresa y comunidad
    $app->delete('/comunidad/{idcomunidad}/empresa/{idempresa}', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $idComunidad = $params['idcomunidad'];
        $idEmpresa = $params['idempresa'];

        $response->getBody()->write( $frontController->context->DeleteRelacionEmpresaComunidad($idComunidad, $idEmpresa) );
        return $response;

    }); 

    //  Documentación requerida para certificados digitales
    $app->get('/comunidad/{id}/documentacioncertificadodigital', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->getDocumentacionByComunidadId($id) );

        return $response;  

    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                                                 EMPLEADO
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->get('/administrador/{id}/comunidades', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListComunidadesByAdministradorId($id) );

        return $response;  

    });
    
    $app->get('/empleado/{id}/empresas', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal   
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListEmpresasByEmpleadoId($id) );

        return $response;  

    });

    $app->get('/empleado/{id}/documentacion', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListDocumentacionCAEEmpleado($id) );

        return $response;  

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                    REQUERIMIENTOS                 
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $app->post('/requerimiento/{destino}/create', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimiento($destinoDocumento, $data) );
        return $response;

    });   

    // Punto de entrada para eliminación de un requerimiento
    $app->delete('/requerimiento/{destino}/{id}', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        
        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $response->getBody()->write( $frontController->context->DeleteRequerimiento($params['destino'], $params['id']) );

        return $response->withHeader('Content-Type', 'application/json');  

    });
    
    // Asigna un documento a un requerimiento según el destino: Empresa, Comunidad, Empleado 
    $app->post('/requerimiento/{destino}/{idrequerimiento}', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimiento($destinoDocumento, $data) );
        return $response;

    }); 

    $app->get('/requerimiento/comunidad/{idcomunidad}/empresa/{idempresa}/infodescarga', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $idComunidad = $params['idcomunidad'];
        $idEmpresa = $params['idempresa'];

        $response->getBody()->write( $frontController->context->ListadoDocumentosComunidadDescargadosEmpresa($idEmpresa, $idComunidad) );

        return $response;  

    });

    $app->get('/requerimiento/{tiporequerimiento}/pendientes/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $tipoRequerimiento = $params['tiporequerimiento'];

        $response->getBody()->write( $frontController->context->GetRequerimientosPendientes($tipoRequerimiento) );

        return $response;  

    });

    //  Histórico de un requerimiento
    $app->get('/requerimiento/{entidad}/{idrelacionrequerimiento}/historico', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Historico');

        $idRelacionRequerimiento = $params['idrelacionrequerimiento'];
        $entidadRequerimiento = $params['entidad'];

        $response->getBody()->write( $frontController->context->GetHistoricoRequerimiento($idRelacionRequerimiento, $entidadRequerimiento) );

        return $response;  

    });    

    //  Cron de requerimientos para comprobar
    $app->get('/requerimiento/cron/checkcaducados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Requerimiento');

        $response->getBody()->write( $frontController->context->ComprobarRequerimientosCaducados() );

        return $response;  

    });     

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                           GESTOR DOCUMENTAL                 
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Refleja la descarga
    $app->post('/storage/descarga', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->ReflejarDescargaFichero( $data ) );
        return $response;

    }); 

    $app->get('/revision/pendientes', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $response->getBody()->write( $frontController->context->ListDocumentosPendientesVerificacion() );

        return $response;  

    });

    //  Cambia el estado de un requerimiento por parte de un técnico de revisión
    $app->post('/requerimiento/{entidad}/{idrequerimiento}/estado', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $idRequerimiento = $params['idrequerimiento'];
        $entidadDestino = $params['entidad'];
        
        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $estado = $data['idestado'];
        $fechaCaducidad = $data['fechacaducidad'];
        $observaciones = $data['observaciones'];
        
        $response->getBody()->write( $frontController->context->cambiarEstadoRequerimiento( $idRequerimiento, $entidadDestino, $estado, $fechaCaducidad, $observaciones ) );
        return $response;

    }); 

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                EMPRESA                     
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //  Empleados de una empresa
    $app->get('/empresa/{id}/empleados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $idEmpresa = $params['id'];

        $response->getBody()->write( $frontController->context->ListEmpleadosByEmpresaId($idEmpresa) );

        return $response;  

    });

    // Comprobación documento operatoria entre empresa externa y comunidad
    $app->get('/empresa/{idempresa}/comunidad/{idcomunidad}/operatoria', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $idEmpresa = $params['idempresa'];
        $idComunidad = $params['idcomunidad'];

        $response->getBody()->write( $frontController->context->CheckOperatoriaEmpresaComunidad($idEmpresa, $idComunidad) );

        return $response;  

    });

    // Recupera los empleados de una empresa para una comunidad 
    $app->get('/empresa/{idempresa}/comunidad/{idcomunidad}/empleados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $idEmpresa = $params['idempresa'];
        $idComunidad = $params['idcomunidad'];

        $response->getBody()->write( $frontController->context->ListEmpleadosByComunidadAndEmpresa($idComunidad, $idEmpresa) );

        return $response;  

    });

    // Documentación de una empresa 
    $app->get('/empresa/{id}/documentacion', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Requerimiento');

        $idEmpresa = $params['id'];

        $response->getBody()->write( $frontController->context->GetRequerimientosEmpresa($idEmpresa) );

        return $response;  

    });

    // Devuelve el listado de comunidades asociadas a una empresa
    $app->get('/empresa/{id}/comunidades', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empresa');

        $idEmpresa = $params['id'];

        $response->getBody()->write( $frontController->context->GetComunidades($idEmpresa) );

        return $response;  

    });

    $app->get('/mensaje/{id}/resend', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Mensaje');

        $idMensaje = $params['id'];

        $response->getBody()->write( $frontController->context->ResendMessage($idMensaje) );

        return $response;  

    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                CERTIFICADOS DIGITALES                     
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /** 
     * Listado de representantes legales relacionados a un administrador 
     * @method(get)
    */
    $app->get('/administrador/{id}/representantelegal/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Representantelegal');
        $administradorId = $params['id'];
        $response->getBody()->write( $frontController->context->ListByAdministradorId( $administradorId ) );

        return $response;  

    });

    /**
     * Envío de sms certificado
     * @method(post)
     */
    $app->post('/certificadodigital/administrador/enviosms', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $phoneNumber = $data['phonenumber'];
        $sender = $data['sender'];
        $message = $data['message'];
        
        $response->getBody()->write( HelperController::successResponse($frontController->context->SendSMS( $phoneNumber, $sender, $message ) ));
        return $response;

    }); 

    /**
     * Envío de sms de contrato con certificado y acuse de recibo
     * @method(post)
     */
    $app->post('/certificadodigital/administrador/enviosmscontrato', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $phoneNumber = $data['phonenumber'];
        $sender = $data['sender'];
        $message = $data['message'];
        $ficheroContrato = $data['filebase64'];
        $ficheroNombre = $data['filename'];

        $response->getBody()->write( HelperController::successResponse($frontController->context->SendSMSContrato( $phoneNumber, $sender, $message, $ficheroContrato, $ficheroNombre )) );
        return $response;

    }); 

    /**
     * Envío de e-mail certificado desde el panel del administrador
     * @method(post)
     */
    $app->post('/certificadodigital/administrador/envioemailcertificado', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $destinatario = $data['destinatario'];
        $administradorId = $data['senderid'];
        $mensaje = $data['mensaje'];
        $asunto = $data['subject'];
        $destinatarioNombre = $data['comunidad'];
        $attachment = isset($data['fichero']) ? $data['fichero']: null;

        $response->getBody()->write( 
            $frontController->context->SendEmailCertificadoAdministrador( $asunto, $destinatario, $administradorId, $mensaje, $destinatarioNombre, $attachment )
        );        
        return $response;

    });     

    /**
     * DEPRECATED: Solicitud de certificado digital para comunidad por parte de un administrador de fincas
     */
    $app->post('/certificadodigital/comunidad/createrequest', function(Request $request, Response $response, array $params ): Response
    {

        $response->getBody()->write( HelperController::errorResponse('deprecated','error', 200 ) );
        return $response;
        
        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        //  Usuario autenticado en el sistema
        $administradorId = $data['userId'];
        //  ID's de solicitudes separados por comas
        $solicitudesId = explode(',', $data['solicitudIds']);
        //  ID's de comunidades separados por comas
        // $comunityIds = explode(',', $data['comunityIds'] );
        //  Imagen frontal DNI B64
        $documentoFrontalBase64 = $data['documentoFrontalBase64'];
        //  Imagen frontal nombre fichero
        $documentoFrontalNombre = $data['documentoFrontalNombre'];
        //  Imagen trasera DNI B64
        $documentoTraseroBase64 = $data['documentoTraseroBase64'];
        //  Imagen trasera nombre fichero
        $documentoTraseroNombre = $data['documentoTraseroNombre'];

        //  Creamos la solicitud en el sistema
        $resultado = $frontController->context->CreateRequestCertificates($administradorId, $solicitudesId, $documentoFrontalBase64, $documentoFrontalNombre, $documentoTraseroBase64, $documentoTraseroNombre);

        if($resultado === true)
        {
            $response->getBody()->write( HelperController::successResponse('ok',200) );
        }else{
            $response->getBody()->write( HelperController::errorResponse($resultado,'error', 200 ) );
        }

        return $response;

    });   

    /**
     * Emails certificados que ha enviado un administrador
     */
    $app->get('/certificadodigital/administrador/emailcertificado/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Mensaje');
       
        $response->getBody()->write( 
            HelperController::successResponse
            (
                $frontController->context->GetEmailsCertificadosAdministrador()
            ) 
        );
        return $response;

    });   
    
    /**
     * Listado de comunidades que están pendientes de aprobar el certificado
     */
    $app->get('/certificadodigital/comunidad/pendientesvalidacion/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
       
        $response->getBody()->write( 
            HelperController::successResponse
            (
                $frontController->context->ComunidadesPendienteValidacion()
            ) 
        );
        return $response;

    });   

    /**
     * Listado de comunidades que están pendientes de solicitar el certificado por parte del administrador
     */
    $app->get('/certificadodigital/comunidad/pendientessolicitud/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
       
        $response->getBody()->write( 
            HelperController::successResponse
            (
                $frontController->context->ComunidadesPendientesSolicitud()
            ) 
        );
        return $response;

    });   

    /**
     * Listado de comunidades que están pendientes de solicitar el certificado por parte del administrador
     */
    $app->get('/certificadodigital/administrador/solicitudes/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
        $resultado = $frontController->context->ComunidadesPendientesSolicitud(true, true);

        if(isset($resultado['comunidad']))
        {
            $response->getBody()->write( 
                HelperController::successResponse
                (
                    $resultado
                ) 
            );
        }else{
            $response->getBody()->write(
                HelperController::errorResponse($resultado, 'Ud. no tiene acceso a este listado',403)
            );
        }

        return $response;

    }); 

    /**
     * Listado de comunidades que están pendientes de solicitar el certificado por parte del administrador
     */
    $app->get('/certificadodigital/comunidad/emitidos/list', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
       
        $response->getBody()->write( 
            HelperController::successResponse
            (
                $frontController->context->ComunidadesCertificadoSolicitado()
            ) 
        );
        return $response;

    });   

    /**
     * Valida la documentación aportada por una comunidad para el certificado digital
     */
    $app->get('/certificadodigital/comunidad/{idcomunidad:[0-9]+}/validate/documentacion', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
        $idComunidad = $params['idcomunidad'];     

        $response->getBody()->write( 
            HelperController::successResponse
            (
                $frontController->context->ValidarDocumentacionAprobadaComunidad($idComunidad)
            ) 
        );
        return $response;

    });  

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                VARIOS                     
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //  Solicitud de scratchcard
    $app->get('/test/certificadodigital/scratchcard', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
       
        $respuestaAccion = $frontController->context->GetFirstUnusedScratchCard();
        if($respuestaAccion !== false)
        {
            $response->getBody()->write( $respuestaAccion );
        }else{
            $response->getBody()->write( 
                HelperController::errorResponse($respuestaAccion,'Error en scratchcard','200') 
            );
    
        }
        return $response;

    }); 

    /** Solicitud certificado  */
    $app->get('/test/certificadodigital/requestcertificate', function(Request $request, Response $response, array $params ): Response
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
       
        $respuestaAccion = $frontController->context->RequestCertificate(1);
        if($respuestaAccion === true)
        {
            $response->getBody()->write( HelperController::successResponse($respuestaAccion, 200 ) );
        }else{
            $response->getBody()->write( HelperController::errorResponse('error', $respuestaAccion['message'], $respuestaAccion['http_code']) );   
        }

        return $response;

    });     

    $app->get('/test/notainformativa/{id:[0-9]+}', function (Request $request, Response $response, array $params) {
        
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();

        $frontController->Init('NotasInformativas');
        $idNota = $params['id'];
        $frontController->context->EnviarEmailNotaInformativa($idNota, true);
        //$frontController->WriteToLog('pruebas','routes', 'prueba ok');        
        $response->getBody()->write('test terminado');
        return $response;
    });

    //  EMail TEST
    $app->get('/emailtest', function (Request $request, Response $response) {

        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        // $frontController->SendTestEmail();
        $response->getBody()->write($frontController->SendTestEmail());
        return $response;   
    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////                                                                CRONS                     
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Cron de normalización de informes desde Mensatek
     */
    $app->get('/cron/emailscertificados/normalizacion', function (Request $request, Response $response)
    {
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        $frontController->Init('Cron');
        //  Lanzamos la normalización para descargar los ficheros
        $respuesta = $frontController->context->NormalizarInformesMensatek();
        $response->getBody()->write($respuesta);
        return $response;               
    });

    /**
     * Cron documentación CAE caducada de empresas y empleados de empresa
    */
    $app->get('/cron/empresa/documentoscaducados', function (Request $request, Response $response) {

        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        $frontController->Init('Cron');
        $frontController->context->ControlCaducidadDocumentosCAEEmpresas();
        $response->getBody()->write(HelperController::successResponse('ejecutado',200));
        return $response;
    });

    /**
     * Cron que reenvía e-mails de registro a las empresas que nunca han accedido
    */
    $app->get('/cron/empresa/pendienteacceso', function (Request $request, Response $response) {

        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        $frontController->Init('Cron');
        $result = $frontController->context->EnvioRecordatorioAccesoEmpresas();
        $response->getBody()->write(HelperController::successResponse( $result, 200) );
        return $response;
    });


    //  Emails certificados
    $app->post('/emailcertificado', function(Request $request, Response $response, array $params ): Response
    {
        
        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';
        
        $frontController = new $frontControllerName();

        $frontController->WriteToLog('emailcertificado_informe','Routes -> /emailcertificado', 'Accedido dese fuera');

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $frontController->WriteToLog('emailcertificado_informe', 'Routes -> emailcertificado (json desde Mensatek)', $body);

        if(isset($data['Resultado']))
        {
            $resultado = $data['Resultado'];
            $idMensaje = $data['idMensaje'];
            $emailDestinatario = $data['Destinatario'];
            // Si el resultado es 14 entonces quiere decir que ya está disponible el certificado
            /*
                10: Apertura/Visualización
                11: Entregado al destinatario
                12: Lectura/Acceso al contenido
                13: Entregado, acceso y descarga
                14: El destinatario ha respondido
                50: E-mail no válido
            */
            switch($resultado){
                case '10':
                case '11':
                case '12':
                case '13':
                case '14':
                    $frontController->Init('Mensaje');
                    $ficheroCertificacion = $frontController->getPDFEmailCertificado($idMensaje);
                    $frontController->context->saveFileEmailCertificado($idMensaje, $ficheroCertificacion);
                    break;
                case '50':
                    //  Incluimos el e-mail en una tabla de e-mails no válidos para evitar posteriores envíos
                    $frontController->IncludeEmailIntoBlackList($emailDestinatario);
                    $frontController->WriteToLog('emailcertificado_informe','Routes -> /emailcertificado', 'Resultado del envío por error parte de Mensatek [Destinatario no válido]: ' . $resultado); 
                    break;
                default:
                    $frontController->WriteToLog('emailcertificado_informe','Routes -> /emailcertificado', 'Resultado del envío con error por parte de Mensatek: ' . $resultado); 
                    break;
            }

        }else{
            $frontController->WriteToLog('emailcertificado_informe','Routes -> /emailcertificado', 'No recibe contenido Resultado' . PHP_EOL . $body);
        }

        $response->getBody()->write( HelperController::successResponse('ok', 200) );
        return $response;

    }); 

    //  TEST
    $app->get('/hsdebug/test', function (Request $request, Response $response) {
        $frontControllerName = ConfigTrait::getHSNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Certificadodigital');
        // $frontController->WriteToLog('pruebas','routes', 'prueba ok');        
        $cert = $frontController->context->RequestCertificateToUanataca(143);
        // $cert = $frontController->context->Test();
        $response->getBody()->write( $cert );
        return $response;
    });

};
