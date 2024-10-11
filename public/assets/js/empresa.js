// const moment = require("moment");

let empresaCore = {

    Empresa: Object(),
    empresa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoEmpresa').length)
        {
            empresaCore.renderTabla();
        }else{
     
        }

        //  Comprobamos si hay que renderizar el listado de empleados por empresa
            empresaCore.renderTablaEmpleados(core.modelId);

        //  Título del módulo
            if($('.titulo-modulo').length && core.model == 'Empresa')
                CoreUI.setTitulo('razonsocial');

    },

    events: function()
    {   

        //  Validación existencia de CIF/NIF Modal de asignación de empresa a comunidad
        $('body').on('blur', '.formEmpresaComunidad #cif', (evt) =>{

            let cifEmpresa = $('body .formEmpresaComunidad #cif').val();
            //  Si se ha introducido el cif se valida
            if(cifEmpresa.trim() !== '')
            {
                empresaCore.existeCIF( cifEmpresa );
            }else{
                empresaCore.Controller.MostrarTablaEmpresasCIF(false);
            }

        });

        //  Descargar email certificado
        $('body').on(core.helper.clickEventType, '.btnDescargarEmailCertificado', (ev)=>{
            ev.stopImmediatePropagation();
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarEmpresa', (evt)=>{
            evt.stopImmediatePropagation();
            empresaCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.bntCrearNuevaEmpresaCAE', function(evt)
        {
            empresaCore.mostrarModalNuevaEmpresaCAE( $('body #searchEmpresa').val() );
        });

        $('body').on(core.helper.clickEventType, '#listadoEmpresaComunidad tr', function(e)
        {
            e.stopImmediatePropagation();
            $('.wrapperEmpresasComunidad').hide();
            $('.wrapperDocumentacionEmpleado').hide();
            
            var idEmpresa = window['tablelistadoEmpresaComunidad'].row( $(this).attr('id')).data().idusuario;
            var nombre = window['tablelistadoEmpresaComunidad'].row( $(this).attr('id')).data().razonsocial;

                $('.tituloEmpresasComunidad').text(nombre);
                $('.empresaDeclaracion').text(nombre);

            //  Cargamos el listado de empleados para la comunidad y empresa seleccionada
                empresaCore.renderTablaRequerimientosEmpresa( idEmpresa ).then( ()=>{
                    $('.wrapperDocumentacionBasica').hide();
                    $('.wrapperEmpleadosEmpresaComunidad').show();
                    empleadoCore.renderTablaEmpleadosEmpresaComunidad(idEmpresa, core.modelId).then (() =>{
                        empresaCore.renderTablaInfoDescargas(idEmpresa, core.modelId);
                    });
                });

                empresaCore.comprobarAceptacionOperatoria(idEmpresa, core.modelId);
        })

        $('body').on(core.helper.clickEventType, '.btnCerrarEmpleadosComunidad', function(e)
        {
            $('.wrapperEmpleadosEmpresaComunidad').hide();
            $('.wrapperEmpresasComunidad').show();
            $('.wrapperDocumentacionBasica').show();
            $('.tituloEmpresasComunidad').text('Empresas externas');

        });

        $('body').on(core.helper.clickEventType, '.enlaceCae', function(e)
        {
            //  Cargamos las empresas asociadas a la comunidad en pantalla
                documentalCore.Comunidad.renderTablaDocumentacionComunidadCAE(core.modelId).then( () =>{
                    empresaCore.renderTablaEmpresasComunidad(core.modelId);
                });                
                $('.tituloEmpresasComunidad').text('Empresas externas');

            //  Operatoria y aceptación de condiciones
                empresaCore.comprobarAceptacionOperatoria(core.Security.user, core.modelId);
        });

        //  Edición de empleado desde contratista listado
        $('body').on(core.helper.clickEventType, '.btnRedirect', function(ev)
        {
            ev.stopImmediatePropagation();
            CoreUI.Utils.redirectTo( $(this).attr('data-url'));
        });

        //  Búsqueda de empresa 
        $('body').on(core.helper.clickEventType, '.btnBuscarEmpresaCAE', async function(ev)
        {

            $('.wrapperBusquedaEmpresa .mensaje').hide();
            $('.wrapperInfoEmpresa').hide();

            //  Validamos que haya proporcionado un e-mail
            if( !core.helper.validarEmail($('#searchEmpresa').val()))
            {
                $('.wrapperBusquedaEmpresa .mensaje').addClass('text-danger');
                $('.wrapperBusquedaEmpresa .mensaje').text('El e-mail proporcionado no es válido');
                $('.wrapperBusquedaEmpresa .mensaje').show();
                return;
            }

            //  Buscamos el e-mail en la tabla de empresas
                await empresaCore.buscarEmailEmpresa( $('#searchEmpresa').val() ).then( (resultado) =>{
                    if( resultado === false )
                    {
                        if( !$('.wrapperBusquedaEmpresa .mensaje').hasClass('text-danger'))
                        {
                            $('.wrapperBusquedaEmpresa .mensaje').addClass('text-danger');
                        }
                        $('.wrapperBusquedaEmpresa .mensaje').text('No existe ninguna empresa asociada al e-mail proporcionado');
                        $('.wrapperBusquedaEmpresa .mensaje').show();
                        $('.bntCrearNuevaEmpresaCAE').show();
                    }else{
                        $('.bntCrearNuevaEmpresaCAE').hide();
    
                        //  Cargamos los datos
                        let infoEmpresa = resultado;
                        // console.log(infoEmpresa);
                        $('.wrapperInfoEmpresa .nombreEmpresa').text(infoEmpresa.razonsocial);
                        $('.wrapperInfoEmpresa .cifEmpresa').text(infoEmpresa.cif);
                        $('.wrapperInfoEmpresa .emailEmpresa').text(infoEmpresa.email);
                        $('.wrapperInfoEmpresa .btnConfirmarEmpresaCAE').attr('data-id', infoEmpresa.id);
                        $('.wrapperInfoEmpresa .btnConfirmarEmpresaCAE').attr('data-nombre', infoEmpresa.razonsocial);
                        //  Mostramos la información
                        $('.wrapperInfoEmpresa').show();
                       
                    }
                });
        });

        //  Asignación de empresa desde el listado de empresas que comparten el mismo CIF
        $('body').on(core.helper.clickEventType, '.btnAsignarEmpresaExistente', function(e){
            //  Lanzamos la asignación de la empresa y guardamos directamente
            comunidadesCore.asignarEmpresa( $(this).attr('data-id') );
        });

        //  Ver actuaciones
        $('body').on(core.helper.clickEventType, '.btnVerActuacionesEmpresa',function(ev){
            ev.stopImmediatePropagation();
            let idEmpresa = $(this).attr('data-idempresa');
            empresaCore.Controller.MostrarModalActuacionesEmpresa(idEmpresa);
        });

        //  Ver incidencia email blacklist
        $('body').on(core.helper.clickEventType, '.btnEmailBlackList', function(ev){
            ev.stopImmediatePropagation();
            let email = $(this).attr('data-email');
            CoreUI.Modal.Error(`El e-mail <strong>${email}</strong> no es válido o no existe.<br>Por favor, verifíquelo.`);
        });

    },

    /**
     * Comprueba si existe ya un cif de un proveedor en el sistema
     * @param {*} cif 
     */
    existeCIF: async function(cif)
    {
        let datos = Object();
        let resultados;
        datos = {
            fields: Array()
        }

        datos.fields.push({
            field: 'cif',
            search: cif,
            type: 'string',
            searchtype: 'eq'
        });

        await apiFincatech.post('empresa/search', datos).then( (response) =>
        {
            var responseData = JSON.parse(response);
            resultados = responseData;
            empresaCore.Model.empresas = resultados.data.Empresa;
            //  Mostramos u ocultamos la tabla de empresas que comparten el mismo CIF
            empresaCore.Controller.MostrarTablaEmpresasCIF(empresaCore.Model.empresas.length > 0);
        });
    },

    Controller:{

        MostrarModalActuacionesEmpresa: function(idEmpresa)
        {
            apiFincatech.get(`empresa/${idEmpresa}/seguimiento`).then((result)=>{
                let res = JSON.parse(result);
                if(result.data == 'error')
                {
                    CoreUI.Modal.Error(result.response.error);
                }else{

                    var actuaciones = res.data.actuaciones;
                    let html = '';
                    for(let i = 0; i < actuaciones.length; i++)
                    {
                        let fecha = moment(actuaciones[i].created).format('DD/MM/YYYY HH:mm');
                        let observaciones = '';
                        let tipo = '';
                        if(!actuaciones[i].subject)
                        {
                            observaciones = actuaciones[i].observaciones;
                            tipo = actuaciones[i].tipo;
                        }else{
                            tipo = 'Envío de E-mail';
                            observaciones = 'Envío de e-mail informando del alta en la plataforma';
                        }
                        
                        html = `${html}<li class="event" data-date="${fecha}"><h3>${tipo}</h3><p>${observaciones}</p></li>`;
                    }

                    if(html != '')
                    {
                        msgHTML = `<div class="row">
                        <div class="col-md-12">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <h6 class="card-title">Seguimiento realizado</h6>
                                    <div id="content">
                                        <ul class="timeline">${html}</ul>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                        <a href="javascript:swal.close();" class="btn btn-primary px-3 py-2">Cerrar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        //  Mostramos el Cuadro de Diálogo
                        CoreUI.Modal.CustomHTML(msgHTML,'',null,'90%');
                    }
                }

            });
        },

        /**
         * Muestra el label de ya existe el cif + la tabla según los datos obtenidos en la consulta
         * @param {*} show 
         */
        MostrarTablaEmpresasCIF: function(show = false)
        {
            //  Ocultamos la tabla
            $('.tabla-empresas-cif').hide();  
                        
            if(!show)
            {
                $('#cifExiste').hide();
              
            }else{
                $('#cifExiste').show();
                //  Cargamos el listado y lo mostramos al usuario
                empresaCore.Controller.CargarEmpresasMismoCIF();
            }
        },

        /**
         * Carga el listado de empresas que comparten el mismo CIF
         */
        CargarEmpresasMismoCIF: function()
        {
            if(empresaCore.Model.empresas.length > 0)
            {
                //  Cargamos los datos
                $('.tabla-empresas-cif-datos').html('');
                for($iEmpresa = 0; $iEmpresa < empresaCore.Model.empresas.length; $iEmpresa++)
                {
                    let empresa = empresaCore.Model.empresas[$iEmpresa];
                    let htmlEmpresa = `
                    <tr data-idempresa="${empresa['id']}" data-idusuario="${empresa['idusuario']}">
                        <td>${empresa['razonsocial']}</td>
                        <td>${empresa['direccion']}</td>
                        <td>${empresa['localidad']}</td>
                        <td>${empresa['telefono']}</td>
                        <td>${empresa['email']}</td>
                        <td><a href="javascript:void(0);" data-id="${empresa['id']}" data-idusuario="${empresa['idusuario']}" class="btn btn-success btnAsignarEmpresaExistente">Seleccionar y Asignar</a></td>
                    </tr>`;
                    $('.tabla-empresas-cif-datos').append(htmlEmpresa);
                }

                //  Mostramos la tabla
                $('.tabla-empresas-cif').show();
            }
        },

    },

    Model:{
        empresas: null,
    },

    /** Busca una empresa por su e-mail */
    buscarEmailEmpresa: async function(emailEmpresa)
    {

        let datos = Object();
        let resultados;
        datos = {
            fields: Array()
        }

        datos.fields.push({
            field: 'email',
            search: emailEmpresa,
            type: 'string',
            searchtype: 'eq'
        });

        await apiFincatech.post('empresa/search', datos).then( (response) =>
        {
            var responseData = JSON.parse(response);
            resultados = responseData;
        });

        return resultados.data.Empresa.length >= 1 ? resultados.data.Empresa[0] : false;

    },

    /**
     * Comprueba si la empresa ha enviado el documento de aceptación de opratoria con la comunidad
     * @param {int} idEmpresa ID de la empresa
     * @param {int} idComunidad ID de la comunidad
     */
    comprobarAceptacionOperatoria: function(_idEmpresa, _idComunidad)
    {
        //  Validamos que esté la tabla de operatoria
        if( $('.tablaOperatoriaCondiciones').length < 1)
        {
            return;
        }

        apiFincatech.get(`empresa/${_idEmpresa}/comunidad/${_idComunidad}/operatoria`).then( result => {
            
            var datos = JSON.parse(result);
            
            $('.fechaSubida').html('');
            $('.tablaOperatoriaCondiciones .estado').html('');

            if(!datos.data)
            {

            }else{
                datos = datos.data;
                $('.btnAdjuntarOperatoria').attr('data-idrelacionrequerimiento', datos.id);
                $('.tablaOperatoriaCondiciones .estado').html( CoreUI.Utils.renderLabelByEstado(datos.estado) );
                if(datos.estado == 'Verificado')
                {
                    var fechaSubida = moment(datos.created).locale('es').format('L');
                    var htmlSubida = `
                        <p class="mb-0 text-center"><a href="${baseURL}public/storage/${datos.nombrestorage}" class="text-success" download="${datos.nombre}"><i class="bi bi-file-earmark-arrow-down" style="font-size: 24px;"></i></a> </p>Subido el ${fechaSubida}`
                    $('.fechaSubida').html(htmlSubida);
                    
                }else{

                    var htmlSubida = `<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block" style="font-size: 12px; font-weight: 300;">Pendiente de adjuntar</span>`;
                    $('.fechaSubida').html(htmlSubida);
                }
            }
        });
    },

    /**
     * Muestra el modal de crear nueva empresa desde el CAE
     */
    mostrarModalNuevaEmpresaCAE: async function(_emailEmpresa = null)
    {

        apiFincatech.getView("empresa", "form").then((resultHTML)=>{

                Swal.fire({
                    text: "",
                    html: resultHTML,
                    grow:'false',
                    width: '120rem',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="bi bi-save mr-2"></i> Guardar',
                    cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        actions: 'text-center p-3 shadow-inset rounded-pill border-light border-2 bg-white',
                        confirmButton: 'btn btnSaveData btn-success shadow d-block pb-2 pt-2 confirmButtonModal',
                        cancelButton: ' btn btn-danger btnCancelSave shadow d-block pb-2 pt-2 mr-3 cancelButtonModal',
                        popup: 'bg-transparent'
                    },                    
                    didOpen: function(e)
                    {
                        //  Email buscado
                        $('.formEmpresaComunidad #email').val(_emailEmpresa);
                        //  Cargamos los combos 
                            core.Forms.getSelectData( 'Empresatipo', 'Empresatipo.nombre', 'Empresatipo.id', '', '', 'tipoEmpresaComunidad', true ).then(()=>{
                                $('body .formEmpresaComunidad #tipoEmpresaComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                                //  Seleccionamos Empresa por defecto
                                $('body .formEmpresaComunidad #tipoEmpresaComunidad').val('1');
                                $('body .formEmpresaComunidad #tipoEmpresaComunidad').trigger('change');
                            });  

                            core.Forms.getSelectData( 'Provincia', 'Provincia.Nombre', 'Provincia.Id', '', '', 'provinciaEmpresaComunidad', true ).then(()=>{
                                $('body .formEmpresaComunidad #provinciaEmpresaComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                            }); 

                            $('.swal2-container .btnSaveData').removeClass('btnSaveData');


                    },
                    preConfirm: function(e)
                    {
                        if(core.Forms.Validate('formEmpresaComunidad'))
                        {
                            return true;
                        }else{
                            //  Mostramos el mensaje de error correspondiente
                            Swal.showValidationMessage('Debe completar todos los campos marcados como obligatorios');
                            return false;
                        }               
                    }                 
                  }).then((result) => {
                    if (result.isConfirmed) 
                    {
                            core.Forms.mapFormDataToSave('formEmpresaComunidad');
                            core.Forms.data['fromcae'] = '1';
                            core.Forms.data['comunidad'] = Object();
                            core.Forms.data['comunidad']['nombre'] = core.Modelo.entity.Comunidad[0].nombre;
                            core.Forms.data['comunidad']['id'] = core.Modelo.entity.Comunidad[0].id;
                            
                            core.Modelo.Insert('empresa', core.Forms.data, false, 'El registro se ha creado correctamente. Hemos remitido un e-mail al proveedor para que acceda y aporte los documentos requeridos.').then( (result)=>{
                                //  Tenemos que asignar la empresa a la comunidad
                                apiFincatech.post(`comunidad/${core.modelId}/empresa/${core.Modelo.insertedId}/asignar`, null).then(async ( response ) =>
                                {
                                    var responseData = JSON.parse(response);
                
                                    if(responseData.status['response'] == "ok")
                                    {                
                                        //  Recargamos la tabla de empresas para reflejar el cambio
                                            window['tablelistadoEmpresaComunidad'].ajax.reload();
                                            
                                    }else{
                                        //  TODO: Ver cuál es el error en el json
                                        Modal.Error("No se ha podido asignar por el siguiente motivo:<br><br>" + responseData.status.response);
                
                                    }
                                });                                
                                
                                //  Recargamos el listado de empresas de la comunidad
                                    window['tablelistadoEmpresaComunidad'].ajax.reload();
                                    $('.loading').hide();
                            });
                    }
                }); 
            }); 
    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("empresa", id, nombre, "listadoEmpresa");
        Swal.fire({
            title:`¿Desea eliminar la empresa:<br>${nombre}?`,
            text: "Se va a eliminar la empresa y toda la información asociada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                //  Llamamos al endpoint de eliminar
                apiFincatech.delete("empresa", id).then((result) =>{
                    Swal.fire(
                        'Empresas eliminada correctamente',
                        '',
                        'success'
                      );
                      $('#listadoEmpresa').DataTable().ajax.reload();
                });
            }
        });
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
    
        if($('#listadoEmpresa').length)
        {
            CoreUI.tableData.init();

            CoreUI.tableData.addColumnRow('listadoEmpresa', 'comunidades');

            //  Razón social
            CoreUI.tableData.addColumn('listadoEmpresa', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpresa', "cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresa', "email", "EMAIL", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresa', "telefono", "TELEFONO", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresa', "personacontacto", "Persona de contacto", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn('listadoEmpresa', "localidad", "Localidad", null, 'text-left');

            //  Tipo
            CoreUI.tableData.addColumn('listadoEmpresa', "empresatipo[0].nombre", "Tipo", null, 'text-left');

            //  Fecha de creación
            // CoreUI.tableData.addColumn('listadoEmpresa', 
            // function(row, type, val, meta)
            // {
            //     var timeStamp;
            //     var fechaCreacion;

            //     if(!row.usuario[0].lastlogin)
            //     {
            //         timeStamp = '';
            //         fechaCreacion = '<span class="badge badge-pill bg-danger text-white">Nunca</span>';
            //     }else{
            //         timeStamp = moment(row.usuario[0].lastlogin, 'YYYY-MM-DD hh:mm').unix();
            //         fechaCreacion = moment(row.usuario[0].lastlogin).locale('es').format('L');
            //     }

            //     return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
            // },
            // "Último acceso", null, 'text-center');

            //  Último acceso
            CoreUI.tableData.addColumn('listadoEmpresa', 
            function(row, type, val, meta)
            {
                var timeStamp;
                var fechaCreacion;

                if(!row.lastlogin)
                {
                    //  Comprobamos si tiene ID de mensaje ya enviado
                    btnReenvio = '';
                    if(row.idmensajeregistro != '-1' && typeof(row.idmensajeregistro) !== 'undefined')
                    {
                        btnReenvio = `<a href="javascript:void(0);" class="btnReenviarMensaje d-inline-block btn btn-primary ml-2 pt-0 pb-0" title="Reenviar mensaje de registro" data-id="${row.idmensajeregistro}"><i data-feather="send" style="width:12px;height:12px;"></i></a>`;
                    }
                    timeStamp = '';
                    fechaCreacion = `<span class="badge badge-pill bg-danger text-white pt-2 pl-3 pr-3 pb-2">Nunca</span>${btnReenvio}`;
                }else{
                    timeStamp = moment(row.lastlogin, 'YYYY-MM-DD hh:mm').unix();
                    fechaCreacion = moment(row.lastlogin).locale('es').format('L');
                }

                return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
                
            },
            "Último acceso", null, 'text-left');   

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}empresa/data:id$" class="btnEditarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="edit" class="text-success img-fluid icono-accion"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoEmpresa', null, "", html);

                $('#listadoEmpresa').addClass('no-clicable');
                // CoreUI.tableData.render("listadoEmpresa", "Empresa", "empresa/list", false, true, true, null, false, false, null, true);
                CoreUI.tableData.render('listadoEmpresa', 'Empresa', 'empresa/list', false, true, true,null,null,false,null,true);
        }
    },

    renderTablaSimple: async function()
    {
        if($('#listadoSimpleEmpresas').length)
        {
            if(typeof window['tablelistadoSimpleEmpresas'] != 'undefined')
            {
                // window['tablelistadoSimpleEmpresas'].destroy();
                CoreUI.tableData.columns['listadoSimpleEmpresas'] = [];

            }
            CoreUI.tableData.init();


            //  Razón social
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "email", "EMAIL", null, 'text-left');

            //  Tipo de empresa
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "empresatipo[0].nombre", "Tipo", null, 'text-left');

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item">
                                <a href="javascript:void(0);" class="btnConfirmarEmpresaCAE d-inline-block btn btn-sm btn-success" data-id="data:id$" data-nombre="data:razonsocial$">ASIGNAR</a>
                            </li>
                            </ul>`;
                CoreUI.tableData.addColumn('listadoSimpleEmpresas', null, "", html);

                $('#listadoSimpleEmpresas').addClass('no-clicable');
                CoreUI.tableData.render("listadoSimpleEmpresas", "Empresa", "empresa/list");
        }  
    },

    renderTablaEmpleados: async function(idempresa, tabla = 'listadoEmpleadosEmpresa')
    {
    
        var tablaDestino = `#${tabla}`;

        if( $(tablaDestino).length)
        {
            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');
            CoreUI.tableData.init();

            //  Nombre y apellidos
            CoreUI.tableData.addColumn( tabla , "nombre","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn( tabla , "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Empresa
                CoreUI.tableData.addColumn( tabla , "razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn( tabla , "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn( tabla , "email", "EMAIL", null, 'text-left');

            //  Teléfono
                CoreUI.tableData.addColumn( tabla , "telefono", "TELEFONO", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn( tabla , "fechaalta", "Fecha alta", null, 'text-left');

            //  Fecha de baja
                CoreUI.tableData.addColumn( tabla , "fechabaja", "Fecha baja", null, 'text-left');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn( tabla , null, "Estado", html);

            CoreUI.tableData.render( tabla , "Empleados", `empresa/${idempresa}/empleados`, false, false, false);
        }

    },

    /**
     * Carga el listado de empleados que tiene un contratista
     * @param {*} idempresa 
     * @param {*} tabla 
     */
    renderTablaEmpleadosContratista: async function(idempresa, tabla = 'listadoEmpleadosEmpresa')
    {
    
        var tablaDestino = `#${tabla}`;

        if( $(tablaDestino).length)
        {
            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');
                CoreUI.tableData.init();

                CoreUI.tableData.addColumn( tabla, function(row, type, val, meta){
                    var salida = `${row.nombre}`;
                    return salida;
                },'Nombre del empleado', null, 'text-left', '30%');

            // //  Nombre y apellidos
            //     CoreUI.tableData.addColumn( tabla , "nombre","Nombre", null, 'text-left');

            //  CIF
                CoreUI.tableData.addColumn( tabla , "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn( tabla , "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn( tabla , "email", "EMAIL", null, 'text-left');

            //  Teléfono
                CoreUI.tableData.addColumn( tabla , "telefono", "TELEFONO", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn( tabla , "fechaalta", "Fecha alta", null, 'text-left');

            //  Fecha de baja
                CoreUI.tableData.addColumn( tabla , "fechabaja", "Fecha baja", null, 'text-left');

            //  Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn( tabla , null, "Estado", html);

            //  Eliminar
                CoreUI.tableData.addColumn( tabla, function(row, type, val, meta)
                {
                    var html = `<a href="javascript:void(0);" class="btnRedirect" data-url="/contratista/empleado?id=${row.idempleado}"><i class="bi bi-pencil-square text-success pt-2" style="font-size: 17px;"></i></a>&nbsp;<a href="javascript:void(0);" class="btnEliminarEmpleado d-inline-block" data-id="${row.idempleado}" data-nombre="${row.nombre}"><i class="bi bi-trash text-danger" style="font-size: 17px;"></i></a>`;
                    return html;
                }, '&nbsp;',null, 'text-center', '20px');

            $(tablaDestino).addClass('no-clicable');
            CoreUI.tableData.render( tabla , "Empleados", `empresa/${idempresa}/empleados`, false, false, false);
        }

    },

    renderTablaEmpresasComunidad: async function(idcomunidad)
    {
        if(idcomunidad == '' || typeof idcomunidad === 'undefined' || idcomunidad == null )
            return;

        if($('#listadoEmpresaComunidad').length)
        {

            CoreUI.tableData.init();

            //  Añadimos la columna de desplegar
            // CoreUI.tableData.addColumnRow('listadoEmpresaComunidad', 'empresatipo');
            // CoreUI.tableData.addColumnRow('listadoEmpresaComunidad', 'documentacioncae');

            //  Razón social
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "cif", "CIF", null, 'text-justify');

            //  Email
            // CoreUI.tableData.addColumn('listadoEmpresaComunidad', "email", "EMAIL", null, 'text-left');
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', function(row, type, val, meta)
            {

                let enlaceEmailBlackList = '';

                //  Validación de e-mail en blacklist
                if(row.blacklist == true)
                {
                    enlaceEmailBlackList = `<a class="btnEmailBlackList mr-1" data-email="${row.email}" href="javascript:void(0);" title="Email No Valido"><i data-feather="alert-triangle" class="text-danger img-fluid"></i></a> `;
                }

                html = `${enlaceEmailBlackList}${row.email}`;

                return html;

            }, "EMAIL", null, 'text-left');    



            //  Email
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "telefono", "TELEFONO", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "personacontacto", "Persona de contacto", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "localidad", "Localidad", null, 'text-left');

            //  Tipo
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "tipoempresa", "Tipo", null, 'text-left');

            //  Último acceso
            // CoreUI.tableData.addColumn('listadoEmpresaComunidad', 
            // function(row, type, val, meta)
            // {
            //     var timeStamp;
            //     var fechaCreacion;

            //     if(!row.lastlogin)
            //     {
            //         //  Comprobamos si tiene ID de mensaje ya enviado
            //         btnReenvio = '';
            //         if(row.idmensajeregistro != '-1')
            //         {
            //             btnReenvio = `<a href="javascript:void(0);" class="btnReenviarMensaje d-inline-block btn btn-primary ml-2 pt-0 pb-0" title="Reenviar mensaje de registro" data-id="${row.idmensajeregistro}"><i data-feather="send" style="width:12px;height:12px;"></i></a>`;
            //         }
            //         timeStamp = '';
            //         fechaCreacion = `<span class="badge badge-pill bg-danger text-white">Nunca</span>${btnReenvio}`;
            //     }else{
            //         timeStamp = moment(row.lastlogin, 'YYYY-MM-DD hh:mm').unix();
            //         fechaCreacion = moment(row.lastlogin).locale('es').format('L');
            //     }

            //     return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
            // },
            // "Último acceso", null, 'text-center');    

            CoreUI.tableData.addColumn('listadoEmpresaComunidad', function(row, type, val, meta)
            {
                var enlaceEmailCertificado = '';

                if( (row.emailcertificado !== '' && row.emailcertificado !== 'null'  && row.emailcertificado !== null) && row.blacklist != true)
                {
                    enlaceEmailCertificado = `<a class="btnDescargarEmailCertificado mr-1" href="${baseURL}public/storage/emailcertificados/${row.emailcertificado}" target="_blank" title="Email certificado"><i data-feather="mail" class="text-success img-fluid"></i></a> `;
                }

                //  Actuaciones realizadas
                let actuaciones = '';
                if(row.estadoprotocolo == '1')
                {
                    actuaciones = `<a class="btnVerActuacionesEmpresa mr-1" data-idempresa="${row.id}" href="javascript:void(0);" title="Actuaciones realizadas"><i data-feather="calendar" class="text-primary img-fluid"></i></a>`;
                }

                let html = '';
                //  Email certificado 
                html = `<ul class="nav justify-content-end accionesTabla">`;

                if(actuaciones != '')
                    html = `${html}<li class="nav-item">${actuaciones}</li>`;

                if(enlaceEmailCertificado != '')
                    html = `${html}<li class="nav-item">${enlaceEmailCertificado}</li>`;

                html=`${html}<li class="nav-item">
                                    <a href="javascript:void(0);" class="btnEliminarEmpresaComunidad d-inline-block mr-1" data-id="${row.id}" data-nombre="${row.razonsocial}">
                                        <i data-feather="trash-2" class="text-danger img-fluid"></i>
                                    </a>

                                </li>
                            </ul>`;

                return html;
                // var checked='';
                //     if(row.cae_contratado === true)
                //     {
                //        checked = ' checked="checked" ';
                //     }
                //     return '<input type="checkbox" ' + checked + '>';
            }, "&nbsp;", null, 'text-left');    

            // //  Columna de acciones
            //     CoreUI.tableData.addColumn('listadoEmpresaComunidad', null, "", html);

                $('#listadoEmpresaComunidad').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpresaComunidad", "empresascomunidad", `comunidad/${idcomunidad}/empresas`, null, false, false);
        }  
    },

    renderTablaRequerimientosEmpresa: async function(idEmpresa)
    {
        if($('#listadoDocumentacionEmpresa').length)
        {

                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

            //  Nombre del documento
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', "requerimiento","Requerimiento", null, 'text-left');

            //  Fecha última actuación
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
                {
                    if(row.fechasubida == '' || row.fechasubida == 'null' || !row.fechasubida)
                    {
                        return 'No se ha realizado ninguna actuación';
                    }else{
                        return '<p class="mb-0 text-center">' + moment(row.fechasubida).locale('es').format('L') + '</p>';
                    }
                }, "Fecha última actuación", null, 'text-center');

            //  Estado
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
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
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
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

                //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                    if(canUploadFile)
                    // if(!ficheroAdjuntado && canUploadFile)
                    {  //  SUBIR FICHERO SOLO PARA CONTRATISTA (EMPRESA)
                        dataset = ` data-idcomunidad="${row.idcomunidad}" data-idempresa="${_idempresa}" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="empresa" `;
                        salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 30px;"></i></a></td>`;
                    }

                    if(!ficheroAdjuntado && !canUploadFile)
                    {
                        salida += '<td>&nbsp;</td>';
                    }
                    return salida;

                }, '&nbsp;', null, 'text-center');

                $('#listadoDocumentacionEmpresa').addClass('no-clicable');

                //  Si ya está inicializada destruimos para volver a generarla¿?
                //  FIXME: Cambiar url ajax simplemente
                if(typeof window['tablelistadoDocumentacionEmpresa'] !== 'undefined' )
                {
                    window['tablelistadoDocumentacionEmpresa'].destroy();
                }


                CoreUI.tableData.render("listadoDocumentacionEmpresa", "documentacioncae", `empresa/${idEmpresa}/documentacion`, null, false, false);

        }  
    },

    renderTablaInfoDescargas: async function(idempresa, idcomunidad)
    {

        if($('#listadoDocumentacionDescargaEmpresa').length )
        {

            CoreUI.tableData.init();
            CoreUI.tableData.columns = [];

            //  Nombre del requerimiento
                CoreUI.tableData.addColumn('listadoDocumentacionDescargaEmpresa', "nombre","Requerimiento", null, 'text-left');
                
            //  Fecha de descarga
                CoreUI.tableData.addColumn('listadoDocumentacionDescargaEmpresa', function(row, type, val, meta)
                {
                    if(row.fechadescarga == '' || row.fechadescarga == 'null' || !row.fechadescarga)
                    {
                        return 'N/D'
                    }else{
                        return '<p class="mb-0 text-center">' + moment(row.fechadescarga).locale('es').format('LLLL') + '</p>';
                    }
                }, "Fecha descarga", null, 'text-center');

                
                //  Estado del requerimiento (Descargado o pendiente de descargar)
                CoreUI.tableData.addColumn('listadoDocumentacionDescargaEmpresa', function(row, type, val, meta)
                {
                    if(row.fechadescarga == '' || row.fechadescarga == 'null' || !row.fechadescarga)
                    {
                        return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No descargado</span>';
                    }else{
                        return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Descargado</span>';
                    }
                }, "Estado", null, 'text-center');
                
                CoreUI.tableData.render("listadoDocumentacionDescargaEmpresa", "infodescargas", `requerimiento/comunidad/${idcomunidad}/empresa/${idempresa}/infodescarga`, null, false, false);
        }

    }

}

$(()=>{
    empresaCore.init();
});