<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/comunidad' => [[['_route' => 'add_comunidad', '_controller' => 'App\\Controller\\ComunidadController::insert'], null, ['POST' => 0], null, false, false, null]],
        '/comunidades' => [[['_route' => 'get_all_comunidades', '_controller' => 'App\\Controller\\ComunidadController::getAll'], null, ['GET' => 0], null, false, false, null]],
        '/getview' => [[['_route' => 'getview_post', '_controller' => 'App\\Controller\\RenderViewController::renderView'], null, ['POST' => 0], null, false, false, null]],
        '/checklogin' => [[['_route' => 'checklogin', '_controller' => 'App\\Controller\\SecurityController::checkLogin'], null, null, null, false, false, null]],
        '/usuarios' => [[['_route' => 'get_all_usuarios', '_controller' => 'App\\Controller\\UsuarioController::getAll'], null, ['GET' => 0], null, false, false, null]],
        '/api/v1' => [[['_route' => 'index', '_controller' => 'App\\Controller\\DefaultController::index'], null, null, null, true, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/comunidad/([^/]++)(?'
                    .'|(*:191)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        191 => [
            [['_route' => 'update_comunidad', '_controller' => 'App\\Controller\\ComunidadController::update'], ['idcomunidad'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'delete_comunidad', '_controller' => 'App\\Controller\\ComunidadController::delete'], ['idcomunidad'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'get_one_comunidad', '_controller' => 'App\\Controller\\ComunidadController::get'], ['idcomunidad'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
