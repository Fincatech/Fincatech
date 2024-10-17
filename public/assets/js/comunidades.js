let comunidadesCore = {

    comunidades: Object(),
    comunidad: Object(),

    accessCae: true,
    accessRgpd: true,

    iTotalDocumentos: 0,
    iTotalDocumentosPendientes: 0,
    iTotalDocumentosSubidos: 0, 

    init: async function()
    {
    
        //  Validamos los accesos del administrador
        let p = new Promise(async(resolve, reject)=>{
            await comunidadesCore.checkAccess();
            resolve(true);
        });
        
        p.then(async (result) =>{
            //  Bindeamos los eventos de los diferentes botones de comunidades
            comunidadesCore.events();
        
            // await comunidadesCore.renderMenuLateral();

            //  Comprobamos si se está cargando el listado
                if(core.actionModel == "list" && core.model.toLowerCase() == "comunidad")
                {

                    if(core.Security.getRole() === 'SUDO')
                    {
                        comunidadesCore.Render.tablaListadoComunidades();
                    }else{
                        await comunidadesCore.listadoDashboard();
                    }

                }

                if(core.actionModel == 'get' && core.model.toLowerCase() == "comunidad"){
                    // TODO: Implementar posibilidad de añadir varios campos para el título
                    let titulo = `${core.Modelo.entity['Comunidad'][0]['codigo']} - ${core.Modelo.entity['Comunidad'][0]['nombre']}`;
                    CoreUI.setTitulo(titulo);
                    comunidadesCore.comprobarServiciosContratados();
                }
                

            //  Listado de servicios contratados por las comunidades
                if(core.actionModel == 'servicios-contratados')
                {
                    comunidadesCore.Render.paginacion.init();
                    comunidadesCore.Render.tablaServiciosContratadosComunidades();
                }

            //  Inicialización del masked para la cuenta IBAN
                $('#ibancomunidad').mask('SS00 0000 0000 00 0000000000');

                if(core.Security.getRole() == 'CONTRATISTA'){
                    $('.tabDatos').hide();
                }

                if(core.Security.getRole() == 'SUDO'){
                    $('.tabDatos').show();
                }

                if(core.actionModel == 'add'){
                    $('.tabDatos').show();
                }

            });

    },

    /** Check access to cae and rgpd for community */
    checkAccess: async function()
    {
        comunidadesCore.accessCae = core.Security.userData.mostrarcae;
        comunidadesCore.accessRgpd = core.Security.userData.mostrarrgpd;
    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType,'.btnLimpiarBusqueda', function(evt){
            $('.busquedaComunidad').val('');
            CoreUI.Sidebar.Comunidades.buscarComunidad();
        });

        $('body').on('keyup', '.busquedaComunidad', function(evt)
        {
            CoreUI.Sidebar.Comunidades.buscarComunidad();
        });

        $('body .form-comunidad').off(core.helper.clickEventType).on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.guardarComunidad();
        });    

    //  Botón ver comunidad
        $('body').on(core.helper.clickEventType, '.btnVerComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.verModalComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    //  Eliminar comunidad
        $('body').on(core.helper.clickEventType, '.btnEliminarComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    //  Asignar empresa a comunidad
        $('body').on(core.helper.clickEventType, '.btnConfirmarEmpresaCAE', function(e)
        {
        
            //  Creamos la asociación
                comunidadesCore.asignarEmpresa( $(this).attr('data-id') );

        });

    //  Desasignar empresa a comunidad
        $('body').on(core.helper.clickEventType, '.btnEliminarEmpresaComunidad', function(e)
        {
            e.stopImmediatePropagation();
            comunidadesCore.eliminarEmpresaComunidad( $(this).attr('data-id'), $(this).attr('data-nombre') );
        });

    //  Mostrar modal de asignar empresa a comunidad
        $('body').on(core.helper.clickEventType, '.btnAsociarEmpresaCAE', function(e)
        {
            //  Validamos el número de empresas que tiene asociadas actualmente
            if(window['tablelistadoEmpresaComunidad'].data().length >= parseInt(core.Modelo.entity.Comunidad[0].limiteempresas))
            {
                CoreUI.Modal.Info(`Ha alcanzado el límite de asignación de empresas a una comunidad.<br>Si necesita asignar más de ${core.Modelo.entity.Comunidad[0].limiteempresas} empresas, por favor, póngase en contacto con nosotros.`);
            }else{
                comunidadesCore.mostrarModalAsociarEmpresa();
            }
        });

    //  Cambio nombre de comunidad
        $('body .form-comunidad #nombre').on('keyup', function(e)
        {
            CoreUI.Utils.setTituloPantalla(null, null, `${$('.form-comunidad #codigo').val()} ${$(this).val()}`);
            //  Cambiamos el nombre en el menú lateral siempre que tenga id
            $('.sidebar-item .comunidad-' + core.modelId).text(`${$('.form-comunidad #codigo').val()} ${$(this).val()}`);
        });

    //  Cambio de nombre/código de comunidad
        $('body .form-comunidad #codigo').on('keyup', function(e)
        {
            //  Cambiamos el nombre en el menú lateral siempre que tenga id
                $('.sidebar-item .comunidad-' + core.modelId).text(`${$(this).val()} ${$('.form-comunidad #nombre').val()}`);
        });

    //  Validación de cuenta IBAN
        $('body').on('blur', '#ibancomunidad', function(e)
        {
            comunidadesCore.validarCuentaIBAN();
        });

    //  Tab de documentación de comunidad (Requerimientos)
        $('body').on(core.helper.clickEventType, '.enlaceDocumentacionComunidad', function(evt)
        {
            documentalCore.Comunidad.renderTablaDocumentacionComunidad(core.modelId);
        });

    //  Enlace CAE No contratado
        $('body').on(core.helper.clickEventType, '.enlaceKOCae', function(evt)
        {
            CoreUI.Modal.Info('Actualmente no tiene contratado el servicio de CAE para esta comunidad.<br><br>Si desea contratarlo, por favor contacte con el departamento de administración de Fincatech o escriba a info@fincatech.es','Servicio no contratado');
        });

    //  Enlace RGPD No contratado
        $('body').on(core.helper.clickEventType, '.enlaceKORGPD', function(evt)
        {
            CoreUI.Modal.Info('Actualmente no tiene contratado el servicio de RGPD para esta comunidad.<br><br>Si desea contratarlo, por favor contacte con el departamento de administración de Fincatech o escriba a info@fincatech.es','Servicio no contratado');
        });    

    //  Certificado digital no contratado
        $('body').on(core.helper.clickEventType, '.enlaceKOCertificadoDigital', function(evt)
        {
            CoreUI.Modal.Info('Actualmente no tiene contratado el servicio de Certificado Digital para esta comunidad.<br><br>Si desea contratarlo, por favor contacte con el departamento de administración de Fincatech o escriba a info@fincatech.es','Servicio no contratado');
        });        

    //  Cámaras de seguridad
        $('body').on(core.helper.clickEventType, '#chkTieneCamarasSeguridad', function(ev)
        {
            var seleccion = ($(this).prop('checked') ? 1 : 0);
            var data = Object();
            data.seleccionada = seleccion;
            // if( $(this).prop('checked'))
            // {
                //CoreUI.Modal.Info('Ha indicado que la comunidad tiene cámara de seguridad.<br><br>Le recordamos que es obligatorio que suba el documento <strong>Registro de actividades de tratamiento</strong> según el requerimiento de Instrucciones Generales para cumplir con la normativa relativa a la Ley de Protección de datos.', 'RGPD');
                //  Mandamos al endpoint la información
                apiFincatech.post(`comunidad/${core.modelId}/camaraseguridad`,data).then( result =>{
                    //  Recargamos el listado de cámaras de seguridad
                    requerimientoCore.renderTablaCamarasSeguridad(core.modelId);
                });
            // }
        });

    //  Comunidad pendiente
        $('body').on(core.helper.clickEventType, '.btnAvisoEstado', function(ev)
        {
            CoreUI.Modal.Info('En breve estará activada la comunidad','Comunidad pendiente activación');
        });

    //  Ver empleados de la comunidad
        $('body').on(core.helper.clickEventType, '.btnVerEmpleadosComunidad', function(ev)
        {
           // empleadoCore.renderTablaEmpleadosEmpresaComunidad(core.modelId);
        });

    //  Adjuntar operatoria entre comunidad y empresa externa
        $('body').on(core.helper.clickEventType, '.btnAdjuntarContrato', async function()
        {

            documentalCore.idcomunidad = $(this).attr('data-idcomunidad');
            documentalCore.idempresa = $(this).attr('data-idempresa');
            documentalCore.idempleado = $(this).attr('data-idempleado');
            documentalCore.idadministrador = $(this).attr('data-idadministrador');
            documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');
            documentalCore.idrelacionrequerimiento = $(this).attr('data-idrelacionrequerimiento');
            documentalCore.entidad = $(this).attr('data-entidad');

            const { value: file } = await Swal.fire({
            title: '',
            html: Constantes.CargaDocumento,
            showCancelButton: false,
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: function()
            {

                //  Inicializamos el componente de ficheros
                    core.Files.init();
            }});

        }); 

    //  Empresas externas asociadas a la comunidad
        $('body').on(core.helper.clickEventType, '.enlaceEmpresasExternas', function(e){
            e.stopImmediatePropagation();
            comunidadesCore.Render.tablaEmpresasConcurrentes();
        });

        $('body').on(core.helper.clickEventType, '#tablaEmpresasConcurrentes tr', function(){
            comunidadesCore.Render.tablaDocumentosEmpresaConcurrente($(this).attr('id'));
        });

    //  Listado de proveedores asignados a las comunidades
        if($('#listadoComunidadesProveedores').length){
            comunidadesCore.Render.tablaProveedores();
        }

    },

    guardarComunidad: function()
    {   
        if(core.Security.getRole() != 'ADMINFINCAS' && core.Security.getRole() != 'SUDO')
        {
            CoreUI.Modal.Error('Su tipo de usuario no tiene privilegios suficientes para poder modificar los datos de la comunidad','Permisos insuficientes');
            return false;
        }

        //  Lo primero es validar que el código iban sea correcto
        if( $('#ibancomunidad').val() != '')
        {
            if( !comunidadesCore.validarCuentaIBAN() )
            {
                CoreUI.Modal.Error('El código IBAN no es correcto. Por favor, revise el número proporcionado');
                return false;
            }
        }

        if(core.Forms.Validate('form-comunidad'))
        {

        //  Mapeamos los datos iniciales de la comunidad
            core.Forms.mapDataToSave();

        //  Mapeamos los datos de los servicios contratados
            if(core.Security.getRole() != 'ADMINFINCAS')
                serviciosCore.mapServiciosContratados();

        //  Si es una nueva mapeamos si desea contratar servicios
            if($('#chkServicioCae').length && $('#chkServicioRGPD').length)
            {
                core.Forms.data.servicioRGPD = ($('#chkServicioRGPD').is(':checked') ? 1 : 0);
                core.Forms.data.servicioCAE = ($('#chkServicioCae').is(':checked') ? 1 : 0);
            }

            if(core.Security.getRole() == 'ADMINFINCAS')
            {
                if(core.Forms.data.servicioRGPD == 0 && core.Forms.data.servicioCAE == 0)
                {
                    CoreUI.Modal.Error('Debe seleccionar al menos un servicio para contratar','Alta de comunidad');
                    return;
                }
            }

        //  Guardamos los datos ya mapeados correctamente
            core.Forms.Save( true );

        }else{
            CoreUI.Modal.Error('Rellene los campos obligatorios para poder guardar la comunidad', 'Formulario incompleto');
        }

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("comunidad", id, nombre, "listadoComunidad");
    },

    validarCuentaIBAN: function()
    {
        var iban = $('#ibancomunidad').unmask().val().trim();
        $('#ibancomunidad').mask('SS00 0000 0000 00 0000000000');

        if( !core.Validator.checkIBAN ( iban ))
        {
            $('body #ibancomunidad').css('border-color', 'red');
            return false;
        }else{
            $('body #ibancomunidad').css('border-color', '');
            return true;
        }
    },

    /**
     * Asigna una empresa a una comunidad
     * @param {*} id 
     */
    asignarEmpresa: async function(id)
    {
            var existe = false;
            console.log(id);
        //  Comprobamos que no esté asociada ya la empresa
            if(core.Modelo.entity.Comunidad[0].empresascomunidad.length)
            {
                //  Buscamos si ya está asignada
                    for(y=0; y < core.Modelo.entity.Comunidad[0].empresascomunidad.length; y++)
                    {
                        if( core.Modelo.entity.Comunidad[0].empresascomunidad[y].id == id)
                        {
                            existe = true;
                            break;
                        }
                    }
            }

            if(existe)
            {
                CoreUI.Modal.Error("La empresa ya está asignada a esta comunidad", null, function()
                {
                    comunidadesCore.mostrarModalAsociarEmpresa();
                });
            }else{

                datos = Object();
                datos = {
                    idcomunidad: core.modelId,
                    idempresa: id
                };

                await apiFincatech.post(`comunidad/${core.modelId}/empresa/${id}/asignar`, datos).then(async ( response ) =>
                {
                    var responseData = JSON.parse(response);

                    if(responseData.status['response'] == "ok")
                    {
                        CoreUI.Modal.Success("La empresa se ha asignado correctamente y ha sido notificada mediante un e-mail para que indique que trabajador va a acceder a la comunidad.");

                        //  Recargamos la tabla de empresas para reflejar el cambio
                            window['tablelistadoEmpresaComunidad'].ajax.reload();
                            
                    }else{
                        //  TODO: Ver cuál es el error en el json
                        Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);

                    }
                });

            }

        //  Enviamos la información al endpoint
        //
    },

    /**
     * Elimina la relación entre una empresa y una comunidad
     * @param {*} id 
     * @param {*} nombre 
     */
    eliminarEmpresaComunidad: async function(id, nombre)
    {
        core.Modelo.Delete(`comunidad/${core.modelId}/empresa`, id, nombre, "listadoEmpresaComunidad", '¿Desea eliminar la relación de la empresa con la comunidad?').then( () =>{
            window['tablelistadoEmpresaComunidad'].ajax.reload();
        });
    },

    mostrarModalAsociarEmpresa: async function()
    {
        const { value: file } = await Swal.fire({
        title: '',
        html: Constantes.AsignacionEmpresa,
        showCancelButton: false,
        showConfirmButton: false,
        width: '75em',
        showCloseButton: true,
        didOpen: function(e)
        {

            //  Iniciamos la tabla de empresas simple
            //    empresaCore.renderTablaSimple();
        }});    
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalComunidad: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        comunidadesCore.getComunidad(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "comunidades/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, comunidadesCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, comunidadesCore.comunidad.nombre);
            });

        });
    },

    renderMenuLateral: async function()
    {

        apiFincatech.get('comunidad/listmenu').then( (result) => {

            comunidadesCore.comunidades = JSON.parse(result);
            // console.log(comunidadesCore.comunidades);

            // // $('.navComunidades').append('<li class="sidebar-header text-uppercase" style="font-size:1.3rem;">Mis comunidades</li>');
            // $('.navComunidades').append(`
            //     <li class="pl-3 pr-3 pb-2 pt-2 mb-3 mt-2">
            //         <p class="text-white text-uppercase mt-0 mb-0"><small>Buscar comunidad</small></p>
            //         <div class="row">
            //             <div class="col-10">
            //                 <input type="text" class="form-control busquedaComunidad" placeholder="Escriba cód o nombre">
            //             </div>
            //             <div class="col-2 align-self-center">
            //                 <a href="javascript:void(0);" class="btnLimpiarBusqueda"><i class="bi bi-x-circle text-white"></i></a>
            //             </div>
            //     </li>`);
            $('.sidebar-nav').html('');
                $('.sidebar-nav').prepend(`
                <div class="row pl-3 pr-3 sticky-top" style="background: #222e3c;">
                    <div class="col-12 pb-2 pt-2 mb-3 mt-2">
                        <p class="text-uppercase mt-0 mb-1 text-white"><small><i class="bi bi-search pr-1"></i> Buscar comunidad</small></p>
                        <div class="row">
                            <div class="col-10 pr-0">
                                <input type="text" class="form-control busquedaComunidad" placeholder="Escriba código o nombre" style="border-radius:12px;">
                            </div>
                            <div class="col-2 align-self-center">
                                <a href="javascript:void(0);" class="btnLimpiarBusqueda"><i class="bi bi-x-circle text-white"></i></a>
                            </div>
                        </div>
                    </div>
                </div>`);

            comunidadesCore.comunidades.data.Comunidad.forEach( function(valor, indice, array){

            var enlaceEstado = (valor['estado'] == 'P' ? 'javascript:void(0);' : `${config.baseURL}comunidad/${valor['id']}`);

            var claseSegunEstado = (valor['estado'] == 'P' ? 'btnAvisoEstado' : '');
            if(valor['estado'] != 'H')
            {
                var html = `<li class="sidebar-item pl-3 pr-3 pb-2 pt-2" data-codigo="${valor['codigo']}" data-nombre="${valor['nombre']}">
                                    <div class="row">
                                        <div class="col-1 text-left pl-0 pr-0 align-self-center">
                                           <!-- <img src="${config.baseURL}public/assets/img/icon_edificio.png" class="img-responsive feather"> -->
                                           <i class="bi bi-building text-white"></i>
                                        </div>

                                        <div class="col-11 pr-2 pl-1">
                                            <a href="${enlaceEstado}" class=" text-white d-block ${claseSegunEstado}">   
                                                <span class="align-middle comunidad-${valor['id']} d-block">${valor['codigo']} ${valor['nombre']}</span>
                                            </a>
                                        </div>

                                        <!--<div class="col-2 pr-0 text-center pl-0 align-self-center">
                                            <a href="javascript:void(0);" class="btnEliminarComunidad" data-id="${valor['id']}" data-nombre="${valor['nombre']}">
                                                <i data-feather="trash-2" class="text-danger"></i>
                                            </a>
                                        </div>-->
                                    </div>
                            </li>`;
                $('.navComunidades').append(html);
            }
            
        });
            feather.replace();
        });

    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTabla: async function()
    {

        // await comunidadesCore.checkAccess();

        if($('#listadoComunidad').length)
        {
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
    
                //  Código
                    CoreUI.tableData.addColumn('listadoComunidad', "codigo","COD", null, null, '120px');
    
                //  Nombre
                    CoreUI.tableData.addColumn('listadoComunidad', "nombre", "NOMBRE", null, null);
    
                //  Si el usuario tiene acceso a CAE mostramos la información correspondiente
                if(core.Security.userData.mostrarcae == true){
    
                //  Cumplimiento CAE
                    CoreUI.tableData.addColumn('listadoComunidad',function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        if(row.cumplimientocae == null)
                        {
                            htmlSalida = '<p class="mb-0">No contratado</p>';
                        }else{
                            if(parseFloat(row.cumplimientocae) >= 75)
                            {
                                bgcolor = 'success';
                                border = 'success';
                            }else{
                                bgcolor = 'warning';
                                border = 'warning';
                            }
                            htmlSalida = `
                            <div class="mr-3 ml-3 border progress shadow-inset" style="background-color: white !important; border-radius: 8px;">
                                <div class="progress-bar bg-${bgcolor}" role="progressbar" style="border:0 !important; width: ${row.cumplimientocae}%;" aria-valuenow="${row.cumplimientocae}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div> 
                            <p class="m-0 text-center"><small>${row.cumplimientocae}%</small></p>                   
                            `;
                        }
    
                        return htmlSalida;
    
                    }, "CUMP. CAE", null, 'text-center', '180px');
                }
    
                if(core.Security.userData.mostrarrgpd == true)
                {
                //  Cumplimiento RGPD
                    CoreUI.tableData.addColumn('listadoComunidad',function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        if(row.cumplimientorgpd == null)
                        {
                            htmlSalida = '<p class="mb-0">No contratado</p>';
                        }else{
                            if(parseFloat(row.cumplimientorgpd) >= 75)
                            {
                                bgcolor = 'success';
                                border = 'success';
                            }else{
                                bgcolor = 'warning';
                                border = 'warning';
                            }
                            htmlSalida = `
                            <div class="mr-3 ml-3 border progress shadow-inset" style="background-color: white !important;">
                                <div class="progress-bar bg-${bgcolor}" role="progressbar" style="border:0 !important; width: ${row.cumplimientorgpd}%;" aria-valuenow="${row.cumplimientorgpd}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div> 
                            <p class="m-0 text-center"><small>${row.cumplimientorgpd}%</small></p>                   
                            `;
                        }
    
                        return htmlSalida;
    
                    }, "CUMP. RGPD", null, 'text-center', '180px');
                }
    
                // Estado
                    var html = 'data:estado$';
                    CoreUI.tableData.addColumn('listadoComunidad', null, "Estado", html, 'text-center', '100px');
    
                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                        html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$?view=1" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoComunidad', null, "", html, null, '60px');
    
                // $('#listadoComunidad').addClass('no-clicable');
                CoreUI.tableData.render("listadoComunidad", "Comunidad", "comunidad/list", false, true, true, null, false, false, null, true);
        }

    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTablaComunidadesAdministrador: async function(id)
    {
        if($('#listadoComunidadesAdministrador').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn('listadoComunidadesAdministrador', "codigo","COD");
            //  Nombre
            CoreUI.tableData.addColumn('listadoComunidadesAdministrador', "nombre", "NOMBRE");

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn('listadoComunidadesAdministrador', null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn('listadoComunidadesAdministrador', "telefono", "TELEFONO");

            // IBAN
            CoreUI.tableData.addColumn('listadoComunidadesAdministrador', "ibancomunidad", "Iban");

            // //  Pendientes de verificar
            // CoreUI.tableData.addColumn('listadoComunidadesAdministrador', "nombre", "doc pend. de verificar");

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoComunidadesAdministrador', null, "Fecha alta", html);

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoComunidadesAdministrador', null, "Estado", html);

            CoreUI.tableData.render("listadoComunidadesAdministrador", "ComunidadesAdministrador", `administrador/${id}/comunidades`);
        }

    },

    renderTablaDocumentacion: async function(id)
    {
        if($('#listadoDocumentacionComunidad').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn('listadoDocumentacionComunidad', "requerimiento",'Documento');

            // //  Fecha de alta
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn('listadoDocumentacionComunidad', null, "Fecha alta", html);

            // Estado
                // var html = 'data:estado$';
                // CoreUI.tableData.addColumn('listadoDocumentacionComunidad', null, "Estado", html);

            CoreUI.tableData.render("listadoDocumentacionComunidad", "Comunidad[0].documentacioncomunidad", `comunidad/${id}`);
        }
    },

    /** Recupera el listado de comunidades en el dashboard */
    listadoDashboard: async function()
    {
        // await comunidadesCore.checkAccess();
        this.renderTabla();  
    },

    getComunidad: async function(comunidadId)
    {
        await apiFincatech.get('comunidad/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidad = responseData.Comunidad[0];
            console.log(comunidadesCore.comunidad);
            return comunidadesCore.comunidad;
        });
    },

    /** Recupera los datos de las comunidades desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('comunidad/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidades = responseData.Comunidad;
            comunidadesCore.comunidades.total = comunidadesCore.comunidades.length;
        });

    },

    /**
     * Comprueba si un administrador ha adjuntado previamente el contrato entre admin y comunidad
     * @param {int} idadministrador 
     * @param {int} idcomunidad 
     */
    comprobarContratoAdministradorComunidad: function(idadministrador, idcomunidad)
    {

    },

    comprobarServiciosContratados: function()
    {
        return;
        if(typeof core.Modelo.entity['Comunidad'][0]['comunidadservicioscontratados'][0]['contratado'] !== 'undefined')
        {
            // console.log('CAE Contratado ---> ' + responseData.comunidadservicioscontratados[0]['contratado']);
            caeContratado = ( core.Modelo.entity['Comunidad'][0].comunidadservicioscontratados[0]['contratado'] == '0' ? false : true);
        }

        if(typeof core.Modelo.entity['Comunidad'][0].comunidadservicioscontratados[4]['contratado'] !== 'undefined')
        {
            // console.log('RGPD Contratado ---> ' + responseData.comunidadservicioscontratados[4]['contratado']);
            rgpdContratado = (core.Modelo.entity['Comunidad'][0].comunidadservicioscontratados[4]['contratado'] == '0' ? false : true);
        }

        if(core.Security.getRole() == 'CONTRATISTA' || core.Security.getRole() == 'TECNICOCAE')
        {
            $('.enlaceRGPD').remove();
            $('.btnAsociarEmpresaCAE').remove();
            $('.empresasComunidadHeader').remove();
            $('.wrapperEmpresasComunidad').remove();
            $('.wrapperEmpleadosEmpresaComunidad').remove();
            return;
        }

        $('.enlaceCae').removeClass('text-success');
        $('.enlaceCae').removeClass('text-danger');

        if(caeContratado === false)
        {
            $('.enlaceCae').attr('href','#');
            $('.enlaceCae').removeClass('enlaceCae').addClass('enlaceKOCae');
            $('.enlaceKOCae').addClass('text-danger');
        }else{
            $('.enlaceCae').addClass('text-success');
        }

        $('.enlaceRGPD').removeClass('text-success');
        $('.enlaceRGPD').removeClass('text-danger');
        if(rgpdContratado === false)
        {
            $('.enlaceRGPD').attr('href','#');
            $('.enlaceRGPD').removeClass('enlaceRGPD').addClass('enlaceKORGPD');
            $('.enlaceKORGPD').addClass('text-danger');
        }else{
            $('.enlaceRGPD').addClass('text-success');
        }

    },

    Import: {

        /**
         * TODO: Valida que el fichero que se está intentando cargar tenga los campos como en la plantilla
         */
        validacionPlantillaComunidades: function()
        {
            return true;
        },

        guardarComunidadDesdePlantilla: async function(jsonExcel)
        {

            var idAdministrador = $('#administradorCargaId option:selected').val();
            var porcentaje = 0;
            for(x = 0; x < jsonExcel.length; x++)
            {

                var Comunidad = {};
                    Comunidad.comunidadservicioscontratados = [];

                var infoServicio = new Object();

                Comunidad.codigo = jsonExcel[x].codigo;
                Comunidad.cif = jsonExcel[x].cif;
                Comunidad.codpostal = jsonExcel[x].codpostal;
                Comunidad.direccion = jsonExcel[x].direccion;
                Comunidad.nombre = jsonExcel[x].nombre;
                Comunidad.localidad = jsonExcel[x].localidad;
                Comunidad.provincia = jsonExcel[x].provincia;
                Comunidad.presidente = jsonExcel[x].presidente;
                Comunidad.telefono = jsonExcel[x].telefono;
                Comunidad.emailcontacto = (jsonExcel[x].emailcontacto === undefined ? '' : jsonExcel[x].emailcontacto );
                Comunidad.ibancomunidad = jsonExcel[x].ibancomunidad;
                Comunidad.usercreate = idAdministrador;
                Comunidad.usuarioId = idAdministrador;
                Comunidad.importacion = 1;
                Comunidad.estado = 'A';

                //  Servicios contratados
                /* Orden de los servicios: 

                    1: CAE
                    2: RGPD
                    3: DOC CAE
                    4: Instalaciones
                    5: Certificados digitales

                */
                // Comunidad.comunidadservicioscontratados[0].idcomunidad = jsonExcel[x].localidad;
                //  CAE
                infoServicio.idservicio = 1;
                infoServicio.contratado = (jsonExcel[x]['cae.contratado'] === undefined ? 0 : jsonExcel[x]['cae.contratado'] );

                infoServicio.precio = 0;
                if(jsonExcel[x]['cae.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['cae.coste'];

                if(jsonExcel[x]['cae.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['cae.pvp'];


                infoServicio.preciocomunidad = (jsonExcel[x]['cae.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['cae.preciocomunidad']);
                infoServicio.mesfacturacion = (jsonExcel[x]['cae.mesfacturacion'] === undefined ? 0 : jsonExcel[x]['cae.mesfacturacion']);

                Comunidad.comunidadservicioscontratados.push(infoServicio);

                //  DPD
                var infoServicio = new Object();
                infoServicio.idservicio = 2;
                infoServicio.contratado = (jsonExcel[x]['Rgpd.contratado'] === undefined ? 0 :jsonExcel[x]['Rgpd.contratado'] );

                infoServicio.precio = 0;
                if(jsonExcel[x]['Rgpd.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['Rgpd.coste'];

                if(jsonExcel[x]['Rgpd.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['Rgpd.pvp'];

                infoServicio.preciocomunidad = ( jsonExcel[x]['Rgpd.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['Rgpd.preciocomunidad'] );
                infoServicio.mesfacturacion = (jsonExcel[x]['Rgpd.mesfacturacion'] === undefined ? 0 : jsonExcel[x]['Rgpd.mesfacturacion']);                
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                //  DOCCAE
                var infoServicio = new Object();
                infoServicio.idservicio = 3;

                //  Compatibilidad con la versión antigua de la plantilla
                //  DOCCAE/PRL Contratado
                infoServicio.contratado = 0;
                if(jsonExcel[x]['doccae.contratado'] !== undefined)
                    infoServicio.contratado = jsonExcel[x]['doccae.contratado'];

                if(jsonExcel[x]['prl.contratado'] !== undefined)
                    infoServicio.contratado = jsonExcel[x]['prl.contratado'];
                
                //  DOCCAE/PRL Precio de coste (al que vende FT)
                infoServicio.precio = 0;
                if(jsonExcel[x]['doccae.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['doccae.coste'];

                if(jsonExcel[x]['doccae.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['doccae.pvp'];                

                if(jsonExcel[x]['prl.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['prl.coste'];

                if(jsonExcel[x]['prl.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['prl.pvp'];  

                //  DOCCAE/PRL -> Precio al que se vende a la comunidad
                infoServicio.preciocomunidad = 0;

                if(jsonExcel[x]['prl.preciocomunidad'] !== undefined)
                    infoServicio.preciocomunidad = jsonExcel[x]['prl.preciocomunidad'];

                if(jsonExcel[x]['doccae.preciocomunidad'] !== undefined)
                    infoServicio.preciocomunidad = jsonExcel[x]['doccae.preciocomunidad'];

                //  DOCCAE/PRL -> Mes de facturación
                infoServicio.mesfacturacion = 0;

                if(jsonExcel[x]['prl.mesfacturacion'] !== undefined)
                    infoServicio.mesfacturacion = jsonExcel[x]['prl.mesfacturacion'];                

                if(jsonExcel[x]['doccae.mesfacturacion'] !== undefined)
                    infoServicio.mesfacturacion = jsonExcel[x]['doccae.mesfacturacion'];

                Comunidad.comunidadservicioscontratados.push(infoServicio);

                //  INSTALACIONES
                var infoServicio = new Object();
                infoServicio.idservicio = 4;
                infoServicio.contratado = (jsonExcel[x]['instalaciones.contratado'] === undefined ? 0 : jsonExcel[x]['instalaciones.contratado']);

                infoServicio.precio = 0;
                if(jsonExcel[x]['instalaciones.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['instalaciones.coste'];

                if(jsonExcel[x]['instalaciones.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['instalaciones.pvp'];  

                infoServicio.preciocomunidad = (jsonExcel[x]['instalaciones.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['instalaciones.preciocomunidad']);
                infoServicio.mesfacturacion = (jsonExcel[x]['instalaciones.mesfacturacion'] === undefined ? 0 : jsonExcel[x]['instalaciones.mesfacturacion']);                
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                //  CERTIFICADOS DIGITALES
                var infoServicio = new Object();
                infoServicio.idservicio = 5;
                infoServicio.contratado = ( jsonExcel[x]['certificadosdigitales.contratado'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.contratado'] );

                infoServicio.precio = 0;
                if(jsonExcel[x]['certificadosdigitales.coste'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['certificadosdigitales.coste'];

                if(jsonExcel[x]['certificadosdigitales.pvp'] !== undefined)
                    infoServicio.precio = jsonExcel[x]['certificadosdigitales.pvp'];  

                infoServicio.preciocomunidad = ( jsonExcel[x]['certificadosdigitales.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.preciocomunidad'] ); 
                infoServicio.mesfacturacion = (jsonExcel[x]['certificadosdigitales.mesfacturacion'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.mesfacturacion']);                
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                console.log(Comunidad);

                //  Pintamos el nombre de la comunidad para que el usuario pueda ir siguiendo el progreso
                $('.importacionComunidad').html(`${Comunidad.codigo} - ${Comunidad.nombre}`);

                //  Generamos la comunidad en base de datos
                await core.Modelo.Insert('comunidad', Comunidad, false, '', false, false);

                porcentaje = (( x * 100 ) / jsonExcel.length).toFixed(2);

                $('.wrapperProgresoCarga .progress-bar-striped').attr('aria-valuenow',`${porcentaje}%`);
                $('.wrapperProgresoCarga .progress-bar-striped').css('width',`${porcentaje}%`);
                $('.wrapperProgresoCarga .progress-bar-striped').html(`${porcentaje}%`);  

                $('.wrapperProgresoCarga .progresoCarga').html(`(${x} de ` + (jsonExcel.length) + ')');
                // await sleep(500);
            }

            
            // FIXME: Arreglar el texto para plural y singular
                $('.wrapperProgresoCarga .progresoCarga').html(jsonExcel.length + ' comunidad(es) importada(s)');
                $('.importacionComunidad').hide();
                $('.btnCerrarProceso').show();

                $('.wrapperProgresoCarga .progress-bar-striped').attr('aria-valuenow',`100%`);
                $('.wrapperProgresoCarga .progress-bar-striped').css('width',`100%`);
                $('.wrapperProgresoCarga .progress-bar-striped').html(`100%`);                  
        },
    
        leerPlantillaComunidades: function(xlsxflag)
        {

            /*Checks whether the browser supports HTML5*/  
            if (typeof (FileReader) != "undefined") 
            {  
    
                var readerExcel = new FileReader();  
    
                readerExcel.onload = function (e) {  
    
                    $('.wrapperProgresoCarga').show();

                    var data = e.target.result;  
                    /*Converts the excel data in to object*/  
                    if (xlsxflag) {  
                        var workbook = XLSX.read(data, { type: 'binary' });  
                    }  
                    else {  
                        var workbook = XLS.read(data, { type: 'binary' });  
                    }  
                    /*Gets all the sheetnames of excel in to a variable*/  
                    var sheet_name_list = workbook.SheetNames;  
    
                    var cnt = 0; /*This is used for restricting the script to consider only first sheet of excel*/  
                    sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/  
                        /*Convert the cell value to Json*/  
                        if (xlsxflag) {  
                            var exceljson = XLSX.utils.sheet_to_json(workbook.Sheets[y]);  
                        }  
                        else {  
                            var exceljson = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y]);  
                        }  

                        $('.wrapperProgresoCarga .progress-bar').css('width','0%');
                        $('.wrapperProgresoCarga .progress-bar-striped').html('0%');

                        if (exceljson.length > 0 && cnt == 0) 
                        {  
                            $('.wrapperProgresoCarga .progresoCarga').html(`(0 de ` + (exceljson.length) + ')');
                            comunidadesCore.Import.guardarComunidadDesdePlantilla(exceljson);
                        }  
                    });
                }  
    
                if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/  
                    readerExcel.readAsArrayBuffer($("#ficheroAdjuntarExcel")[0].files[0]);  
                }  
                else {  
                    readerExcel.readAsBinaryString($("#ficheroAdjuntarExcel")[0].files[0]);  
                }  
            }else {  
                alert("Sorry! Your browser does not support HTML5!");  
            }  
        },
    
        /**
         * Importa comunidades desde un fichero excel
         */
        importarComunidades: function()
        {
            //  Comprobamos que el fichero sea un fichero de Excel
                if(!core.Files.isExcelFile('ficheroAdjuntarExcel'))
                {
                    $('.wrapperMensajeErrorCarga .mensaje').html('El fichero no es válido. Seleccione un fichero de formato Excel (xls o xlsx)');
                    $('.wrapperMensajeErrorCarga').show();
                    return;
                }else{
                    $('.wrapperMensajeErrorCarga').hide();
                }
    
            //  Validamos que haya seleccionado un administrador
                if( $('#administradorCargaId option:selected').val() == '-1' )
                {
                    $('.wrapperMensajeErrorCarga .mensaje').html('Debe seleccionar el administrador al que asignar las comunidades a importar');
                    $('.wrapperMensajeErrorCarga').show();
                    return;
                }

            //  Si es un fichero de Excel debemos validar los campos que trae el fichero
            //  si no tiene el formato de la plantilla entonces avisamos al usuario que ese fichero no es válido, que se descargue la plantilla
            //  y volvemos a mostrar el modal de carga automática de comunidades solo si es sudo
                if(!comunidadesCore.Import.validacionPlantillaComunidades())
                {
                    $('.wrapperMensajeErrorCarga .mensaje').html('El fichero no es válido. Utilice la plantilla de Fincatech.');
                    $('.wrapperMensajeErrorCarga').show();
                    return;                
                }
    
                var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/  
                if ($("#ficheroAdjuntarExcel").val().toLowerCase().indexOf(".xlsx") > 0) {  
                    xlsxflag = true;  
                } 
    
            //  Si el fichero es correcto, ocultamos los wrappers de información y mostramos el de procesando
                $('.wrapperInformacion').hide();
                $('.wrapperSelectorAdministrador').hide();
                $('.wrapperSelectorFichero').hide();
                $('.wrapperMensajeErrorCarga').hide();
    
            //  Cargamos el nombre del administrador en el modal
                $('.importacionAdministrador').html( $('#administradorCargaId option:selected').text() );

            //  Intentamos hacer la importación
                comunidadesCore.Import.leerPlantillaComunidades( xlsxflag );
    
        }

    },

    Render:{

        pagina: 0,
        nResultados: 15,
        inicio: 0,
        final: 15,
        totalResultados: 0,
        numeroPaginas: 0,

        paginacion:
        {

            init: function(){

                $('body').on(core.helper.clickEventType, '.btnPage', function(){
                    comunidadesCore.Render.pagina = $(this).attr('data-page');
                    comunidadesCore.Render.inicio = parseInt(comunidadesCore.Render.pagina-1) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.final = comunidadesCore.Render.nResultados;// parseInt(comunidadesCore.Render.pagina) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.tablaServiciosContratadosComunidades();
                });

                $('body').on(core.helper.clickEventType, '.btnAnterior', function(){
                    if(comunidadesCore.Render.pagina - 1 == 0 )
                    {
                        return;
                    }
                    comunidadesCore.Render.pagina--;
                    comunidadesCore.Render.inicio = parseInt(comunidadesCore.Render.pagina-1) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.final = comunidadesCore.Render.nResultados;// parseInt(comunidadesCore.Render.pagina) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.tablaServiciosContratadosComunidades();
                });

                $('body').on(core.helper.clickEventType, '.btnSiguiente', function(){
                    if((comunidadesCore.Render.pagina + 1) > comunidadesCore.Render.numeroPaginas )
                    {
                        return;
                    }
                    comunidadesCore.Render.pagina++;
                    comunidadesCore.Render.inicio = parseInt(comunidadesCore.Render.pagina-1) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.final = comunidadesCore.Render.nResultados;// parseInt(comunidadesCore.Render.pagina) * parseInt(comunidadesCore.Render.nResultados);
                    comunidadesCore.Render.tablaServiciosContratadosComunidades();
                });                

                $('body').on('keyup', '#search', function(){
                    comunidadesCore.Render.tablaServiciosContratadosComunidades();
                });

            },

            construct: function(){

                //  Calculamos el total de páginas
                var numeroPaginas = comunidadesCore.Render.numeroPaginas;
                var paginaActual = comunidadesCore.Render.pagina;
                var paginas = '';
                var botonAnterior = `
                <li class="page-item lnkAnterior">
                    <a class="page-link btnAnterior" href="javascript:void(0);">Anterior</a>
                </li>`;

                var botonSiguiente = `
                <li class="page-item lnkSiguiente">
                    <a class="page-link btnSiguiente" href="javascript:void(0);">Siguiente</a>
                </li>`;

                $('.pagination').html('');

                //  Siempre fija la página 1
                    if(paginaActual>0)
                        paginas = `
                            <li class="page-item">
                                <a class="page-link btnPage" data-page="1" href="javascript:void(0);">1</a>
                            </li>   
                            <li class="page-item disabled">
                                <a class="page-link btnPage" data-page="1" href="javascript:void(0);">...</a>
                            </li>                                                    
                            `;

                //  Construimos la paginación intermedia
                var paginasIntermedias = '';

                    for(var iPagina = 1; iPagina < 7; iPagina++)
                    {
                        if(iPagina==4)
                        {
                            paginasIntermedias += `
                            <li class="page-item disabled">
                                <a class="page-link btnPage bg-primary text-white shadow-neumorphic" href="javascript:void(0);">${(paginaActual == 0 ? 1: paginaActual)}</a>
                            </li>`;  
                        }
                        var nPag = parseInt(paginaActual)+parseInt(iPagina);
                        if(nPag < numeroPaginas && nPag != numeroPaginas)
                            paginasIntermedias += `
                                <li class="page-item">
                                    <a class="page-link btnPage" data-page="${nPag}" href="javascript:void(0);">${nPag}</a>
                                </li>`;  
                    }

                //  Siempre fija la última página
                paginas += `
                ${paginasIntermedias}
                    <li class="page-item disabled">
                        <a class="page-link btnPage" data-page="1" href="javascript:void(0);">...</a>
                    </li>                
                    <li class="page-item">
                        <a class="page-link btnPage" data-page="${numeroPaginas}" href="javascript:void(0);">${numeroPaginas}</a>
                    </li>`;                

                //  Máx
                var menPaginacion = `${botonAnterior} ${paginas}  ${botonSiguiente}`; 
                $('.pagination').html(menPaginacion);

            },

            anterior: function(){

            },

            siguiente: function()
            {

            }

        },

        tablaListadoComunidadesInfoProgreso: function(){

            if($('#listadoComunidad').length)
            {
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
    
                //  Columna con información adicional de estado de documentación
                    // CoreUI.tableData.addColumnRow('listadoComunidad', 'documentacioncomunidad');
    
                //  Código
                    CoreUI.tableData.addColumn('listadoComunidad', "codigo","COD", null, null, '40px');
                //  Nombre
                    CoreUI.tableData.addColumn('listadoComunidad', "nombre", "NOMBRE", null, null, '40%');
    
                if(core.Security.getRole() == 'SUDO')
                {

                //  Administrador
                    CoreUI.tableData.addColumn('listadoComunidad', "administrador", "Administrador");            
    
                //  Email
                    // var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                    // CoreUI.tableData.addColumn('listadoComunidad', null, "EMAIL", html);
    
                //  Teléfono
                    CoreUI.tableData.addColumn('listadoComunidad', "telefono", "TELEFONO");
    
                }
    
                //  Cumplimiento CAE
                    CoreUI.tableData.addColumn('listadoComunidad',function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        if(row.cumplimientocae == null)
                        {
                            htmlSalida = '<p class="mb-0">No contratado</p>';
                        }else{
                            if(row.cumplimientocae == '100')
                            {
                                bgcolor = 'success';
                            }else{
                                bgcolor = 'warning';
                            }
                            htmlSalida = `
                            <div class="mr-3 ml-3 progress shadow-inset" style="background-color: white !important;">
                                <div class="progress-bar bg-${bgcolor}" role="progressbar" style="border:0 !important; width: ${row.cumplimientocae}%;" aria-valuenow="${row.cumplimientocae}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div> 
                            <p class="m-0 text-center"><small>${row.cumplimientocae}%</small></p>                   
                            `;
                        }

                        return htmlSalida;

                    }, "CUMP. CAE", null, 'text-center');
    
                //  Cumplimiento RGPD
                    CoreUI.tableData.addColumn('listadoComunidad',function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        if(row.cumplimientorgpd == null)
                        {
                            htmlSalida = '<p class="mb-0">No contratado</p>';
                        }else{
                            if(row.cumplimientorgpd == '100')
                            {
                                bgcolor = 'success';
                            }else{
                                bgcolor = 'warning';
                            }
                            htmlSalida = `
                            <div class="mr-3 ml-3 progress shadow-inset" style="background-color: white !important;">
                                <div class="progress-bar bg-${bgcolor}" role="progressbar" style="border:0 !important; width: ${row.cumplimientorgpd}%;" aria-valuenow="${row.cumplimientorgpd}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div> 
                            <p class="m-0 text-center"><small>${row.cumplimientorgpd}%</small></p>                   
                            `;
                        }
    
                        return htmlSalida;
    
                    }, "CUMP. RGPD", null, 'text-center');
    
                // Estado
                    var html = 'data:estado$';
                    CoreUI.tableData.addColumn('listadoComunidad', 'estado', "Estado", html, null, '80px');
    
                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                        html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$?view=1" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoComunidad', null, "", html);
    
                // $('#listadoComunidad').addClass('no-clicable');
                CoreUI.tableData.render("listadoComunidad", "Comunidad", "comunidad/list");
            }
        },

        /** Renderiza el listado de comunidades para dashboard sudo */
        tablaListadoComunidadesPendientes: function()
        {
            if($('#listadoComunidadesPendientes').length)
            {
                let endpointListado = "comunidad/list?status=P";

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();

                //  Código
                    CoreUI.tableData.addColumn('listadoComunidadesPendientes', "codigo","COD", null, null, '40px');

                //  Nombre
                    CoreUI.tableData.addColumn('listadoComunidadesPendientes', "nombre", "NOMBRE", null, null, '40%');

                //  Administrador
                    var html = 'data:usuario.nombre$';
                    CoreUI.tableData.addColumn('listadoComunidadesPendientes', 'administrador', "Administrador", html);

                //  Fecha de alta
                    var html = 'data:created$';
                    CoreUI.tableData.addColumn('listadoComunidadesPendientes', 'created', "Fecha alta", html);

                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$?view=1" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid pr-2" style="width:32px;height:32px;"></i></a></li>`;
                        // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid" style="width:26px;height:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoComunidadesPendientes', null, "", html);

                CoreUI.tableData.render("listadoComunidadesPendientes", "Comunidad", endpointListado, false, true, false, null, false, false, null, false);
            }
        },

        /** Renderiza el listado de comunidades para usuarios que no sean admin de fincas */
        tablaListadoComunidades: function(estado = '')
        {
            if($('#listadoComunidad').length)
            {
                let endpointListado = "comunidad/list" + (estado !== '' ? '?status='+estado : '');

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();

                //  Código
                    CoreUI.tableData.addColumn('listadoComunidad', "codigo","COD", null, null, '40px', null);
                //  Nombre
                    CoreUI.tableData.addColumn('listadoComunidad', "nombre", "NOMBRE", null, null, '40%');

                //  CIF Solo para sudo
                    if(core.Security.getRole() == 'SUDO'){
                        CoreUI.tableData.addColumn('listadoComunidad', "cif", "CIF/NIF", null, null, '10%');
                    }

                    console.log('Role: ', core.Security.getRole());

                //  Administrador
                    var html = 'data:usuario.nombre$';
                    CoreUI.tableData.addColumn('listadoComunidad', 'administrador', "Administrador", html);

                //  Fecha de alta
                    var html = 'data:created$';
                    CoreUI.tableData.addColumn('listadoComunidad', 'created', "Fecha alta", html);

                // Estado
                    var html = 'data:estado$';
                    CoreUI.tableData.addColumn('listadoComunidad', 'estado', "Estado", html, null, '80px');

                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$?view=1" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoComunidad', null, "", html);

                CoreUI.tableData.render("listadoComunidad", "Comunidad", endpointListado, false, true, true, null, false, false, null, true);
            }
        },

        /** Genera la tabla de servicios contratados por todas las comunidades */
        tablaServiciosContratadosComunidades: async function(){

            if( $('#listadoServiciosContratadosComunidades').length){

                $('#listadoServiciosContratadosComunidades thead').html('');
                $('#listadoServiciosContratadosComunidades tbody').html('');

                //  Cabecera
                var header = `
                <tr class="align-middle text-center font-weight-light">
                    <th class="font-weight-light">Código</th>
                    <th class="font-weight-light" style="min-width:300px;">Comunidad</th>
                    <th class="font-weight-light">Administrador</th>
                    <th class="pl-3 pr-3">CAE</th>
                        <th class="font-weight-light pl-3 pr-3">Mes Facturación</th>
                        <th class="font-weight-light pl-3 pr-3">Precio</th>
                        <th class="font-weight-light pl-3 pr-3">Precio comunidad</th>
                    <th class="pl-3 pr-3">RGPD</th>
                        <th class="font-weight-light pl-3 pr-3">Mes Facturación</th>
                        <th class="font-weight-light pl-3 pr-3">Precio</th>
                        <th class="font-weight-light pl-3 pr-3">Precio comunidad</th>
                    <th class="pl-3 pr-3">Certificados digitales</th>
                        <th class="font-weight-light pl-3 pr-3">Mes Facturación</th>
                        <th class="font-weight-light pl-3 pr-3">Precio</th>
                        <th class="font-weight-light pl-3 pr-3">Precio comunidad</th>
                    <th class="pl-3 pr-3">PRL</th>
                        <th class="font-weight-light pl-3 pr-3">Mes Facturación</th>
                        <th class="font-weight-light pl-3 pr-3">Precio</th>
                        <th class="font-weight-light pl-3 pr-3">Precio comunidad</th>
                    <th class="pl-3 pr-3">Instalaciones</th>
                        <th class="font-weight-light pl-3 pr-3">Mes Facturación</th>                    
                        <th class="font-weight-light pl-3 pr-3">Precio</th>
                        <th class="font-weight-light pl-3 pr-3">Precio comunidad</th>
                </tr>`;

                $('#listadoServiciosContratadosComunidades thead').html(header);

                var queryEndpoint = 'comunidad/servicioscontratados/list?start=' + comunidadesCore.Render.inicio + '&length=' + comunidadesCore.Render.final + '&search=' + $('#search').val();

                apiFincatech.get( queryEndpoint ).then( (result) => {

                    var data = JSON.parse(result);
                        data = data.data;

                    comunidadesCore.Render.totalResultados = data['total'];
                    comunidadesCore.Render.numeroPaginas = parseInt(comunidadesCore.Render.totalResultados / comunidadesCore.Render.nResultados) + 1;

                    for(var iServicio = 0; iServicio < data['Comunidad'].length; iServicio++)
                    {

                        var datos = data['Comunidad'][iServicio];
                        var body = '';

                        var cae_contratado = (datos['cae_contratado'] == true ? ' checked="checked" ' : '');
                        var rgpd_contratado = (datos['rgpd_contratado'] == true ? ' checked="checked" ' : '');
                        var prl_contratado = (datos['prl_contratado'] == true ? ' checked="checked" ' : '');
                        var certificados_contratado = (datos['certificados digitales_contratado'] == true ? ' checked="checked" ' : '');
                        var instalaciones_contratado = (datos['instalaciones_contratado'] == true ? ' checked="checked" ' : '');

                        body = `
                        <tr class="bg-white servicios-comunidad-${datos['id']}" data-idcomunidad="${datos['id']}">
                            <td class="bg-white text-center pl-3 pr-3">
                                <p>${datos['codigo']}</p>
                            </td>
                            <td class="bg-white pl-3 pr-3">
                                <p>${datos['comunidad']}</p>
                            </td>
                            <td class="bg-white pl-3 pr-3">
                                <p>${datos['administrador']}</p>
                            </td>

                        <!-- CAE -->

                            ${serviciosCore.Render.ServiceInfo('cae', datos['cae'], cae_contratado, datos['id'], datos['cae_idtiposervicio'], datos['cae_mesfacturacion'], datos['cae_precio'], datos['cae_preciocomunidad'] )}

                        <!-- RGPD Contratado -->

                            ${serviciosCore.Render.ServiceInfo('rgpd', datos['rgpd'], rgpd_contratado, datos['id'], datos['rgpd_idtiposervicio'], datos['rgpd_mesfacturacion'], datos['rgpd_precio'], datos['rgpd_preciocomunidad'] )}

                        <!-- Certificados digitales -->

                            ${serviciosCore.Render.ServiceInfo('certificados', datos['certificados digitales'], certificados_contratado, datos['id'], datos['certificados digitales_idtiposervicio'], datos['certificados digitales_mesfacturacion'], datos['certificados digitales_precio'], datos['certificados digitales_preciocomunidad'] )}

                        <!-- PRL -->

                            ${serviciosCore.Render.ServiceInfo('prl', datos['prl'], prl_contratado, datos['id'], datos['prl_idtiposervicio'], datos['prl_mesfacturacion'], datos['prl_precio'], datos['prl_preciocomunidad'] )}

                        <!-- Instalaciones -->

                            ${serviciosCore.Render.ServiceInfo('instalaciones', datos['instalaciones'], instalaciones_contratado, datos['id'], datos['instalaciones_idtiposervicio'], datos['instalaciones_mesfacturacion'], datos['instalaciones_precio'], datos['instalaciones_preciocomunidad'] )}

                        `;

                        $('#listadoServiciosContratadosComunidades tbody').append(body);
                        comunidadesCore.Render.paginacion.construct();

                    }

                    $('.servicio-mesfacturacion').each(function(){
                        let valor = $(this).children('option:selected').val();
                        $(this).select2({
                            theme:'bootstrap4'
                        });
                        $(this).val(valor).trigger('change');
                    });

                });

            }

        },

        /** Tabla de empresas concurrentes de una comunidad */
        tablaEmpresasConcurrentes: function(){

                //  Cargamos el listado de comunidades
                CoreUI.tableData.init();

                //  
                    CoreUI.tableData.addColumn('tablaEmpresasConcurrentes', "razonsocial", "Empresa", null, null, '40%');

                //  Código
                    CoreUI.tableData.addColumn('tablaEmpresasConcurrentes', "telefono","Teléfono", null, null, '40px');


                //  Email
                    CoreUI.tableData.addColumn('tablaEmpresasConcurrentes', "email", "E-mail");            
                
                //  Persona de contacto
                    CoreUI.tableData.addColumn('tablaEmpresasConcurrentes', "personacontacto", "Persona de contacto");            

                CoreUI.tableData.render("tablaEmpresasConcurrentes", "empresascomunidad", `comunidad/${core.modelId}/empresas`, false, false, false, null, false, false, null, false);

        },

        tablaDocumentosEmpresaConcurrente: async function(idEmpresa){
            
            if($('#tablaCAEEmpresasConcurrentes').length)
            {
    
                // console.log(window['tabletablaEmpresasConcurrentes'].row(idEmpresa).data());
                    var nombreEmpresa = window['tabletablaEmpresasConcurrentes'].row(idEmpresa).data().razonsocial;
                    $('.empresaConcurrenteNombre').text(nombreEmpresa);
                    var idEmpresa = window['tabletablaEmpresasConcurrentes'].row(idEmpresa).data().idusuario;
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Nombre del documento
                    CoreUI.tableData.addColumn('tablaCAEEmpresasConcurrentes', "requerimiento","Requerimiento", null, 'text-left');
    
                //  Fecha última actuación
                    CoreUI.tableData.addColumn('tablaCAEEmpresasConcurrentes', function(row, type, val, meta)
                    {
                        if(row.fechasubida == '' || row.fechasubida == 'null' || !row.fechasubida)
                        {
                            return 'No se ha realizado ninguna actuación';
                        }else{
                            return '<p class="mb-0 text-center">' + moment(row.fechasubida).locale('es').format('L') + '</p>';
                        }
                    }, "Fecha última actuación", null, 'text-center');
    
                //  Estado
                    CoreUI.tableData.addColumn('tablaCAEEmpresasConcurrentes', function(row, type, val, meta)
                    {
                        var valor;
                        var _idEstado = row.idestado;
    
                        if(!_idEstado)
                        {
                            _idEstado = 1;
                        }else{
                            _idEstado = parseInt(_idEstado);
                        }
    
                        switch (_idEstado)
                        {
                            case 1:
                                valor = 'no adjuntado';
                                break;
                            case 2:
                                valor = 'no descargado';
                                break;
                            case 3:
                                valor = 'no verificado';
                                break;
                            case 4:
                                valor = 'verificado';
                                break;
                            case 5:
                                valor = 'descargado';
                                break;
                            case 6:
                                valor = 'verificado';
                                break;
                            case 7:
                                valor = 'rechazado';
                                break;
                        }
    
                        var salida = CoreUI.Utils.renderLabelByEstado(valor);
                        return salida;
    
                    }, "Estado", null, 'text-center');
    
                //  Columna de acciones
                    CoreUI.tableData.addColumn('tablaCAEEmpresasConcurrentes', function(row, type, val, meta)
                    {
                        var canUploadFile = false;
                        var salida = '';
    
                        if(core.Security.getRole() == 'CONTRATISTA')
                            canUploadFile = true;
        
                    //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                        ficheroAdjuntado = (!row.idficherorequerimiento ? false : true);
    
                        if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                        {
                            var enlaceDescarga = config.baseURL + 'public/storage/' + row.storageficherorequerimiento;
                            salida += ` <td class="text-center">
                                            <a href="${enlaceDescarga}" target="_blank" title="Ver documento">
                                                <i class="bi bi-cloud-arrow-down text-primary mr-1" style="font-size: 30px;"></i>
                                            </a>
                                        </td>`;
                        }
    
                        var _idempresa = (row.idempresa == null ? idEmpresa : row.idempresa);
    
                        if(!ficheroAdjuntado && !canUploadFile)
                        {
                            salida += '<td>&nbsp;</td>';
                        }
                        return salida;
    
                    }, '&nbsp;', null, 'text-center');
    
                    //$('#tablaCAEEmpresasConcurrentes').addClass('no-clicable');
    
                    var idsRequerimiento = new Array(10,11,12);

                    CoreUI.tableData.render("tablaCAEEmpresasConcurrentes", "documentacioncae", `empresa/${idEmpresa}/documentacion`, null, false, false);

                    window['tabletablaCAEEmpresasConcurrentes'].on('draw', function(){
                        $('#tablaCAEEmpresasConcurrentes tbody tr').each( function()
                        {
                            if(typeof window['tabletablaCAEEmpresasConcurrentes'].row( $(this).attr('id') ).data() !== 'undefined')
                            {
                                var idReq = window['tabletablaCAEEmpresasConcurrentes'].row( $(this).attr('id') ).data().idrequerimiento;
                                if(idsRequerimiento.indexOf(parseInt(idReq)) < 0)
                                    $(this).remove();
                            }
                        });                    
                    });
            }  
        },

        tablaProveedores: function()
        {
            if($('#listadoComunidadesProveedores').length)
            {
    
                    //  Cargamos el listado de comunidades
                        CoreUI.tableData.init();
                        CoreUI.tableData.columns = [];
    
                    //  Código Comunidad
                        CoreUI.tableData.addColumn('listadoComunidadesProveedores', "codigocomunidad", 'CÓD', null, 'text-left', '30px');
    
                    //  Comunidad
                        CoreUI.tableData.addColumn('listadoComunidadesProveedores', "comunidad", 'Comunidad', null, 'text-left');
    
                    //  Nombre del proveedor
                        CoreUI.tableData.addColumn('listadoComunidadesProveedores', "empresa", 'Proveedor Asignado', null, 'text-left');
    
                    // //  Estado del requerimiento
                    //     CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', 
                    //         function(row, type, val, meta)
                    //         {
                    //             if(row.idficherorequerimiento == null)
                    //             {
                    //                 return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                    //             }else{
                    //                 //  Comprobamos el estado del requerimiento
                    //                 if(core.Security.getRole() == 'CONTRATISTA')
                    //                 {
                                        
                    //                     //  Comprobamos si el contratista ha descargado el documento previamente
                    //                     if(documentalCore.Helper.ComprobarDescargaRequerimientoPorEmpresa(core.Security.user, row.idficherorequerimiento, row.descargas))
                    //                     {
                    //                         return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Descargado</span>';
                    //                     }else{
                    //                         return '<span class="badge rounded-pill bg-warning pl-3 pr-3 pt-2 pb-2 d-block">Pendiente descarga</span>';
                    //                     }
                    //                 }else{
                    //                     return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Subido</span>';
                    //                 }
                    //             }
                    //         },
                    //     "Estado", null, 'text-center', '10%');
  
                    CoreUI.tableData.render("listadoComunidadesProveedores", "proveedores", 'comunidad/proveedoresasignados/list', false, true, true, null, true, true, 'comunidad', false, 'GET', true, true ).then ( () =>{
                        window['tablelistadoComunidadesProveedores'].table(0).columns(0).visible(false);
                        window['tablelistadoComunidadesProveedores'].table(0).columns(1).visible(false);
                    });
                    $('#listadoComunidadesProveedores').show();
                    // CoreUI.tableData.render("listadoComunidadesProveedores", "proveedores", 'comunidad/proveedoresasignados/list', false, false, false, null, false, false, null, false);
        }
    }

    },
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

document.addEventListener('coreInitialized', function(event) {
    comunidadesCore.init();     
});
   
document.addEventListener('modelLoaded', function(event) {
    if(core.actionModel == 'get' && core.model.toLowerCase() == "comunidad"){
        let titulo = `${core.Modelo.entity['Comunidad'][0]['codigo']} - ${core.Modelo.entity['Comunidad'][0]['nombre']}`;
        CoreUI.Utils.setTituloPantalla(null, null, titulo);
    }    
});