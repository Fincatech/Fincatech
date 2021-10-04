<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use HappySoftware\Controller\Traits\ConfigTrait;

use Fincatech\Controller;
use Fincatech\Controller\FrontController;
use HappySoftware\Controller\HelperController;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });    

    /**
     * Recupera el schema para una entidad y sus posibles entidades relacionadas
     */
    $app->get('/logout', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

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
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        //  Instanciamos el controller del login
        $frontController->Init( 'login', $data );

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->checkLogin( $data ) );
        
        return $response;

    });  

    //  Renderizado de vistas
    $app->post('/getview', function(Request $request, Response $response, array $params ): Response
    {

        $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        
        $response->getBody()->write( $frontController->renderView( $data ) );
        return $response;

    });    

    $app->get('/userinfo', function(Request $request, Response $response, array $params )
    {
        // Instanciamos el controller principal
        //$frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

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
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

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
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

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

    /** 
     * Punto de entrada para delete. Se utiliza para eliminar una entidad y sus relaciones
    */
    $app->delete('/{controller}/{id}', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
        
        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->Delete($params['id']) );

        return $response;  

    });

    /**
     * Recupera el schema para una entidad y sus posibles entidades relacionadas
     */
    $app->get('/{controller}/schema', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->GetSchemaAction() );

        return $response;  

    });

    //  Endpoint para devolver tablas ya construidas
    $app->post('/{controller}/list', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->List($data) );
        return $response;

    });

    /** Recupera la documentación para una comunidad */


    /** Recupera la documentación básica dada de alta en el sistema */
    $app->get('/rgpd/documentacionbasica', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('Requerimiento');

        $response->getBody()->write( $frontController->context->ListRequerimientoByIdTipo(1)  );
        return $response;  

    });

    /** Recupera las notas informativas asociadas a un administrador */
    $app->get('/rgpd/notasinformativas', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('Notasinformativas');

        $response->getBody()->write( $frontController->context->List($params)  );
        return $response;  

    });

    /** Recupera los informes de valoración y seguimiento asignados a un administrador */
    $app->get('/rgpd/informevaloracionseguimiento', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init('InformeValoracionSeguimiento');

        $response->getBody()->write( $frontController->context->List($params)  );
        return $response;  

    });

    ///////////////////////////////////////////////////
    ////                   CAE, PRL                 ///
    ///////////////////////////////////////////////////

    $app->post('/requerimiento/{destino}/create', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimiento($destinoDocumento, $data) );
        return $response;

    });   

    /** Asigna un documento a un requerimiento según el destino: Empresa, Comunidad, Empleado */
    $app->post('/requerimiento/{destino}/{idrequerimiento}', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Documental');

        $destinoDocumento = $params['destino'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->uploadRequerimiento($destinoDocumento, $data) );
        return $response;

    }); 

    /** Punto de entrada para get. Se utiliza para listar todo
     *
    */
    $app->get('/{controller}/list', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);
        $queryString = $request->getQueryParams();
        
        $response->getBody()->write( $frontController->List($queryString) );

        return $response;  

    });

    //  Empresas asociadas a una comunidad
    $app->get('/comunidad/{id}/empresas', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

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
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListServiciosContratadosByComunidadId($id) );

        return $response;  

    });    

    //  Servicios contratados por una comunidad
    $app->get('/comunidad/{id}/servicioscontratados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListServiciosContratadosByComunidadId($id) );

        return $response;  

    });

    //  Servicios contratados por una comunidad
    $app->get('/comunidad/{id}/documentacioncomunidad', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->getDocumentacionByComunidadId($id) );

        return $response;  

    });

    /** Asigna una empresa a una comunidad */
    $app->post('/comunidad/{idcomunidad}/empresa/{idempresa}/asignar', function(Request $request, Response $response, array $params ): Response
    {

        // $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $idComunidad = $params['idcomunidad'];
        $idEmpresa = $params['idempresa'];

        $body= file_get_contents("php://input"); 
        $data = json_decode($body, true);

        $response->getBody()->write( $frontController->context->asignarEmpresa($idComunidad, $idEmpresa) );
        return $response;

    });  

    // NOTE: ¿? Documentación requerida para una comunidad
    // $app->get('/comunidad/{idcomunidad}/documentacion', function (Request $request, Response $response, array $params)
    // {
    //     //$frontController = new Fincatech\Controller\FrontController();
    //     $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
    //     $frontController = new $frontControllerName();
        
    //     $frontController->Init('Requerimiento');

    //     $id = $params['idcomunidad'];

    //     $response->getBody()->write( $frontController->context->GetDocumentacionComunidad($id)  );
    //     return $response;  

    // });

    $app->get('/administrador/{id}/comunidades', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Comunidad');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListComunidadesByAdministradorId($id) );

        return $response;  

    });

    $app->get('/empleado/{id}/empresas', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $id = $params['id'];

        $response->getBody()->write( $frontController->context->ListEmpresasByEmpleadoId($id) );

        return $response;  

    });

    $app->get('/empresa/{id}/empleados', function (Request $request, Response $response, array $params)
    {

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init('Empleado');

        $idEmpresa = $params['id'];

        $response->getBody()->write( $frontController->context->ListEmpleadosByEmpresaId($idEmpresa) );

        return $response;  

    });

    //  Punto de entrada para get con ID
    $app->get('/{controller}/{id:[0-9]+}', function (Request $request, Response $response, array $params)
    {
        //$frontController = new Fincatech\Controller\FrontController();
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';
        $frontController = new $frontControllerName();
        
        $frontController->Init($params['controller']);
        
        $response->getBody()->write( $frontController->Get($params['id']) );
        return $response;  

    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    // $app->group('/users', function (Group $group) {
    //     $group->get('', ListUsersAction::class);
    //     $group->get('/{id}', ViewUserAction::class);
    // });
};
