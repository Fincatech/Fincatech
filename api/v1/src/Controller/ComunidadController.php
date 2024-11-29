<?php
namespace Fincatech\Controller;

use Fincatech\Model\ComunidadModel;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\AutorizadoController;
use HappySoftware\Controller\DocumentalController;
use HappySoftware\Controller\HistoricoController;
use HappySoftware\Controller\EmpresaController;
use HappySoftware\Controller\MensajeController;
use HappySoftware\Controller\UsuarioController;
use HappySoftware\Controller\ServiciosController;

use HappySoftware\Controller\Traits\SecurityTrait;

class ComunidadController extends FrontController{

    use SecurityTrait;

    public $UsuarioController, $EmpresaController, $MensajeController, $AutorizadoController, $ServiciosController, $DocumentalController, $HistoricoController;
    public $ComunidadModel;
    private $comunidadModel;

    public function __construct($params = null)
    {

        $this->InitModel('Comunidad', $params);
        $this->ComunidadModel = new ComunidadModel($params);
        //  Instanciamos el controller de tipos de servicio
        $this->InitController('Servicios');
        $this->InitController('Usuario');
        $this->InitController('Autorizado');
    }

    /**
     * Crea una nueva comunidad en el sistema
     * REFACTORIZAR: Refactorizar para utilizar la nueva arquitectura basandose en objetos
     */
    public function Create($entidadPrincipal, $datos)
    {

        //  Comprobamos si es una creación desde el proceso de importación automático para el master
        $procesoImportacion = isset($datos['importacion']);
        unset($datos['importacion']);

        //  Servicios contratados en el alta
        if(isset($datos['servicioCAE']))
        {
            $servicioCaeContratado = ($datos['servicioCAE'] == '0' ? 0 : 1);
            unset($datos['servicioCAE']);
        }

        if(isset($datos['servicioRGPD']))
        {
            $servicioRGPDContratado = ($datos['servicioRGPD'] == '0' ? 0 : 1);
            unset($datos['servicioRGPD']);
        }

        if(isset($datos['servicioDOCCAE']))
        {
            $servicioDOCCAEContratado = ($datos['servicioDOCCAE'] == '0' ? 0 : 1);
            unset($datos['servicioDOCCAE']);
        }

        //  Si no está informado, cogemos el usuario autenticado en el sistema
        if(!isset($datos['usuarioId']))
            $datos['usuarioId'] = $this->getLoggedUserId();

        //  Comprobamos si es un usuario autorizado
            $idUsuario = $datos['usuarioId'];
            //$usuarioAutorizado = $this->UsuarioAutorizado( $idUsuario );
            $usuarioAutorizado = $this->UsuarioController->IsAuthorizedUserByAdmin($idUsuario);

        //  Si es un autorizado, asignamos la comunidad al administrador principal
            if($usuarioAutorizado !== false)
            {
                $datos['usuarioId'] = $usuarioAutorizado;
            }

        
        //  Si es el usuario sudo el que ha dado de alta la comunidad
        //  forzamos que se guarde en estado 'A' (Activada) para que lo apruebe el admin del sistema
        if($this->getLoggedUserRole() == 'ROLE_SUDO')
        {
            $datos['estado'] = 'A';
        }else{
            $datos['estado'] = 'P';
        }
        
        //  Creamos la comunidad y obtenemos el id del registro para procesar los posibles servicios contratados
        //  así como los precios 
        $datosServiciosContratados = null;
        if(isset($datos['comunidadservicioscontratados']))
        {
            $datosServiciosContratados = $datos['comunidadservicioscontratados'];           
            //  Quitamos los datos para evitar que de error al guardar
            unset($datos['comunidadservicioscontratados']);
        }
        
        //  Comprobamos si existe el IBAN para poder parsearlo
        if(isset($datos['ibancomunidad']))
            $datos['ibancomunidad'] = HelperController::NormalizeIBAN($datos['ibancomunidad']);

        //  Llamamos al método de crear
        // $datos['codigo'] = intval($datos['codigo']);
        $idComunidad = $this->ComunidadModel->Create($entidadPrincipal, $datos);

        if(isset($servicioCaeContratado))
        {
            if(!$this->ComunidadModel->ExisteServicioComunidad($idComunidad['id'], 1))
            {
                $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], 1, '0', '0', $servicioCaeContratado);
            }
        }

        if(isset($servicioRGPDContratado))
        {
            if(!$this->ComunidadModel->ExisteServicioComunidad($idComunidad['id'], 2))
            {            
                $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], 2, '0', '0', $servicioRGPDContratado);
            }
        }

        if(isset($servicioDOCCAEContratado))
        {
            if(!$this->ComunidadModel->ExisteServicioComunidad($idComunidad['id'], 3))
            {            
                $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], 3, '0', '0', $servicioDOCCAEContratado);
            }
        }

        // Instalaciones y Certificados digitales
        if(isset($servicioCaeContratado) && isset($servicioRGPDContratado))
        {
            $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], 4, '0', '0', '0');
            $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], 5, '0', '0', '0');
        }

        //  Insertamos los servicios contratados por la comunidad
        if(!is_null($datosServiciosContratados))
        {
            for($x = 0; $x < count($datosServiciosContratados); $x++)
            {
                $idServicio = $datosServiciosContratados[$x]['idservicio'];
                $precio = $datosServiciosContratados[$x]['precio'];
                $precioComunidad = str_replace(',','.',$datosServiciosContratados[$x]['preciocomunidad']);
                $contratado = $datosServiciosContratados[$x]['contratado'];
                $mesfacturacion = $datosServiciosContratados[$x]['mesfacturacion'];
                if(!$this->ComunidadModel->ExisteServicioComunidad($idComunidad['id'], $idServicio))
                {   
                    $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], $idServicio, $precio, $precioComunidad, $contratado, $mesfacturacion);
                }
            }
        }

        //  Asignamos la comunidad a un usuario autorizado
        if($usuarioAutorizado !== false)
        {
            $this->InitController('Autorizado');
            $this->AutorizadoController->GuardarComunidadAsignada($this->getLoggedUserId(), $idComunidad['id']);
        }

        //  Solo enviamos el e-mail de alta cuando no proviene de un proceso de importación
        if(!$procesoImportacion)
        {
            //  Si no ha sido el sudo el que ha dado de alta la comunidad, envíamos e-mail al administrador de la plataforma
            $this->EnviarEmailAltaComunidad($datos['codigo'], $datos['nombre']);
        }


        return $idComunidad;

    }

    /**
     * Actualiza la entidad de la comunidad con los datos proporcionados
     */
    public function Update($entidadPrincipal, $datos, $usuarioId)
    {

        //  Comprobamos si existe el IBAN para poder parsearlo
        if(isset($datos['ibancomunidad']))
            $datos['ibancomunidad'] = HelperController::NormalizeIBAN($datos['ibancomunidad']);
        

        $comunidadId = $datos['id'];

        //  Creamos la comunidad y obtenemos el id del registro para procesar los posibles servicios contratados
        //  así como los precios 
        if(isset($datos['comunidadservicioscontratados']))
        {
            $datosServiciosContratados = $datos['comunidadservicioscontratados'];

            //  Quitamos los datos para evitar que de error al guardar
                unset($datos['comunidadservicioscontratados']);

            //  Actualizamos los servicios contratados por la comunidad
                for($x = 0; $x < count($datosServiciosContratados); $x++)
                {
                    $idServicioComunidad = $datosServiciosContratados[$x]['idserviciocomunidad'];
                    $precio = $datosServiciosContratados[$x]['precio'];
                    $precioComunidad = $datosServiciosContratados[$x]['preciocomunidad'];
                    $contratado = $datosServiciosContratados[$x]['contratado'];
                    $mesFacturacion = $datosServiciosContratados[$x]['servicio-mesfacturacion'];
                    $this->ComunidadModel->UpdateServicioContratado($datos['id'], $datosServiciosContratados[$x]['idservicio'], $idServicioComunidad, $precio, $precioComunidad, $contratado, $mesFacturacion);
                }
        }

        return $this->ComunidadModel->Update($entidadPrincipal, $datos, $comunidadId); 

    }

    public function getSchemaEntity()
    {
        return $this->ComunidadModel->getSchema();
    }

    public function Delete($id)
    {
        //  Recuperamos la información de la comunidad
        $comunidad = $this->Get($id);
        $codigoComunidad = $comunidad['Comunidad'][0]['codigo'];
        $nombreComunidad = $comunidad['Comunidad'][0]['nombre'];

        $resultado = $this->ComunidadModel->Delete($id);

        $this->EnviarEmailBajaComunidad($codigoComunidad, $nombreComunidad);

        return $resultado;
    }

    /** Recupera una comunidad por su id */
    public function Get($id, $_extraInfo = true)
    {

        //  Comprobamos que la comunidad pertenezca al usuario o al autorizado
        $tieneAcceso = false;
        $autorizado = $this->UsuarioController->Get($this->getLoggedUserId());
        $comunidad = $this->ComunidadModel->Get($id);
        $idUsuarioAcceso = 0;

        //  Si el usuario existe
        if(!is_null($autorizado))
        {
            $idUsuarioAcceso = ( $autorizado['Usuario'][0]['idadministrador'] != '' ? $autorizado['Usuario'][0]['idadministrador'] : $autorizado['Usuario'][0]['id']);
        }

        //  Si el usuario es un autorizado o pertenece a los roles: Sudo, Contratista ó Técnico RAO, se le concede el acceso
        if($idUsuarioAcceso == $comunidad['Comunidad'][0]['usuarioId'] || $this->isFacturacion() || $this->isSudo() || $this->isContratista() || $this->isTecnicoRao())
        {
            $tieneAcceso = true;
        }
        
        if(!$tieneAcceso){
            $status = [];
            $status['error'] = '403';
            return $status;
        }

        if($_extraInfo === true)
        {
                $comunidad['Comunidad'][0]['comunidadservicioscontratados'] = $this->ListadoServiciosContratados($comunidad['Comunidad'][0]['id']);
            //  Recuperamos los documentos asociados a una comunidad
                $comunidad['Comunidad'][0]['documentacioncomunidad'] = $this->GetDocumentacionComunidad($id);
        }
        return $comunidad;
    }

    public function List($params = null, $useLoggedUserId = true)
    {

        $data = [];
        $administradorId = null;
        $_serviciosContratados = true;
        $_documentacionAsociada = false;
        $_gradoCumplimiento = true;

        //  Parámetros de paginación
        $limitStart = (isset($params['start']) ? $params['start'] : null);
        $limitLength = (isset($params['length']) ? $params['length'] : null);        
        $orderType = 'asc';

        //  Parámetros de búsqueda
        if(isset( $params['search']['value'] ))
        {
            $params['searchfields'] =[];
            $params['searchvalue'] = $params['search']['value'];
            $params['searchfields'][0]['field'] = "nombre";
            $params['searchfields'][0]['operator'] = "%";
            
            if(!$this->isSudo())
            {
                $params['searchfields'][1]['field'] = "codigo";
                $params['searchfields'][1]['condition'] = "or";
                $params['searchfields'][1]['operator'] = "%";     
            }

            //  23/07/2024 - ORR
            //  Se acota por cif sólo para el usuario sudo, hablado con Cristóbal por WhatsApp
            if($this->isSudo()) 
            {
                $params['searchfields'][1]['field'] = "cif";
                $params['searchfields'][1]['condition'] = "or";
                $params['searchfields'][1]['operator'] = "%";                                                  
            }
        }

        //  Parámetros de orden por columna
        //  Establecemos orden si es que lo hay
        // if(isset($params['order']))
        // {
        //     $iColumna = $params['order'][0]['column'];
        //     if(trim($params['columns'][$iColumna]['name']) !== '')
        //     {
        //         $params['orderby'] = $params['columns'][$iColumna]['name'];
        //         $params['order'] = $params['order'][0]['dir'];   
        //         $orderType = $params['order'][0]['dir'];
        //     }
        // }        

        //  Usuarios con rol Administrador de fincas
        if($this->isAdminFincas())
        {

            unset($params['start']);
            unset($params['length']);

            $_serviciosContratados = true;
            $_documentacionAsociada = true;
            $_gradoCumplimiento = true;

            //  Listado de comunidades para administradores de fincas o autorizados
            $params['filterfield'] = 'estado';
            $params['filteroperator'] = ' in ';
            $params['filtervalue'] = " ('A','P') ";

            $userId = $this->getLoggedUserId();
            
            //  Comunidades por usuario autorizado
            $administradorId = $this->UsuarioController->IsAuthorizedUserByAdmin( $this->getLoggedUserId() );

            //  Si es un usuario autorizado
            if($administradorId !== false)
            {

                //  Recuperamos las comunidades que tiene asignado un usuario
                $comunidadesAutorizado = $this->AutorizadoController->ComunidadesAsignadas($userId);
                if(count($comunidadesAutorizado))
                {

                    $listadoComunidades = [];
                    for($i =  0; $i<count($comunidadesAutorizado); $i++)
                    {
                        $idComunidad = $comunidadesAutorizado[$i]['idcomunidad'];
                        $listadoComunidades[] =  $idComunidad;
                    }

                    $params['filterfield'] = "usuarioId = $administradorId";
                    $params['filteroperator'] = ' and ';
                    $params['filtervalue'] = " id in(" . implode(',',$listadoComunidades) . ")";

                    $data = $this->ComunidadModel->List($params, false);
                    //$data['Comunidad'] = $listadoComunidades;

                }else{
                    $data = $this->ComunidadModel->List($params, true);
                //   $daa['Comunidad'] = [];
                }

                //  Recuperamos el listado de comunidades
                // $data = $this->ComunidadModel->List($params, false);

            }else{
                $data = $this->ComunidadModel->List($params, true);
            }
            $data = $this->ComunidadModel->filterResults($data, 'Comunidad', 'estado', 'A');

        }

        //  Usuarios SUDO
        if($this->isSudo() || $this->isFacturacion())
        {

            $useLoggedUserId = false;

            //  Comprobamos si se han de recuperar los servicios contratados por comunidad
            $_serviciosContratados = isset($params['servicios']) ? true : false;
            $_gradoCumplimiento = false;

            /** Para el listado de comunidades pendientes de activar */
            if(isset($_GET['status']))
            {
                $estado = [];
                $estado['field'] = 'estado';
                $estado['operator'] = '=';
                $params['searchfields'][] = $estado;
                $params['searchvalue'] = "'P'";
            }

            //  Establecemos orden si es que lo hay
            // if(isset($params['order']))
            // {
            //     $iColumna = $params['order'][0]['column'];
            //     $params['orderby'] = $params['columns'][$iColumna]['name'];
            //     $params['order'] = $params['order'][0]['dir'];
            // }

            // if(!is_null($limitStart) && !is_null($limitLength) )
            // {
            //     $params['start'] = $limitStart;
            //     $params['length'] = $limitLength;
            // }

            if(isset($params['administradorId']))
            {
                //$data['Comunidad'] = $this->ComunidadModel->ListComunidadesByAdministradorId($params['administradorId'], $params['fechaDesde'], $params['fechaHasta']);
                $data = $this->ComunidadModel->ListServiciosContratadosComunidades($params);
            }else{
                //  TODO: Hay que meterlo en una vista y definirlo en el $params['view']
                //          Para poder acotar por nombre de administrador
                $data = $this->ComunidadModel->List($params, $useLoggedUserId);
                $limitStart = null;
                $limitLength = null;
            }

        }

        //  Recuperamos los servicios asociados a las comunidades
        // if(@count($data['Comunidad']) > 0 && !$this->isSudo())
        if(@count($data['Comunidad']) > 0)
        {
                $data = $this->ordenarComunidades($data, $limitStart, $limitLength, $orderType);

            //  Recuperamos todos los servicios del sistema
                if($_serviciosContratados)
                {
                    $servicios = $this->ServiciosController->List(null, false);
                }

            //  Procesamos solo si hay alguna de las variables de control true
                if($_documentacionAsociada || $_serviciosContratados || $_gradoCumplimiento)
                {
                    for($x=0; $x < count($data['Comunidad']); $x++)
                    {
                        if(!empty($data['Comunidad'][$x]))
                        {
    
                            $comunidadId = $data['Comunidad'][$x]['id'];
    
                            //  Recuperamos el listado de servicios contratados por una comunidad
                            if($_serviciosContratados)
                            {
                                $data['Comunidad'][$x]['comunidadservicioscontratados'] = $this->ListadoServiciosContratados( $comunidadId );
                            }
    
                            //  Recuperamos la documentación asociada a la comunidad
                            if($_documentacionAsociada)
                            {
                                $data['Comunidad'][$x]['documentacioncomunidad'] = $this->GetDocumentacionComunidad( $comunidadId );
                            }
    
                            //  Grado de cumplimiento por tipo de servicio
                            if($_gradoCumplimiento)
                            {
                                for($i = 0; $i < count($servicios['Tiposservicios']); $i++)
                                {
    
                                    $nombreServicio = 'cumplimiento' . strtolower( $servicios['Tiposservicios'][$i]['nombre'] );
                                    $nombreServicio = str_replace(' ' , '', $nombreServicio);
                                    $idTipoServicio = $servicios['Tiposservicios'][$i]['id'];
                                    if(isset($data['Comunidad'][$x]['id']))
                                    {
                                        $idComunidad = $data['Comunidad'][$x]['id'];
                                        $data['Comunidad'][$x][$nombreServicio] = null;
                
                                        if($this->TieneServicioContratado( $idComunidad, $idTipoServicio ))
                                        {
                                        $data['Comunidad'][$x][$nombreServicio] = $this->CalcularGradoCumplimiento($idComunidad, str_replace('cumplimiento','',$nombreServicio));
                                        }
                                    }
                                }
                            }
    
    
                        }
                    }
                }
                

        }
    
        return $data;

    }

    /**
     * Comprueba si una comunidad tiene el servicio especificado contratado
     * @param int $idComunidad ID de la comunidad a consultar
     * @param int $idServicio ID del servicio que se va a consultar
     * @return bool Estado de contratación del servicio
     */
    private function TieneServicioContratado($idComunidad, $idServicio)
    {
        $servicio = $this->ComunidadModel->ServicioContratado($idComunidad, $idServicio);
        return ($servicio == -1 ? false : $servicio);
    }

    /** Recupera el listado de servicios contratados por una comunidad
     * @param int $id ID de la comunidad que se va a consultar
     */
    public function ListadoServiciosContratados($id)
    {
        if(is_null($id))
            return;

        $data = $this->ComunidadModel->ServiciosContratados($id);   

        //  Validamos que haya servicioscontratados para si no crear el mismo modelo y devolverlo
        if(is_null($data) || @count($data) == 0)
        {
            //  Recuperamos los precios base del producto
            $data = $this->ServiciosController->List(null)['Tiposservicios'];
        }  
            
        return $data;            
    }

    /** Devuelve las comunidades asignadas a un administrador o un contratista */
    public function ListComunidadesMenu($params = null)
    {

        if($this->isContratista())
        {
            $comunidades = $this->ComunidadModel->ListComunidadesMenuContratista($params);
            //  Ordenación del array para alfanumérico teniendo en cuenta los códigos
            if(count($comunidades) > 0)
            {
                $comunidades = $this->ordenarComunidades($comunidades);                
            }
            return $comunidades; //$this->ComunidadModel->ListComunidadesMenuContratista($params);   
        }else{

            // $administradorId = $this->UsuarioAutorizado($this->getLoggedUserId());
            $administradorId = $this->UsuarioController->IsAuthorizedUserByAdmin($this->getLoggedUserId());
            
            $comunidades = null;

            //  Recuperamos las comunidades que tiene asignado el autorizado
            if($administradorId !== false)
            {
                $comunidades =  $this->ComunidadModel->ListComunidadesMenu($params, true, $administradorId);
                $comunidadesAutorizado = $this->AutorizadoController->ComunidadesAsignadas($this->getLoggedUserId());
                $listadoComunidades = [];
                if(count($comunidadesAutorizado))
                {
                    for($i =  0; $i<count($comunidadesAutorizado); $i++)
                    {
                        $idComunidad = $comunidadesAutorizado[$i]['idcomunidad'];
                        $filtro = $this->ComunidadModel->filterResults($comunidades, 'Comunidad', 'id', $idComunidad);
                        if( !empty($filtro['Comunidad']) ){
                            $listadoComunidades[] = $filtro['Comunidad'][0];
                        }
                    }

                    //  Ordenamos el listado por código
                    if(count($listadoComunidades) > 0)
                    {
                        usort($listadoComunidades, fn($a, $b) => $a['codigo'] <=> $b['codigo']);//sort($listadoComunidades)
                    }
                    $comunidades['Comunidad'] = $listadoComunidades;
                }
            }else{
                $comunidades =  $this->ComunidadModel->ListComunidadesMenu($params, true);
            }

            //  Ordenación del array para alfanumérico teniendo en cuenta los códigos
            if(count($comunidades) > 0)
                $comunidades = $this->ordenarComunidades($comunidades);
 
            return $comunidades;

        }

    }

    public function GetTable($params)
    {
        return $this->ComunidadModel->GetTable($params);
    }

    /** Listado de servicios contratados por una comunidad */
    public function ListServiciosContratadosByComunidadId($id)
    {
        $data = $this->Get($id);

        //  Recuperamos los servicios contratados por la comunidad
        $data['Comunidad'][0]['servicioscontratados'] = $this->ListadoServiciosContratados($id);

        return helperController::successResponse( $data );
    }

    /**
     * Listado de comunidades con los servicios que tiene contratados por administrador
     * @param int $idAdministrador ID del administrador que se va a consultar
     * @param int $mesFacturacion Mes de facturación según su ordinal: 1,2,3,...
     * @param array $servicios Servicios que se van a consultar
     * @return object Listado
     */
    public function ListComunidadesWithServicesByAdministradorId($idAdministrador, $mesFacturacion = null, $servicios = null)
    {
        return $this->ComunidadModel->GetComunidadesAndServicesByAdministradorId($idAdministrador, $mesFacturacion, $servicios);
    }

    /** Listado de comunidades por id de administrador */
    public function ListComunidadesByAdministradorId($id)
    {
        $data = [];
        $data['ComunidadesAdministrador'] = $this->ComunidadModel->ListComunidadesByAdministradorId($id);
        $data['total'] = count($data);
        return HelperController::successResponse( $data );
    }

    /** Empresas asociadas a una comunidad */
    public function getEmpresasByComunidadId($id)
    {
        $data = [];
        $data['empresascomunidad'] = $this->ComunidadModel->GetEmpresasByComunidadId($id);
        
        if(!is_null($data['empresascomunidad']))
        {

            //  Por cada una de las empresas comprobamos si accedió alguna vez al sistema
            $this->InitController('Usuario');
            $this->InitController('Mensaje');
            $this->InitController('Documental');
            $this->InitController('Empresa');

            for($x = 0; $x < count($data['empresascomunidad']); $x++)
            {
                $idUsuario = $data['empresascomunidad'][$x]['idusuario'];
                $idEmpresa = $data['empresascomunidad'][$x]['id'];
                $emailEmpresa = $data['empresascomunidad'][$x]['email'];
                $usuarioData = $this->UsuarioController->Get($idUsuario);
    
                $lastLogin = null;
                $idmensajeregistro = -1;
    
                if(!is_null($usuarioData['Usuario']) && @count($usuarioData['Usuario']) > 0)
                {
                    $lastLogin =  $usuarioData['Usuario'][0]['lastlogin'];
                    $idmensajeregistro =  $this->MensajeController->GetEmailRegistroIdByEmail($usuarioData['Usuario'][0]['email']);
                }
    
                $data['empresascomunidad'][$x]['lastlogin'] = $lastLogin;
                $data['empresascomunidad'][$x]['idmensajeregistro'] = $idmensajeregistro;
                $data['empresascomunidad'][$x]['actuaciones'] = $this->EmpresaController->ListadoActuaciones($idEmpresa)['actuaciones'];
                //  Recuperamos el mail certificado si lo tuviese
                $data['empresascomunidad'][$x]['emailcertificado'] = $this->DocumentalController->GetEmailCertificadoEmpresaComunidad($idEmpresa, $id);
                //  Comprobamos si el e-mail del proveedor está en nuestra blacklist
                $data['empresascomunidad'][$x]['blacklist'] = $this->EmpresaController->EmailInBlacklist($emailEmpresa);
    
            }    
        }else{
            $data['empresascomunidad'] = [];
        }


        $data['total'] = count($data);
        return HelperController::successResponse( $data );
    
    }

    /** Devuelve el número de empresas que tiene asignadas una comunidad
     * @param int $idComunidad. ID de la comunidad a comprobar
     * @return int Número de empresas asignadas
     */
    public function TotalEmpresasAsignadas($idComunidad)
    {
        $this->ComunidadModel->SetId($idComunidad);
        return $this->ComunidadModel->TotalEmpresasAsignadas();
    }

    /** Asigna una empresa a una comunidad */
    public function asignarEmpresa($idcomunidad, $idempresa)
    {

        //  FIXME: Validamos el límite del número de empresas que tiene asignadas a una comunidad
            // $empresasAsignadas = $this->TotalEmpresasAsignadas($idcomunidad);
            // if(intval($empresasAsignadas) >= 4)
            //     return HelperController::errorResponse('error','Ha alcanzado el límite de asignación de empresas a una comunidad. Si necesita asignar más de 4 empresas, por favor, póngase en contacto con nosotros.', 200);

            $resultAsignacion = $this->ComunidadModel->asignarEmpresa($idcomunidad, $idempresa);

        //  Al asignar una nueva empresa hay que enviar el e-mail certificado con la información de la documentación básica
            $this->InitController('Documental');
            $this->DocumentalController->comprobarDocumentacionComunidad($idcomunidad, $idempresa);

        return HelperController::successResponse( $resultAsignacion );
    }

    public function GetDocumentacionComunidad($id)
    {
        return $this->ComunidadModel->GetDocumentacionComunidad($id);
    }

    /** Recupera los documentos relativos a certificados digitales */
    public function getDocumentacionCertificadoDigitalByComunidadId($id)
    {
        $data = [];
        $data['documentacioncertificado'] = $this->ComunidadModel->GetDocumentacionComunidadCertificadoDigital($id);

        //  Adjuntamos las descargas realizadas de los documentos
        if(count($data['documentacioncertificado']) > 0)
        {
            
            for($x = 0; $x < count($data['documentacioncertificado']); $x++)
            {
                $data['documentacioncertificado'][$x]['descargas'] = $this->ComunidadModel->GetDescargasByFicheroId( $data['documentacioncertificado'][$x]['idficherorequerimiento']);
            }
        }

        if(count($data) > 0)
        {
            //  Recuperamos el historial de subidas de documento
                $this->InitController('Historico');

                for($n = 0; $n < count($data['documentacioncertificado']); $n++)
                {
                    $data['documentacioncertificado'][$n]['historico'] = $this->HistoricoController->TieneHistorico( $data['documentacioncertificado'][$n]['idrelacion'], 'certificadorequerimiento');
                }
        }

        $data['total'] = count($data);
        return $data;
        
    }    

    /** Recupera la documentación para una comunidad por su id */
    public function getDocumentacionByComunidadId($id)
    {
        $data = [];
        $data['documentacioncomunidad'] = $this->ComunidadModel->GetDocumentacionComunidad($id);

        //  Adjuntamos las descargas realizadas de los documentos
        if(count($data['documentacioncomunidad']) > 0)
        {
            for($x = 0; $x < count($data['documentacioncomunidad']); $x++)
            {
                $data['documentacioncomunidad'][$x]['descargas'] = $this->ComunidadModel->GetDescargasByFicheroId( $data['documentacioncomunidad'][$x]['idficherorequerimiento']);
            }
        }

        if(count($data) > 0)
        {
            //  Recuperamos el historial de subidas de documento
                $this->InitController('Historico');

                for($n = 0; $n < count($data['documentacioncomunidad']); $n++)
                {
                    $data['documentacioncomunidad'][$n]['historico'] = $this->HistoricoController->TieneHistorico( $data['documentacioncomunidad'][$n]['idrelacion'], 'comunidadrequerimiento');
                }
        }

        $data['total'] = count($data);
        return HelperController::successResponse( $data );
        
    }

    /** Elimina la relación entre la comunidad y una empresa */
    public function DeleteRelacionEmpresaComunidad($idComunidad, $idEmpresa)
    {
        if( $this->ComunidadModel->DeleteRelacionEmpresaComunidad($idComunidad, $idEmpresa) )
        {
            return HelperController::successResponse('ok');
        }else{
            return HelperController::errorResponse('error','No se ha podido eliminar la relación entre empresa y comunidad',200);
        }
    }

    public function ListServiciosContratadosComunidades($params){
        return HelperController::successResponse( $this->ComunidadModel->ListServiciosContratadosComunidades($params) );
    }

    /** Envía un e-mail al master con la información de la comunidad */
    public function EnviarEmailAltaComunidad($codigoComunidad, $nombreComunidad)
    {
        $body = $this->GetTemplateEmailAltaComunidad();
        $body = str_replace('[@codigo_comunidad@]', $codigoComunidad, $body);
        $body = str_replace('[@comunidad@]', $nombreComunidad, $body);
        $body = str_replace('[@administrador@]', $this->getLoggedUserName(), $body);
        $body = str_replace('[@fecha@]', date('d/m/Y'), $body);
        
        $this->SendEmail(ADMINMAIL, 'Fincatech - Cristóbal', 'Alta de nueva comunidad', $body);

    }

    /** Envía un e-mail al master con la información de la comunidad que ha dado de baja */    
    public function EnviarEmailBajaComunidad($codigoComunidad, $nombreComunidad)
    {
        
        $body = $this->GetTemplateEmailBajaComunidad();
        $body = str_replace('[@codigo_comunidad@]', $codigoComunidad, $body);
        $body = str_replace('[@comunidad@]', $nombreComunidad, $body);
        $body = str_replace('[@administrador@]', $this->getLoggedUserName(), $body);
        $body = str_replace('[@fecha@]', date('d/m/Y'), $body);
        
        $this->SendEmail(ADMINMAIL, 'Fincatech - Cristóbal', 'Baja de comunidad', $body);

    }

    /** Recupera el template del alta de comunidad */
    public function GetTemplateEmailAltaComunidad(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/alta_comunidad.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

    /** Recupera el template de baja de comunidad por parte de cualquier usuario*/
    public function GetTemplateEmailBajaComunidad(){
        $vistaRenderizado = ABSPATH.'src/Views/templates/mails/baja_comunidad.html';
        ob_start();
            include_once($vistaRenderizado);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
    }

    /** Recupera las empresas asociadas a una comunidad */
    private function EmpresasComunidad($idComunidad)
    {
        return $this->ComunidadModel->GetEmpresasByComunidadId($idComunidad, false);
    }

    private function CalcularGradoCumplimiento($idComunidad, $nombreServicio)
    {

        $gradoCumplimiento = 0;

        switch($nombreServicio)
        {
            case 'cae':
                $gradoCumplimiento = $this->GradoCumplimientoCAE($idComunidad);
                break;
            case 'rgpd':
                $gradoCumplimiento = $this->GradoCumplimientoRGPD($idComunidad);
                break;

        }

        return $gradoCumplimiento;

    }

    /** Calcula el grado de cumplimiento en materia de RGPD por parte de una comunidad */
    private function GradoCumplimientoRGPD($idComunidad)
    {

            $this->InitController('Documental');

            $infoComunidad = $this->Get($idComunidad, false);
            // echo 'GradoCumplimientoRGPD. ID Comunidad: ' . $idComunidad;
            // var_dump($infoComunidad);
            if(is_null($infoComunidad) || @isset($infoComunidad['error'])){
                return 0;
            }

            $tieneCamaraSeguridad = is_null($infoComunidad['Comunidad'][0]['camarasseguridad']) ? false : $infoComunidad['Comunidad'][0]['camarasseguridad'];

        //  Recuperamos el total de requerimientos de RGPD para una comunidad
            $totalRequerimientos            = $this->DocumentalController->GetTotalRequerimientosRGPDComunidad( $this->getLoggedUserId(), $idComunidad, $tieneCamaraSeguridad );  
        //  Calculamos el número de requerimientos pendientes de RGPD para una comunidad
            $totalRequerimientosPendientes  = $this->DocumentalController->GetTotalRequerimientosPendientesRGPDComunidad($idComunidad, $this->getLoggedUserId(), $tieneCamaraSeguridad);

            if($totalRequerimientosPendientes > 0)
            {
                $totalGradoCumplimiento = round( ((($totalRequerimientos - $totalRequerimientosPendientes) * 100) / $totalRequerimientos), 2);
            }else{
                $totalGradoCumplimiento = 100;
            }        

            return $totalGradoCumplimiento;

    }

    /** Devuelve el grado de cumplimiento en materia de CAE por una comunidad */
    private function GradoCumplimientoCAE($idComunidad)
    {

        $grado = 0;

        //  Porcentaje que se aplica a aquellas comunidades que tienen subidos los 3 documentos y además tienen empresas asignadas
        $porcentajeDocumentosSubidosConEmpresas = 50;

        //  Porcentaje que se aplica sobre aquellas empresas que han recibido el e-mail certificado
        $porcentajeEmailCertificado = 35;

        //  Porcentaje que se aplica sobre aquellas empresas que tienen todos los documentos subidos en materia de CAE
        $porcentajeDocumentosSubidosEmpresas = 15;

        $empresas = $this->EmpresasComunidad($idComunidad);
        if(!is_null($empresas))
        {
            $numeroEmpresas = count($empresas);
        }else{
            $numeroEmpresas = 0;
        }

        //  Si no tiene empresas, directamente tiene 0%
        if($numeroEmpresas === 0)
            return 0;

        //  Si tiene empresas, se comprueba si ha subido los documentos obligatorios de CAE para la comunidad
        $documentosSubidos = 0;

        //  Comprobamos si tiene todos los documentos subidos en materia de CAE
        $documentacionComunidad = $this->ComunidadModel->GetDocumentacionComunidad($idComunidad);
        if(count($documentacionComunidad) > 0)
        {
            for($x = 0; $x < count($documentacionComunidad); $x++)
            {
                if($documentacionComunidad[$x]['idrelacion'] !== '' && $documentacionComunidad[$x]['idrelacion'] !== 'null')
                    $documentosSubidos++;
            }
        }

        //  Si tiene los documentos subidos y además tiene empresas asignadas
        if( $documentosSubidos == ( count($documentacionComunidad) ) )
        {
            $grado += $porcentajeDocumentosSubidosConEmpresas;
        }

        //  Calculamos cuántas empresas han recibido el e-mail certificado
        $porcentajeEmailCertificado = $porcentajeEmailCertificado / $numeroEmpresas;
        for($xEmpresa = 0; $xEmpresa < $numeroEmpresas; $xEmpresa++)
        {
            $emailCertificado = $this->ComunidadModel->EmailCertificadoEnviadoEmpresa( $idComunidad, $empresas[$xEmpresa]['id']);
            if(is_array($emailCertificado))
            {
                //  Si ha recibido e-mail certificado
                if( count($emailCertificado) > 0 )
                {
                        if(trim($emailCertificado[0]['filename']) != '' && $emailCertificado[0]['filename'] != 'null')
                        {
                            $grado += $porcentajeEmailCertificado;
                        }
                }
            }

        }

        //  TODO: Calculamos por cada una de las empresas, cuántas tienen todos los documentos subidos en materia de CAE
        $this->InitController('Empresa');
        $porcentajeDocumentosSubidosEmpresas = $porcentajeDocumentosSubidosEmpresas / $numeroEmpresas;
        for($iEmpresa = 0; $iEmpresa < count($empresas); $iEmpresa++)
        {

           if( $this->EmpresaController->ValidacionDocumentacionCAECompleta($empresas[$iEmpresa]['idusuario']) )
           {
                $grado += $porcentajeDocumentosSubidosEmpresas;
           }

        }
        return ($grado >= 100 ? 100 : number_format($grado, 2, '.',','));

    }

    /**
     * Ordena las comunidades por código alfanumérico
     */
    private function ordenarComunidades($data, $limitStart = null, $limitLength = null,  $orderBy = 'asc')
    {

        $arrayComunidades = $data['Comunidad'];
        usort($arrayComunidades, ['HappySoftware\Controller\HelperController', 'customAlphanumericSort']);
        
        $inicio = $limitStart * $limitLength;

        //  Si tiene paginación hay eliminar del array los que no nos sirven
        if(!is_null($limitStart) && !is_null($limitLength))
        {
            $arrayComunidades = array_slice($arrayComunidades, $limitStart, $limitLength);
        }
        $data['Comunidad'] = $arrayComunidades; 
        return $data;        
    }

    public function GetUsuarioAutorizado($idComunidad)
    {
        $usuarioAutorizado = false;
        //  Establecemos el ID de la comunidad
        $this->ComunidadModel->SetId($idComunidad);
        //  Recuperamos si es una comunidad asignada a un usuario autorizado
        $result = $this->ComunidadModel->UsuarioAutorizado();

        if(count($result) > 0)
        {
            $usuarioAutorizado = $this->UsuarioController->Get($result[0]['idautorizado'], false);
        }

        if($usuarioAutorizado !== false && @count($usuarioAutorizado) > 1)
        {
            $usuarioAutorizado = $usuarioAutorizado['Usuario'][0];
        }

        return $usuarioAutorizado;

    }

    /** Listado de proveedores asignados a las comunidades */
    public function ProveedoresAsignados()
    {
        $proveedoresAsignados = [];
        $comunidades = [];
        $this->ComunidadModel->SetUsuarioId($this->getLoggedUserId());

        //  Comprobamos si es un usuario autorizado o el principal
        $administradorAutorizado = $this->UsuarioController->IsAuthorizedUserByAdmin($this->getLoggedUserId());

        if($administradorAutorizado !== false)
        {
            $comunidades['Comunidad'] = $this->ComunidadModel->ProveedoresAsignadosAutorizado();
        }else{
            $comunidades['Comunidad'] = $this->ComunidadModel->ProveedoresAsignados();
        }

        $comunidades = $this->ordenarComunidades($comunidades);

        $proveedoresAsignados['proveedores'] = $comunidades['Comunidad'];
        return $proveedoresAsignados;
    }

    /**
     * Bulk update for community status
     * @param string $newStatus New Status to apply
     * @param int $idComunidad (optional) ID de la comunidad. If null, update all comunities for administrador
     * @param int $idAdministrador (optional) ID del administrador.
     * @return string result
     */
    public function BulkChangeStatus($newStatus, $idComunidad = null, $idAdministrador = null)
    {

        $result = '';
        
        if(is_null($idComunidad) && is_null($idAdministrador))
            return 'No se ha podido cambiar el estado de las comunidades debido a que no viene informada ni la comunidad ni el administrador';

        if(!is_null($idAdministrador))
            $this->ComunidadModel->SetUsuarioId($idAdministrador);

        //  Update communities status on repository
        $this->ComunidadModel->SetEstado($newStatus);

        //  Launch update to repository
        $this->ComunidadModel->BulkUpdateStatus();

        return true;
    }

}