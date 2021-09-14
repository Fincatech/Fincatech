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

    //  EVALUAR QUE LOS LISTADOS SE HAGAN POR POST Y/O POR GET TAMBIÉN
    //  CUANDO ES POST ES PORQUE TIENE PAGINACIÓN Y DEMÁS FILTROS
    $app->post('/{controller}/create', function(Request $request, Response $response, array $params ): Response
    {

        $data = $request->getParsedBody();

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

        $data = $request->getParsedBody();

        // Instanciamos el controller principal
        $frontControllerName = ConfigTrait::getNamespaceName() . 'Controller\\FrontController';

        $frontController = new $frontControllerName();
        $frontController->Init($params['controller']);

        $response->getBody()->write( $frontController->GetTable($data) );
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
