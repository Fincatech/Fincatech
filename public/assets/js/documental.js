
let documentalCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    //  CONSTANTES DE TIPO DE REQUERIMIENTO
        RGPD_DOCUMENTACIONBASICA: 1,
        RGPD_CAMARASEGURIDAD: 2,
        RGPD_CONTRATOSCESION: 3,

        CAE_EMPRESA: 4,
        CAE_EMPLEADO: 5,
        CAE_COMUNIDAD: 6,
        CAE_AUTONOMO: 7,

        ENTIDAD_GENERAL: 'informacion', //  Documentos de sólo descarga
        ENTIDAD_EMPRESA: 'empresa',
        ENTIDAD_COMUNIDAD: 'comunidad',
        ENTIDAD_EMPLEADO: 'empleado',

        idrequerimiento: null,
        idcomunidad: null,
        idempleado: null,
        idempresa: null,
        idadministrador: null,
        entidad: null,

    //  Varible de control para saber cuál es el último requerimiento subido y así poder
    //  en consecuencia recargar el listado asociado al tipo de requerimiento
    lastUploadedRequerimiento: null,

    init: async function()
    {

        //  Notas informativas
        if($('#listadoNotasinformativas').length)
        {
          //  notasInformativasCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;        
        }

        //  Documentación básica
        if($('#listadoDocumentacionBasica').length)
        {
            // documentalCore.renderTablaDocumentacionBasica();
        }

        documentalCore.Events();
        core.Files.init();

        if($('#listadoNotasinformativas').length && core.Security.getRole() == 'DPD')
        {
            notasInformativasCore.renderTabla();
        }

    },

    Events: function()
    {   
        $('body .bntUploadDocumento').off();
        $('body').on(core.helper.clickEventType, '.bntUploadDocumento', function(e)
        {
            
            e.stopImmediatePropagation();
            documentalCore.uploadRequerimiento();
        });

        $('body .btnAdjuntarFicheroDocumento').off();
        $('body').on(core.helper.clickEventType, '.btnAdjuntarFicheroDocumento', async function()
        {
            console.log('btnAdjuntarFicheroDocumento contratista');
            documentalCore.idadministrador = null;
            documentalCore.idcomunidad = $(this).attr('data-idcomunidad');
            documentalCore.idempresa = $(this).attr('data-idempresa');
            documentalCore.idempleado = $(this).attr('data-idempleado');
            documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');
            documentalCore.idrelacionrequerimiento = $(this).attr('data-idrelacionrequerimiento');
            documentalCore.entidad = $(this).attr('data-entidad');

            const { value: file } = await Swal.fire({
            title: '',
            html: Constantes.CargaDocumento,
            showCancelButton: false,
            showConfirmButton: false,
            // grow: 'row',
            showCloseButton: true,
            didOpen: function()
            {

                //  Inicializamos el componente de ficheros
                    core.Files.init();
            }});

        });

        //  Adjuntar documento RGPD
        $('body .btnAdjuntarDocumentoRGPD').off();
        $('body').on(core.helper.clickEventType, '.btnAdjuntarDocumentoRGPD', async function()
        {

            documentalCore.idcomunidad = core.modelId;
            documentalCore.entidad = $(this).attr('data-tipo');
            documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');

            const { value: file } = await Swal.fire({
            title: '',
            html: Constantes.CargaDocumentoRGPD,
            showCancelButton: false,
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: function()
            {
                //  Si es de cámara de seguridad quitamos el título y las observaciones
                    if(documentalCore.entidad == 'camarasseguridad' && typeof documentalCore.idrequerimiento !== 'undefined')
                    {
                        $('.wrapperTituloDocumento').remove();
                        $('.wrapperObservaciones').remove();
                    }

                //  Inicializamos el componente de ficheros
                    core.Files.init();
            }});

        });        

        //  Adjuntar documento de contrato de confidencialidad de empleado
        $('body').on(core.helper.clickEventType, '.btnAdjuntarDocumentoEmpleadoRGPD', async function()
        {

            var nombreEmpleado = '';
            documentalCore.entidad = $(this).attr('data-tipo');
            documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');

            if( typeof $(this).attr('data-idrow') !== 'undefined')
            {
                nombreEmpleado = window['tablelistadoRGPDEmpleadosAdministracion'].row($(this).attr('data-idrow')).data().titulo;
            }


            const { value: file } = await Swal.fire({
            title: '',
            html: rgpdCore.Constantes.CargaDocumentoEmpleadoRGPD,
            showCancelButton: false,
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: function()
            {
                //  Cargamos el nombre del empleado
                    $('.wrapperTituloDocumento .tituloDocumentoRGPD').val(nombreEmpleado);

                //  Inicializamos el componente de ficheros
                    core.Files.init();
            }});

        });         

        $('body .bntUploadDocumentoRGPD').off();
        $('body').on(core.helper.clickEventType, '.bntUploadDocumentoRGPD', function(e)
        {
            e.stopImmediatePropagation();
            documentalCore.uploadRequerimientoRGPD();
        });

        $('body').on(core.helper.clickEventType, '.bntUploadDocumentoEmpleadoRGPD', function(e)
        {
            e.stopImmediatePropagation();
            //  Validamos que haya escrito el nombre del empleado
            if($('body .tituloDocumentoRGPD').val() == '')
            {
                $('body .tituloDocumentoRGPD').removeClass('form-error').addClass('form-error');
            }else{
                documentalCore.uploadContratoConfidencialidadEmpleadoRGPD();
            }
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarDocumentoRGPD', function(e)
        {
            //  Eliminamos el registro correspondiente según el tipo al que corresponda
                var id = $(this).attr('data-id');
                var nombre = $(this).attr('data-nombre');
                var tipo = $(this).attr('data-tipo');
                documentalCore.RGPD.eliminarDocumento(id, nombre, tipo);
        });

        //  Registro de descarga de fichero
        $('body').on(core.helper.clickEventType, '.btnDescargarFichero', function(e)
        {

            var idComunidad;
            var idEmpresa;
            var idFichero;
            var idUsuario = core.Security.user;

            if(core.Security.getRole() == 'CONTRATISTA')
                idEmpresa = core.Security.user;

            if(core.model == 'Comunidad')
                idComunidad = core.modelId;

            idFichero = $(this).attr('data-idfichero');

            //documentalCore.reflejarDescargaFichero(idFichero, idComunidad, idEmpresa, idUsuario);
            documentalCore.Helper.reflejarDescargaFichero(idFichero, idComunidad, idEmpresa, idUsuario);

        });

        $('body').on(core.helper.clickEventType, '.btnVerMotivoRechazo', function(ev)
        {
            CoreUI.Modal.Info(documentalCore.Model.motivosRechazo[ $(this).attr('data-idmotivo') ], 'Motivo del rechazo');
        });


        $('body').on(core.helper.clickEventType, '#listadoEmpleadosComunidad tr', function(evt)
        {

            evt.stopImmediatePropagation();
            var idEmpleado = window['tablelistadoEmpleadosComunidad'].row( $(this).attr('id')).data().idempleado;
            // contratista.idEmpleadoSeleccionado = idEmpleado;
            
            core.Files.Fichero.entidadId = idEmpleado;
            $('body .empleadoRequerimientosInfo').text('Requerimientos de ' + window['tablelistadoEmpleadosComunidad'].row( $(this).attr('id')).data().nombre );
            $('body .empleadoRequerimientosInfo').show();
            $('body .wrapperDocumentacionEmpleado .mensajeInformacion').hide();

            // $('body #listadoDocumentacionEmpleado').removeClass('d-none');
            $('body #wrapperContratistaDocumentacionEmpleado').removeClass('d-none');
            documentalCore.Listado.Cargar('wrapperContratistaDocumentacionEmpleado', documentalCore.ENTIDAD_EMPLEADO, documentalCore.CAE_EMPLEADO, null, core.Security.user, idEmpleado);
            $('.wrapperDocumentacionEmpleado').show();
            // empleadoCore.renderTablaDocumentacionEmpleado(idEmpleado, 'listadoDocumentacionEmpleado');

        });

        //  Documentación del empleado al hacer click sobre la tabla
        if( $('body #listadoEmpleadosContratista').length )
        {

            $('body').on(core.helper.clickEventType, '#listadoEmpleadosContratista tr', function(evt)
            {

                evt.stopImmediatePropagation();
                var idEmpleado = window['tablelistadoEmpleadosContratista'].row( $(this).attr('id')).data().idempleado;
                contratista.idEmpleadoSeleccionado = idEmpleado;
                
                core.Files.Fichero.entidadId = idEmpleado;
                $('body .empleadoRequerimientosInfo').text('Requerimientos de ' + window['tablelistadoEmpleadosContratista'].row( $(this).attr('id')).data().nombre );
                $('body .empleadoRequerimientosInfo').show();
                $('body .wrapperDocumentacionEmpleado .mensajeInformacion').hide();

                // $('body #listadoDocumentacionEmpleado').removeClass('d-none');
                $('body #wrapperContratistaDocumentacionEmpleado').removeClass('d-none');
                documentalCore.Listado.Cargar('wrapperContratistaDocumentacionEmpleado', documentalCore.ENTIDAD_EMPLEADO, documentalCore.CAE_EMPLEADO, null, core.Security.user, idEmpleado);
                // empleadoCore.renderTablaDocumentacionEmpleado(idEmpleado, 'listadoDocumentacionEmpleado');

                $('body .btnAdjuntarFicheroDocumento').off();
                $('body').on(core.helper.clickEventType, '.btnAdjuntarFicheroDocumento', async function()
                {
                    documentalCore.idadministrador = null;
                    documentalCore.idcomunidad = $(this).attr('data-idcomunidad');
                    documentalCore.idempresa = $(this).attr('data-idempresa');
                    documentalCore.idempleado = $(this).attr('data-idempleado');
                    documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');
                    documentalCore.idrelacionrequerimiento = $(this).attr('data-idrelacionrequerimiento');
                    documentalCore.entidad = $(this).attr('data-entidad');
        
                    const { value: file } = await Swal.fire({
                    title: '',
                    html: Constantes.CargaDocumento,
                    showCancelButton: false,
                    showConfirmButton: false,
                    // grow: 'row',
                    showCloseButton: true,
                    didOpen: function()
                    {
        
                        //  Inicializamos el componente de ficheros
                            core.Files.init();
                    }});
        
                });

            });

        }

        //  Selector Requisitos Pendientes CAE
        $('body').on(core.helper.clickEventType, '.btnRequisitosPendientesCAE', function(ev)
        {
            $('.wrapperRequerimientosCAE').show();
            $('.infoListado .seleccionado').html('');
        });

        //  Listado de requerimientos pendientes CAE
        $('body').on(core.helper.clickEventType, '.btnRequisitosPendientes', function(ev)
        {
           
            $('#listadoRequerimientosPendientes').hide();

            switch($(this).attr('data-tipo'))
            {
                case 'cae':
                    titulo = '- CAE Comunidad';
                    break;
                case 'cae_empresa':
                    titulo = '- CAE Empresas';
                    break;
                case 'rgpd':
                    titulo = '- RGPD';
                    break;
            }
            $('.infoListado .seleccionado').html(titulo);

            if( $(this).attr('data-tipo') == 'rgpd')
            {
                $('.wrapperRequerimientosCAE').hide();
            }
            documentalCore.Comunidad.renderTablaDocumentacionPendienteCAE( $(this).attr('data-tipo'));
        });

        //  Modal histórico de requerimiento
        $('body').on(core.helper.clickEventType, '.btnVerHistorial', function(e){
            var idRelacionRequerimiento = $(this).attr('data-idrelacionrequerimiento');
            var entidad = $(this).attr('data-entidad');
            documentalCore.Render.TablaHistorico(idRelacionRequerimiento, entidad);
        });

    },

    /**
     * 
     * @param {*} idrequerimiento 
     * @param {*} idtipo Admin C: Comunidad | E: Empresa | EM: Empleado 
     */
    uploadRequerimiento: async function()
    {
        //  Envía el documento al endpoint para registrarlo
        var data = Object();
            data = {
                idcomunidad: documentalCore.idcomunidad,
                idempresa: documentalCore.idempresa,
                idempleado: documentalCore.idempleado,
                idrequerimiento: documentalCore.idrequerimiento,
                idrelacionrequerimiento: documentalCore.idrelacionrequerimiento,
                idadministrador: documentalCore.idadministrador,
                entidad: documentalCore.entidad,
                fichero: core.Files.fichero
            };
            // console.log(data);
            // console.log('endpoint requerimiento ' + `requerimiento/${documentalCore.entidad}/${documentalCore.idrequerimiento}`);
        await apiFincatech.post(`requerimiento/${documentalCore.entidad}/${documentalCore.idrequerimiento}`, data).then(async (response) =>
        {
            console.log(response);
            var responseData = JSON.parse(response);

            if(responseData.status['response'] == "ok")
            {
                CoreUI.Modal.Success("El documento se ha registrado correctamente");

                //  Recargamos la tabla de empresas para reflejar el cambio
                    empresaCore.renderTabla();

                //  Recargamos el listado de comunidades
                    documentalCore.refreshTables.reloadTables();

            }else{
                //  TODO: Ver cuál es el error en el json
                Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);

            }

        });


    },

    /**
     * Sube el contrato de confidencialidad entre empleado y administrador
     */
    uploadContratoConfidencialidadEmpleadoRGPD: async function()
    {
        //  TODO: Hay que ver si es un update o no, esto viene determinado por el idrequerimiento
        var _titulo = ($('body .tituloDocumentoRGPD').length ? $('body .tituloDocumentoRGPD').val() : '');
        var _observaciones = ($('body .observacionesDocumentoRGPD').length ? $('body .observacionesDocumentoRGPD').val() : '');

         //  Envía el documento al endpoint para registrarlo
         var data = Object();
             data = {
                 idrequerimiento: (typeof documentalCore.idrequerimiento === 'undefined' ? '-1' : documentalCore.idrequerimiento),
                 entidad: documentalCore.entidad,
                 fichero: core.Files.fichero,
                 titulo: _titulo,
                 observaciones: _observaciones,
             };

         await apiFincatech.post(`rgpd/empleados/contratos/confidencialidad/create`, data).then(async (response) =>
         {
 
             var responseData = JSON.parse(response);
 
             if(responseData.status['response'] == "ok")
             {
                 CoreUI.Modal.Success("El documento se ha registrado correctamente");

                 // Recargamos la tabla de cámaras de seguridad adjuntos
                    // if($('body #listadoCamarasSeguridad').length)
                    //     window['tablelistadoCamarasSeguridad'].ajax.reload();               

                 // Recargamos la tabla de contratos de cesión adjuntos
                    // if($('body #listadoContratosCesion').length)
                    //     window['tablelistadoContratosCesion'].ajax.reload();

                    documentalCore.refreshTables.reloadTables();

             }else{
                 //  TODO: Ver cuál es el error en el json
                 CoreUI.Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);
 
             }
 
         });
    },

    refreshTables: 
    {
        //  Recarga todas las tablas susceptibles de ser recargadas tras cualquier tipo de subida de documento
        reloadTables: function(){

            //  Certificados digitales
            documentalCore.refreshTables.reloadTable('listadoDocumentacionCertificadoDigital');

            //  Listado comunidad
            documentalCore.refreshTables.reloadTable('listadoComunidad');

            //  Listado Documentación comunidad
            documentalCore.refreshTables.reloadTable('listadoDocumentacionComunidad');
            CoreUI.tableData.tableEventsClick('listadoDocumentacionComunidad');
            documentalCore.refreshTables.reloadTable('listadoDocumentacionComunidadCAE');
            documentalCore.refreshTables.reloadTable('listadoDocumentacionComunidadCae');
            documentalCore.refreshTables.reloadTable('listadoDocumentacionBasica');

            //  Listado Empresa
            documentalCore.refreshTables.reloadTable('listadoEmpresa');
            documentalCore.refreshTables.reloadTable('listadoEmpresasComunidad');
            documentalCore.refreshTables.reloadTable('listadoDocumentacionEmpresa');

            //  Listado empleados 
            documentalCore.refreshTables.reloadTable('listadoEmpleadosComunidad');
            documentalCore.refreshTables.reloadTable('listadoEmpleadosContratista');

            //  RGPD
            documentalCore.refreshTables.reloadTable('listadoNotasInformativas');
            documentalCore.refreshTables.reloadTable('listadoInformevaloracionseguimiento');
            documentalCore.refreshTables.reloadTable('listadoDpd');
            documentalCore.refreshTables.reloadTable('listadoContratosCesion');
            documentalCore.refreshTables.reloadTable('listadoCamarasSeguridad');
            documentalCore.refreshTables.reloadTable('listadoContratosCesion');
            documentalCore.refreshTables.reloadTable('listadoRGPDEmpleadosAdministracion');

            //  Requerimientos Pendientes
            documentalCore.refreshTables.reloadTable('listadoRequerimientosPendientes');

            if(typeof rgpdCore !=='undefined')
            {
                rgpdCore.comprobarContratoAdministracionComunidad(core.modelId);
            }

            
            if(core.Security.getRole() == 'CONTRATISTA')
            {
                empresaCore.comprobarAceptacionOperatoria(core.Security.user, documentalCore.idcomunidad);

                //  Documentos de Empresa
                if($('body .wrapperListadoDocumentacionCAEEmpresa').length > 0){
                    contratista.Model.ListarDocumentosEmpresa();
                }

                if($('body .wrapperContratistaDocumentacionEmpleado').length > 0){
                    documentalCore.Listado.Cargar('wrapperContratistaDocumentacionEmpleado', documentalCore.ENTIDAD_EMPLEADO, documentalCore.CAE_EMPLEADO, null, core.Security.user, contratista.idEmpleadoSeleccionado);
                }

            }

        },

        /**
         * Recarga una tabla conectada a un endpoint
         * @param {string} _tableName 
         */
        reloadTable: function(_tableName){

            if($(`body #${_tableName}`).length > 0 && typeof window[`table${_tableName}`] !== 'undefined')
            {
                window[`table${_tableName}`].ajax.reload();
            }
        }

    },

    /**
     * 
     * @param {*} idrequerimiento 
     * @param {*} idtipo Admin C: Comunidad | E: Empresa | EM: Empleado 
     */
    uploadRequerimientoRGPD: async function()
     {
        //  TODO: Hay que ver si es un update o no, esto viene determinado por el idrequerimiento
        var _titulo = ($('body .tituloDocumentoRGPD').length ? $('body .tituloDocumentoRGPD').val() : '');
        var _observaciones = ($('body .observacionesDocumentoRGPD').length ? $('body .observacionesDocumentoRGPD').val() : '');

         //  Envía el documento al endpoint para registrarlo
         var data = Object();
             data = {
                 idcomunidad: documentalCore.idcomunidad,
                 idrequerimiento: (typeof documentalCore.idrequerimiento === 'undefined' ? '-1' : documentalCore.idrequerimiento),
                 entidad: documentalCore.entidad,
                 fichero: core.Files.fichero,
                 titulo: _titulo,
                 observaciones: _observaciones,
             };

         await apiFincatech.post(`rgpd/${documentalCore.entidad}/${documentalCore.idcomunidad}/create`, data).then(async (response) =>
         {
 
             var responseData = JSON.parse(response);
 
             if(responseData.status['response'] == "ok")
             {
                 CoreUI.Modal.Success("El documento se ha registrado correctamente");

                 // Recargamos la tabla de cámaras de seguridad adjuntos
                    // if($('body #listadoCamarasSeguridad').length)
                    //     window['tablelistadoCamarasSeguridad'].ajax.reload();               

                 // Recargamos la tabla de contratos de cesión adjuntos
                    // if($('body #listadoContratosCesion').length)
                    //     window['tablelistadoContratosCesion'].ajax.reload();

                    documentalCore.refreshTables.reloadTables();

             }else{
                 //  TODO: Ver cuál es el error en el json
                 Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);
 
             }
 
         });
 
 
     },

     Crud: {

        cambiarEstadoRequerimiento: async function(datos, _idEstado, _Observaciones, _fechaCaducidad)
        {
            var _entidad = datos.entidad;
            var _idrequerimiento = datos.idrelacionrequerimiento;

            var data = Object();

            data.fechacaducidad = _fechaCaducidad;
            data.idestado = _idEstado;
            data.observaciones = _Observaciones;

            apiFincatech.post(`requerimiento/${_entidad}/${_idrequerimiento}/estado`, data).then( (result) =>{
                switch(_entidad){
                    case 'certificadorequerimiento': // Certificado digital
                        TecnicoCertificado.Render.ComunidadesDocumentacionAportada(TecnicoCertificado.Model.comunidadId);
                        break;
                    default:
                        tecnicoRevision.GUI.RenderTablaDocumentosPendientesVerificacion();
                        Swal.close();
                        CoreUI.Modal.Success('El estado del requerimiento se ha modificado correctamente','Revisión documental');                        
                        break;
                }
            });
        },

        uploadRequerimiento: async function()
        {

        },

        uploadRequerimientoRGPD: async function()
        {

        },

        deleteRequerimiento: function()
        {

        },

     },

     /** Componente de listado de documentación */
     Listado: {

        Cargar: function( DOMElement, entidad, tipoRequerimiento, idComunidad = null, idEmpresa = null, idEmpleado = null )
        {
            documentalCore.Model.GetAll(DOMElement, entidad, tipoRequerimiento, idComunidad, idEmpresa, idEmpleado);
        },

     },

     Model: {

        entidad: null,

        idrequerimiento: null,
        idcomunidad: null,
        idempleado: null,
        idempresa: null,

        comunidadcae: null,
        documentacionempresa: null,
        documentacionempleado: null,

        documentacionbasica: null,
        notasinformativas: null,
        informesvaloracionseguimiento: null,
        contratoscesionterceros: null,

        motivosRechazo: Array(),

        /**
         * Recupera todos los requerimientos documentales para una entidad concreta
         * @param {string} entidad Nombre del tipo de Requerimiento que se va a recuperar
         */
        GetAll: function(DOMElDestination, entidad, tipoRequerimiento = null, idComunidad = null, idEmpresa = null, idEmpleado = null)
        {
            
            var data = Object();
            data.idcomunidad = idComunidad;
            data.idempresa = idEmpresa;
            data.idusuario = core.Security.user;
            data.idempleado = idEmpleado;
            data.entidaddestino = entidad;
            data.tiporequerimiento = tipoRequerimiento;

            apiFincatech.post('documental/requerimientos', data).then( result =>
            {
                //  Renderizamos el listado de documentos
                var datos = JSON.parse(result);
                documentalCore.Render.List(DOMElDestination, datos.data, tipoRequerimiento, entidad, idComunidad, idEmpresa, idEmpleado);
            });
        },

        /**
         * Recupera la información de un requerimiento documental concreto
         * @param {*} entidad 
         * @param {*} _requerimientoId 
         */
        Get: function(entidad, _requerimientoId)
        {

        },

     },

     Helper: {
        /** Refleja la descarga de un fichero por parte de un usuario */
        reflejarDescargaFichero: function(idFichero, idComunidad, idEmpresa, idUsuario)
        {

            var data = Object();
                data.idfichero = idFichero;
                data.idcomunidad = idComunidad;
                data.idempresa = idEmpresa;
                data.idusuario = idUsuario;

            apiFincatech.post('storage/descarga', data).then( (result) =>
            {
                //  Habilitamos el botón de subir fichero
                $('body .btnSubidaFichero').show();
                if(core.Security.getRole() == 'CONTRATISTA')
                {
                    documentalCore.refreshTables.reloadTables();
                }
            });

        },

        ComprobarDescargaRequerimientoPorEmpresa: function(idempresa, requerimiento, descargas)
        {
            var resultado = false;

            if(descargas.length > 0)
            {
                for(var iDescarga = 0; iDescarga < descargas.length; iDescarga++)
                {
                    if(descargas[iDescarga].idempresa == idempresa)
                    {
                        resultado = true;
                        break;
                    }
                }
            }

            return resultado;

        }

     },

    Comunidad: {
    
        /**  */
        renderTablaDocumentacionComunidad: async function(id)
        {
            if(id == '' || typeof id === 'undefined' || id == null )
                return;

            if($('#listadoDocumentacionComunidad').length)
            {

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                //  Requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidad', "requerimiento", 'Requerimiento', null, 'text-justify', '70%');

                //  Estado del requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidad', 
                        function(row, type, val, meta)
                        {
                            if(row.idficherorequerimiento == null)
                            {
                                return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                            }else{
                                return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Subido</span>';
                            }
                        },
                    "Estado", null, 'text-center', '10%');

                //  Fichero asociado
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidad', 
                        function(row, type, val, meta)
                        {
                            var ficheroAdjuntado = false;
                            var htmlSalida = '';
                            var estado = '';

                            //  Enlace de descarga
                            if(row.idficherorequerimiento != null)
                            {
                                ficheroAdjuntado = true;
                                //  Tiene fichero ya subido
                                htmlSalida += `<a href="${config.baseURL}public/storage/${row.storagefichero}" class="btnDescargarFichero" download="${row.nombre}" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>`;
                            }

                            //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                            // if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                            if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') || core.Security.getRole() == 'TECNICOCAE')
                            {
                                htmlSalida += `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento ml-2" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>`;
                            }

                            return htmlSalida; // row.requerimiento;

                        }, 
                    "Fichero", null, 'text-center', '20%');

                    $('#listadoDocumentacionComunidad').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentacionComunidad", "documentacioncomunidad", `comunidad/${id}/documentacioncomunidad`, false, false, false );
            }    
        },

        renderTablaDocumentacionComunidadCAE: async function(id)
        {
            if(id == '' || typeof id === 'undefined' || id == null )
                return;

            if($('#listadoDocumentacionComunidadCae').length)
            {

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                var tituloColumnaRequerimiento = 'Requerimiento';
                if(core.Security.getRole() == 'CONTRATISTA')
                {
                    tituloColumnaRequerimiento = 'Requerimiento (Descargue los documentos de este listado)';
                }

                //  Requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', "requerimiento", tituloColumnaRequerimiento, null, 'text-justify', '70%');

                //  Estado del requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', 
                        function(row, type, val, meta)
                        {
                            if(row.idficherorequerimiento == null)
                            {
                                return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                            }else{
                                //  Comprobamos el estado del requerimiento
                                if(core.Security.getRole() == 'CONTRATISTA')
                                {
                                    
                                    //  Comprobamos si el contratista ha descargado el documento previamente
                                    if(documentalCore.Helper.ComprobarDescargaRequerimientoPorEmpresa(core.Security.user, row.idficherorequerimiento, row.descargas))
                                    {
                                        return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Descargado</span>';
                                    }else{
                                        return '<span class="badge rounded-pill bg-warning pl-3 pr-3 pt-2 pb-2 d-block">Pendiente descarga</span>';
                                    }
                                }else{
                                    return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Subido</span>';
                                }
                            }
                        },
                    "Estado", null, 'text-center', '10%');

                //  Fichero asociado
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', 
                        function(row, type, val, meta)
                        {
                            var ficheroAdjuntado = false;
                            var htmlSalida = '<div class="row mb-0 mx-auto" style="width:fit-content;">';
                            var estado = '';

                            //  Enlace de descarga
                            if(row.idficherorequerimiento != null)
                            {
                                ficheroAdjuntado = true;
                                //  Tiene fichero ya subido
                                htmlSalida += `<div class="col text-center align-self-center p-0"><a href="${config.baseURL}public/storage/${row.storageficherorequerimiento}" class="btnDescargarFichero" data-idfichero="${row.idficherorequerimiento}" target="_blank" download="${row.nombreficherorequerimiento}" ><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a></div>`;
                            }

                            //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                            // if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                            if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') || core.Security.getRole() == 'TECNICOCAE')
                            {
                                htmlSalida += `<div class="col text-center align-self-center p-0"><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento ml-2" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a></div>`;
                            }

                            //  Historial
                            if(row.historico === true){
                                htmlSalida += `
                                <div class="col text-center align-self-center p-0">
                                    <a href="javascript:void(0)" class="btnVerHistorial ml-2" data-toggle="tooltip" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidadrequerimiento" title="Ver Histórico">
                                        <i class="bi bi-clock-history text-danger" style="font-size: 18px;"></i>
                                    </a>
                                </div>`;
                            }

                            return htmlSalida + `</div>`; // row.requerimiento;

                        }, 
                    "Fichero", null, 'text-center', '20%');

                    $('#listadoDocumentacionComunidadCae').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentacionComunidadCae", "documentacioncomunidad", `comunidad/${id}/documentacioncomunidad`, false, false, false );

            }    
        },        

        /**
         * Renderiza la tabla de requerimientos de certificados digitales
         * @param {*} id 
         * @returns 
         */
        renderTablaDocumentacionComunidadCertificadoDigital: async function(id)
        {
            if(id == '' || typeof id === 'undefined' || id == null )
                return;

            if($('#listadoDocumentacionCertificadoDigital').length)
            {

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                var tituloColumnaRequerimiento = 'Requerimiento';

                //  Requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', "requerimiento", tituloColumnaRequerimiento, null, 'text-justify', '30%');

                //  Modelo para descargar
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', 
                    function(row, type, val, meta)
                    {
                        var htmlSalida = '<div class="row mb-0 mx-auto" style="width:fit-content;">';

                        htmlSalida += `<div class="col text-center align-self-center p-0">`;
                        //  Enlace de descarga
                        if(row.idfichero != null)
                        {
                            //  Tiene modelo disponible para descargar
                            htmlSalida += `<a href="${config.baseURL}public/storage/${row.storagefichero}" data-idfichero="${row.idficherorequerimiento}" target="_blank" download="${row.nombrefichero}" ><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>`;
                        }

                        return htmlSalida + `</div></div>`; 

                    }, 
                    "Modelo", null, 'text-center', '10%'); 

                //  Fecha de caducidad
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', function(row, type, val, meta){
                        var fechaCaducidad = row.fechacaducidad;
                        if(!fechaCaducidad)
                        {
                            return 'No caduca';
                        }else{
                            return moment(fechaCaducidad).locale('es').format('L');
                        }
                    },"Fecha de caducidad", null, 'text-center', '30%'); 

                //  Estado del requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', 
                        function(row, type, val, meta)
                        {
                            if(row.idficherorequerimiento == null)
                            {
                                return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                            }else{
                                // Estado en el que se encuentra el documento
                                estadoDocumento = documentalCore.Render.RenderEstadoRequerimiento(row.idestado);

                                if(row.idestado == 7)
                                {
                                    var nElemento = documentalCore.Model.motivosRechazo.length;
                                    documentalCore.Model.motivosRechazo[nElemento] = row.observaciones;
                                    estadoDocumento += `<a href="javascript:void(0);" class="btnVerMotivoRechazo" data-idmotivo="${nElemento}">ver motivo rechazo</a>`;
                                }

                                return estadoDocumento;
                            }
                        },
                    "Estado", null, 'text-center', '10%');

                //  Fichero asociado
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', 
                        function(row, type, val, meta)
                        {
                            var ficheroAdjuntado = false;
                            var htmlSalida = '<div class="row mb-0 mx-auto" style="width:fit-content;">';
                            var estado = '';

                            //  Enlace de descarga
                            if(row.idficherorequerimiento != null)
                            {
                                ficheroAdjuntado = true;
                                //  Tiene fichero ya subido
                                htmlSalida += `<div class="col text-center align-self-center p-0"><a href="${config.baseURL}public/storage/${row.storageficherorequerimiento}" class="btnDescargarFichero" data-idfichero="${row.idficherorequerimiento}" target="_blank" download="${row.nombreficherorequerimiento}" ><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a></div>`;
                            }

                            //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                            // if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                            if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') || core.Security.getRole() == 'TECNICOCERTIFICADO')
                            {
                                htmlSalida += `<div class="col text-center align-self-center p-0"><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento ml-2" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="certificado"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a></div>`;
                            }

                            //  Historial
                            if(row.historico === true){
                                htmlSalida += `
                                <div class="col text-center align-self-center p-0">
                                    <a href="javascript:void(0)" class="btnVerHistorial ml-2" data-toggle="tooltip" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="certificadorequerimiento" title="Ver Histórico">
                                        <i class="bi bi-clock-history text-danger" style="font-size: 18px;"></i>
                                    </a>
                                </div>`;
                            }

                            return htmlSalida + `</div>`; // row.requerimiento;

                        }, 
                    "Fichero", null, 'text-center', '20%');

                    $('#listadoDocumentacionCertificadoDigital').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentacionCertificadoDigital", "documentacioncertificado", `comunidad/${id}/documentacioncertificado`, false, false, false );

            }    
        },   

        renderTablaDocumentacionPendienteCAE: async function(tipoRequerimiento)
        {
            //TODO: Comprobar si es de comunidad, de cae o de rgpd

            //  Construimos la tabla
            if($('#listadoRequerimientosPendientes').length)
            {

                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                //  Endpoint tabla
                    var endpointTabla = `requerimiento/${tipoRequerimiento}/pendientes/list`;
                    var tituloColumna = 'Requerimiento pendiente ' + tipoRequerimiento.toUpperCase();

                //  Código Comunidad
                    CoreUI.tableData.addColumn('listadoRequerimientosPendientes', "codigocomunidad", 'CÓD', null, 'text-left', '30px');

                //  Comunidad
                    CoreUI.tableData.addColumn('listadoRequerimientosPendientes', "comunidad", 'Comunidad', null, 'text-left');

                //  Nombre del requerimiento
                    CoreUI.tableData.addColumn('listadoRequerimientosPendientes', "requerimiento", tituloColumna, null, 'text-left');

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

                //  Fichero asociado
                    // CoreUI.tableData.addColumn('listadoRequerimientosPendientes', 
                    //     function(row, type, val, meta)
                    //     {
                    //         var ficheroAdjuntado = false;
                    //         var htmlSalida = '';
                    //         var estado = '';

                    //         //  Enlace de descarga
                    //         if(row.idficherorequerimiento != null)
                    //         {
                    //             ficheroAdjuntado = true;
                    //             //  Tiene fichero ya subido
                    //             htmlSalida += `<a href="${config.baseURL}public/storage/${row.storageficherorequerimiento}" class="btnDescargarFichero" data-idfichero="${row.idficherorequerimiento}" target="_blank" download="${row.nombreficherorequerimiento}" ><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>`;
                    //         }

                    //         //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                    //         // if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                    //         if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') || core.Security.getRole() == 'TECNICOCAE')
                    //         {
                    //             htmlSalida += `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento ml-2" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>`;
                    //         }

                    //         return htmlSalida; // row.requerimiento;

                    //     }, 
                    // "Fichero", null, 'text-center', '20%');

                    CoreUI.tableData.render("listadoRequerimientosPendientes", "pendientes", endpointTabla, false, true, true, null, true, true, 'comunidad' ).then ( () =>{
                        window['tablelistadoRequerimientosPendientes'].table(0).columns(0).visible(false);
                        window['tablelistadoRequerimientosPendientes'].table(0).columns(1).visible(false);
                    });
                    $('#listadoRequerimientosPendientes').show();
            }              
        }

    },

    CAE:{

        verModalAdjuntarFichero: async function(idtiporequerimiento, idempresa)
        {
        
        },

        /**
         * 
         * @param {*} idEmpleado 
         * @param {*} tablaDestino 
         */
        renderTablaDocumentacionEmpleado: function(idEmpleado, tablaDestino)
        {

            CoreUI.tableData.init();
            CoreUI.tableData.columns = [];
            
            //  Nombre
            CoreUI.tableData.addColumn(tablaDestino, 'nombre' ,"Nombre Requerimiento", null, 'text-left');
            
                //  Estado del requerimiento
                CoreUI.tableData.addColumn(tablaDestino, 
                function(row, type, val, meta)
                {
                    if(row.idficherorequerimiento == null)
                    {
                        return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                    }else{
                        return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Subido</span>';
                    }
                },
            "Estado", null, 'text-center', '10%');

                //  Fichero asociado
                CoreUI.tableData.addColumn(tablaDestino, 
                function(row, type, val, meta)
                {
                    var ficheroAdjuntado = false;
                    var htmlSalida = '';
                    var estado = '';

                    //  Enlace de descarga
                    if(row.idficherorequerimiento != null)
                    {
                        ficheroAdjuntado = true;
                        //  Tiene fichero ya subido
                        htmlSalida += `<a href="${config.baseURL}public/storage/${row.storagefichero}" target="_blank" download="${row.nombre}" ><i class="bi bi-cloud-arrow-down mr-2" style="font-size:24px;"></i></a>`;
                    }

                    //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                    if(core.Security.getRole() == 'CONTRATISTA')
                    {
                        htmlSalida += `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" data-idcomunidad="" data-idempresa="" data-idempleado="${idEmpleado}" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="empleado"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>`;
                    }

                    return htmlSalida; // row.requerimiento;

                }, 
            "Documento", null, 'text-center', '20%');

            //  
/**
 * 
 *                                 <th class="text-center" style="background: #dee2e6 !important;">Documento</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Fecha de caducidad</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Observaciones</th>
 */

            $('#'+tablaDestino).addClass('no-clicable');
            CoreUI.tableData.render(tablaDestino, "documentacioncae",`empleado/${idEmpleado}/documentacion`, false, false, false);
        }

    },

    PRL:{
    
        verModalAdjuntarFichero: async function()
        {
        
        }

    },

    RGPD: {

    /** Elimina una comunidad previa confirmación */
        eliminarDocumento: function(id, nombre, tipo)
        {
            var destinoTablaHTML = '';
            switch(tipo)
            {
                case 'camarasseguridad':
                    destinoTablaHTML = 'listadoCamarasSeguridad';
                    break;
                case 'contratoscesion':
                    destinoTablaHTML = 'listadoContratosCesion';
                    break;
                case 'rgpdempleado':
                    destinoTablaHTML = 'listadoRGPDEmpleadosAdministracion';
                    break;
            }

            core.Modelo.Delete('requerimiento/'+tipo, id, '', destinoTablaHTML, 'Eliminar requerimiento', `¿Desea eliminar el requerimiento ${nombre}?`);
        },

        cargarDocumentacionCamarasSeguridad: async function()
        {
            if($('.wrapperDocumentosCamarasSeguridad'))
            {

                //  Llamamos al endpoint que carga la documentación de las cámaras de seguridad
                    apiFincatech.get('rgpd/requerimiento/2/list').then( (result) =>
                    {

                        var documentos = JSON.parse(result);
                        var outputHTML = '<div class="row">';

                        for(x=0; x < documentos.data.Requerimiento.length;x++)
                        {
                            // console.log(documentos.data.Requerimiento[x]);
                            var fDescarga = documentos.data.Requerimiento[x].ficheroscomunes[0].nombrestorage;
                            var nombreFichero = documentos.data.Requerimiento[x].ficheroscomunes[0].nombre;
                            outputHTML += `
                                <div class="col-12 col-sm-4">
                                    <a href="${config.baseURL}public/storage/${fDescarga}" target="_blank" download="${nombreFichero}">
                                        <p class="m-0 d-inline-flex">
                                            <i class="bi bi-cloud-arrow-down mr-2" style="font-size:24px;"></i>
                                            <span class="align-self-center">${documentos.data.Requerimiento[x].nombre}</span>
                                        </p>
                                    </a>
                                </div>
                            `;
                        }

                        outputHTML += '</div>';
                        $('.wrapperDocumentosCamarasSeguridad').html(outputHTML);

                    });

            }
            return true;
        },

    },

    Render: {

        /**
         * Renderiza el listado de requerimientos
         * @param {string} DOMElDestination Elemento en el que se va a pintar el listado de requerimientos
         * @param {Object} data Datos que se van a renderizar
         * @param {string} tipoRequerimiento Tipo de requerimiento
         */
        List: function(DOMElDestination, data, tipoRequerimiento, entidad, idComunidad = null, idEmpresa = null, idEmpleado = null)
        {

            //  Endpoint desde el que va a descargar
                var headerHTML = ``;
                var bodyHTML = ``;
                var renderHTML = ``;
                // var propiedadesRequerimiento = documentalCore.Render.CheckPropiedadesRequerimiento(data);
            
                $(`#${DOMElDestination} .contenido`).html('');

                headerHTML = `
                    <div class="row text-uppercase mb-2" style="background-color: rgb(243, 240, 215);">
                        <div class="col-3 text-left">
                            <p class="m-0 pt-2 pb-2 font-weight-bold">Requerimiento</p>
                        </div>
                        <div class="col-2 text-center">
                            <p class="m-0 pt-2 pb-2 font-weight-bold">Fecha última actuación</p>
                        </div>
                        <div class="col-3 text-center">
                            <p class="m-0 pt-2 pb-2 font-weight-bold">Estado</p>
                        </div>
                        <div class="col-1 pt-2 pb-2">&nbsp;</div>
                        <div class="col-1 pt-2 pb-2  align-self-center">&nbsp;</div>
                        <div class="col-2 pt-2 pb-2">&nbsp;</div>
                    </div>`;

            for(var i = 0; i < data.length; i++)
            {

                var fechaSubida = '';           //  Fecha de subida
                var estadoDocumento;            //  Estado del documento
                var idRelacionRequerimiento = data[i]['documentoasociado']['id'];    //  ID del documento previamente subido
                var enlaceFicheroDescarga = 'javascript:void(0);';      //  Enlace del fichero de descarga
                var enlaceFicheroDescargaNombre = '';
                var accionHistorial = '';

                //  Construcción enlace de descarga de fichero
                    if(data[i].ficheroscomunes.length > 0)
                    {
                        enlaceFicheroDescarga = `${config.baseURL}public/storage/${data[i].ficheroscomunes[0].nombrestorage}`;
                        enlaceFicheroDescargaNombre = data[i].ficheroscomunes[0].nombre;
                    }

                //  Construimos el icono de descarga de documento
                    var accionDescarga = `<p class="m-0 fechaSubida text-center small">
                                                <a href="${enlaceFicheroDescarga}" download="${enlaceFicheroDescargaNombre}" target="_blank" title="Ver documento">
                                                    <i class="bi bi-cloud-arrow-down text-primary mr-1" style="font-size: 24px;"></i>
                                                </a>
                                            </p>`;
                            
                    if(data[i].historico === true)
                    {
                        accionHistorial = `
                            <a href="javascript:void(0)" class="btnVerHistorial d-inline-block position-absolute mt-1 ml-3" data-toggle="tooltip" data-idrelacionrequerimiento="${data[i]['documentoasociado']['id']}" data-entidad="${entidad}requerimiento" title="Ver Histórico">
                                <i class="bi bi-clock-history text-danger" style="font-size: 18px;"></i>
                            </a>                        
                        `; 
                    }

                var accionVerFichero = '';
                
                //  Acción de Ver fichero previamente subido por el usuario
                if(typeof data[i]['documentoasociado'] !== 'undefined')
                {
                    
                    if(data[i]['documentoasociado']['fechasubida'] === null)
                    {
                        fechaSubida = 'No Disponible';
                    }else{
                        fechaSubida = moment(data[i]['documentoasociado']['fechasubida']).locale('es').format('L');
                        //  Construimos el icono de ver fichero
                        accionVerFichero = `<p class="m-0 fechaSubida text-center small">
                                                <a href="${config.baseURL}public/storage/${data[i]['documentoasociado']['nombrestorage']}" class="text-success" download="${data[i]['documentoasociado']['nombre']}">
                                                    <i class="bi bi-file-earmark-arrow-down" style="font-size: 24px;"></i>
                                                </a>
                                                Subido el ${fechaSubida}
                                            </p>`;
                        idRelacionRequerimiento = data[i]['documentoasociado']['id'];
                    }
                    estadoDocumento = documentalCore.Render.RenderEstadoRequerimiento(data[i]['documentoasociado']['idestado']);


                    //  Si es un documento rechazado mostramos el por qué

                        if(data[i]['documentoasociado']['idestado'] == 7)
                        {
                            var nElemento = documentalCore.Model.motivosRechazo.length;
                            documentalCore.Model.motivosRechazo[nElemento] = data[i]['documentoasociado']['observaciones'];
                            estadoDocumento += `<a href="javascript:void(0);" class="btnVerMotivoRechazo" data-idmotivo="${nElemento}">ver motivo rechazo</a>`;
                        }

                }

                //  Si es un tipo de requerimiento CAE_EMPRESA o CAE_EMPLEADO no tienen icono de descargar fichero asociado a requerimiento
                if(tipoRequerimiento == documentalCore.CAE_AUTONOMO || tipoRequerimiento == documentalCore.CAE_EMPRESA || tipoRequerimiento == documentalCore.CAE_COMUNIDAD || tipoRequerimiento == documentalCore.CAE_EMPLEADO)
                {
                    accionDescarga = '';
                }

                //  Entidad a la que va a subir el documento
                //  ID de requerimiento que se va a adjuntar
                var idRequerimiento = data[i]['id'];
                var accionSubida='';
                if(core.Security.getRole() == 'CONTRATISTA')
                {
                 accionSubida = `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" data-idcomunidad="${idComunidad}" data-idempresa="${idEmpresa}" data-idempleado="${idEmpleado}" data-idrequerimiento="${idRequerimiento}" data-idrelacionrequerimiento="${idRelacionRequerimiento}" data-entidad="${entidad}" data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento">
                                        <i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i>
                                    </a>`;
                }
                
                bodyHTML += `
                <div class="row">
                    <div class="col-3 font-weight-light text-left">
                        <p>${data[i].nombre}</p>
                    </div>
                    <div class="col-2 text-center font-weight-light">
                        <p>${fechaSubida}</p>
                    </div>
                    <div class="col-3 text-center font-weight-light">
                        <p>${estadoDocumento}</p>
                    </div>
                    <div class="col-1 text-center">${accionDescarga}</div>
                    <div class="col-1 text-center  align-self-center p-0">${accionSubida}${accionHistorial}</div>
                    <div class="col-2">${accionVerFichero} </div>
                </div>`;
            }

            // console.log(data);

            renderHTML = headerHTML + bodyHTML;

            $(`#${DOMElDestination} .contenido`).html(renderHTML);

        },

        RenderEstadoRequerimiento: function ( estado ){

            var labelEstado;            
            var claseBG;
            var claseText = 'text-white';

            switch(estado)
            {
                case '1':
                    labelEstado = 'No adjuntado';
                    claseBG = 'danger';
                    break;
                case '2':
                    labelEstado = 'No descargado';
                    claseBG = 'danger';
                    break;
                case '3':
                    labelEstado = 'Pendiente de verificación';
                    claseBG = 'warning';
                    claseText = 'text-dark';
                    break;
                case '4':
                    labelEstado = 'Verificado';
                    claseBG = 'success';
                    break;
                case '5':
                    labelEstado = 'Descargado';
                    claseBG = 'success';
                    break;
                case '6':
                    labelEstado = 'Verificado';
                    claseBG = 'success';
                    break;
                case '7':
                    labelEstado = 'Rechazado';
                    claseBG = 'danger';
                    break;
            }

            labelEstado = `<span class="badge rounded-pill ${claseText} bg-${claseBG} pl-3 pr-3 pt-2 pb-2 font-weight-light d-block">${labelEstado}</span>`;

            return labelEstado;

        },

        TablaHistorico: function(idRelacionRequerimiento, entidad)
        {

            var html=`
            <div class="row">
                <div class="col-12">
                    <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoHistoricoRequerimiento" id="listadoHistoricoRequerimiento" data-order='[[2, "desc"]]'>
                        <thead></thead><tbody></tbody>
                    </table>
                </div>
            </div>`;

            CoreUI.Modal.CustomHTML(html, 'Histórico',function(){documentalCore.renderTablaHistorico(idRelacionRequerimiento, entidad)},'80%', 'row');

        },

        Table:{

        },
    },

    renderTablaHistorico: function(idRelacionRequerimiento, entidad)
    {
        if($('#listadoHistoricoRequerimiento').length)
        {

            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

            //  Titulo
                CoreUI.tableData.addColumn('listadoHistoricoRequerimiento', "nombre","Nombre documento", null, 'text-justify');

            //  Fecha de subida
                CoreUI.tableData.addColumn('listadoHistoricoRequerimiento', function(row, type, val, meta){
                    var html = `<span>${moment(row.created_at).locale('es').format('L')}</span>`;
                    return html;
                }, 'Fecha de subida', null, 'text-center');

            //  Fecha de inclusión al histórico
                CoreUI.tableData.addColumn('listadoHistoricoRequerimiento', function(row, type, val, meta){
                    var html = `<span>${moment(row.created).locale('es').format('L')}</span>`;
                    return html;
                }, 'Fecha Inclusión histórico', null, 'text-center');

            //  Enlace de descarga
                CoreUI.tableData.addColumn('listadoHistoricoRequerimiento', function(row, type, val, meta){
                    var enlaceDescarga = `
                        <a href="${config.baseURL}public/storage/${row.nombrestorage}" class="btnDescargarArchivo" data-idfichero="${row.idfichero}" download="${row.nombre}" target="_blank" title="Ver documento">
                            <i class="bi bi-cloud-arrow-down text-primary mr-1" style="font-size: 24px;"></i>
                        </a>                    
                    `;                   
                    return enlaceDescarga;
                }, '&nbsp;', null, 'text-center','10%');

                $('#listadoHistoricoRequerimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoHistoricoRequerimiento", "Historico", `requerimiento/${entidad}/${idRelacionRequerimiento}/historico`, null,false, false);

        }  
    },

    /** Carga los datos del listado de documentación básica */
    renderTablaDocumentacionBasica: async function()
    {

        if($('#listadoDocumentacionBasica').length)
        {

            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

            //  Titulo
                CoreUI.tableData.addColumn('listadoDocumentacionBasica', "nombre","Nombre documento", null, 'text-justify');

            //  Fichero descarga y/o subida según requiera
            CoreUI.tableData.addColumn('listadoDocumentacionBasica', function(row, type, val, meta)
            {
                
                var canUploadFile = false;
                var salida = '';
                var requiereDescargaPrevia = row.requieredescarga;

                if(row.requieredescarga == '1' && core.Security.getRole() == 'ADMINFINCAS')
                {
                    canUploadFile = true;
                }

            //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                ficheroAdjuntado = (!row.idficherorequerimiento ? false : true);

                //baseURL = 'https://beta.fincatech.es';
                //  Descarga de fichero
                if(typeof row.ficheroscomunes[0] !== 'undefined')
                {
                    var enlaceDescarga = config.baseURL + 'public/storage/' + row.ficheroscomunes[0].nombrestorage;//storageficherorequerimiento;
                    salida += ` <td class="text-center">
                                    <a href="${enlaceDescarga}" class="btnDescargarArchivo" data-idfichero="${row.idfichero}" download="${row.ficheroscomunes[0].nombre}" target="_blank" title="Ver documento">
                                        <i class="bi bi-cloud-arrow-down text-primary mr-1" style="font-size: 24px;"></i>
                                    </a>
                                </td>`;
                }

            //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                if(canUploadFile)
                {  
                    dataset = ` data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad" `;
                    salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a></td>`;
                }

                if(row.requiereDescargaPrevia)
                {
                    //  Comprobamos si el fichero está adjuntado
                    // var enlaceDescarga = config.baseURL + 'public/storage/' + row.ficheroscomunes[0].nombrestorage;//storageficherorequerimiento;
                    // salida += ` <td class="text-center">
                    //                 <a href="${enlaceDescarga}" class="btnDescargarArchivo" data-idfichero="${row.idfichero}" download="${row.ficheroscomunes[0].nombre}" target="_blank" title="Ver documento">
                    //                     <i class="file-earmark-arrow-downn text-primary mr-1" style="font-size: 24px;"></i>
                    //                 </a>
                    //             </td>`;  
                    salida += '<td>&nbsp;</td>';

                }else{
                    salida += '<td>&nbsp;</td>';
                }

                if(!ficheroAdjuntado && !canUploadFile)
                {
                    salida += '<td>&nbsp;</td>';
                }

                return salida;

            }, 'FICHERO', null, 'text-center');

            //  Fichero asociado
                // var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                // CoreUI.tableData.addColumn('listadoDocumentacionBasica', null, "Fichero", html, 'text-center');

                $('#listadoDocumentacionBasica').addClass('no-clicable');
                await CoreUI.tableData.render("listadoDocumentacionBasica", "Requerimiento", "rgpd/documentacionbasica", null, false, false);

        }  

    },

    /** Carga los datos del listado */
    renderTabla: async function()
    {
        if($('#listadoNotasinformativas').length)
        {

            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];
                
            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoNotasinformativas', null, "Fecha", html, 'text-center');

            //  Titulo
                CoreUI.tableData.addColumn('listadoNotasinformativas', "titulo","TITULO", null, 'text-justify');

            //  Descripcion
                CoreUI.tableData.addColumn('listadoNotasinformativas', "descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" download="data:ficheroscomunes.nombre$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoNotasinformativas', null, "Fichero", html, 'text-center');

                // $('#listadoNotasinformativas').addClass('no-clicable');
                await CoreUI.tableData.render("listadoNotasinformativas", "Notasinformativas", "notasinformativas/list");
        }
    },

    /** Refleja la descarga de un fichero por parte de un usuario */
    reflejarDescargaFichero: function(idFichero, idComunidad, idEmpresa, idUsuario)
    {

        var data = Object();
            data.idfichero = idFichero;
            data.idcomunidad = idComunidad;
            data.idempresa = idEmpresa;
            data.idusuario = idUsuario;

        apiFincatech.post('storage/descarga', data).then( (result) =>
        {
            console.log('Descarga reflejada');
            //  Habilitamos el botón de subir fichero
            $('body .btnSubidaFichero').show();
        });

    }

}

$(()=>{
    documentalCore.init();
});