
let TecnicoCertificado = {

    Init: function(){
        TecnicoCertificado.GUI.MostrarListadoDocumentacion(false);
        TecnicoCertificado.Events();
        TecnicoCertificado.Render.ComunidadesCertificadoSolicitado();
    },

    Events: function(){

        /**
         * Selección de comunidad para ver documentos pendientes de validación
         */
        $('body').on(core.helper.clickEventType, '#listadoComunidadesPendientesCertificado tr', function(evt)
        {

            evt.stopImmediatePropagation();
            TecnicoCertificado.GUI.MostrarListadoDocumentacion(false);
            // $('.infoComunidad').hide();
            if($(this).attr('id') === undefined){
                return;
            }

            var idComunidad = window['tablelistadoComunidadesPendientesCertificado'].row( $(this).attr('id')).data().id;
            var nombreComunidad = window['tablelistadoComunidadesPendientesCertificado'].row( $(this).attr('id')).data().nombre;

            $('.nombreComunidad').text(`[ ${nombreComunidad} ]`);

            TecnicoCertificado.Render.ComunidadesDocumentacionAportada(idComunidad);
            TecnicoCertificado.Model.comunidad = nombreComunidad;
            TecnicoCertificado.Model.comunidadId = idComunidad;
        });

        //  Click sobre un documento para ver/actuar sobre él
        $('body').on(core.helper.clickEventType, '.btnCambiarEstadoRequerimiento', function(ev)
        {

            TecnicoCertificado.Model.entidad = $(this).attr('entidad');
            TecnicoCertificado.Model.id = $(this).attr('entidad');
            TecnicoCertificado.Model.caduca = $(this).attr('caduca');
            TecnicoCertificado.GUI.ModalRevisionRequerimiento($(this).attr('data-rowid'), $(this).attr('data-nombrefichero'));
        });

        //  Cambiar el estado a un documento
        $('body').on(core.helper.clickEventType, '.btnGuardarCambioEstadoDocumento', function(ev)
        {

            var resultado = TecnicoCertificado.Validation.EstadoRequerimiento();

            $('body .mensajeError').html('');

            if(resultado == '')
            {
                var _idEstado = $('body input[name=rbEstado]:checked').val();
                var _Observaciones = $('body .observacionesRechazo').val();
                var _fechaCaducidad = $('body .fechaCaducidad').val();
                
                documentalCore.Crud.cambiarEstadoRequerimiento(TecnicoCertificado.Model.documentoSeleccionado, _idEstado, _Observaciones, _fechaCaducidad);
                //  Comprobamos si la comunidad ya tiene todos los requerimientos completados para poder realizar la solicitud
                Swal.close();
                CoreUI.Modal.Success('El estado del requerimiento se ha modificado correctamente','Revisión documental', 
                    (()=>TecnicoCertificado.Validation.DocumentacionObligatoriaAportada(TecnicoCertificado.Model.comunidadId))); 
            }else{
                $('body .mensajeError').html('Errores detectados:<br><br>'+resultado);
            }

        });

        //  Listado comunidades pendientes de validar documentación
        $('body').on(core.helper.clickEventType, '.enlaceComunidades', function(evt){
            TecnicoCertificado.Render.ComunidadesCertificadoSolicitado();
        });

        //  Listado de comunidades con documentación aprobada y pendiente solicitud certificado
        $('body').on(core.helper.clickEventType, '.enlaceCertificadosPendientes', function(evt){
            TecnicoCertificado.Render.CertificadosPendientes();
        });

        //  Listado de comunidades que ya tienen emitido certificado digital
        $('body').on(core.helper.clickEventType, '.enlaceCertificadosSolicitados', function(evt){
            TecnicoCertificado.Render.CertificadosEmitidos();
        });

    },

    GUI: {
        /**
         * Muestra el modal de revisión de requerimiento
         */
        ModalRevisionRequerimiento: function(_rowId, rutaFichero)
        {
            apiFincatech.getView('modals','modal_revision_certificado').then( (result)=>{

                CoreUI.Modal.CustomHTML(result, null, null,'90%');

                var datos = window['tablelistadoComunidadesDocumentosCertificado'].row(_rowId).data();

                TecnicoCertificado.Model.documentoSeleccionado = datos;
                TecnicoCertificado.Model.documentoSeleccionado.entidad = 'certificadorequerimiento';
                TecnicoCertificado.Model.documentoSeleccionado.idrelacionrequerimiento = datos.idrelacion;

                $('body .visorDocumento').attr('src', rutaFichero);

                if(TecnicoCertificado.Model.comunidad != '' && TecnicoCertificado.Model.comunidad)
                {
                    $('body .datosValidacion .nombreComunidad').text(TecnicoCertificado.Model.comunidad);
                }else{
                    console.log('no tiene comunidad');
                    $('body .wrapperComunidad').hide();
                }

                $('body .datosValidacion .nombreDocumento').text(datos.nombre);

                if(datos.caduca == 0)
                {
                    $('body .wrapperFechaCaducidad').hide();
                }else{
                    $('body .datosValidacion #fechaCaducidad').val(datos.fechacaducidad);
                }

            });
        },

        /**
         * Muestra/Oculta el listado de documentación aportada por la comunidad
         * @param {*} _show 
         */
        MostrarListadoDocumentacion: function(_show)
        {
            $('.infoComunidad').removeClass('d-none');
            $('#listadoComunidadesDocumentosCertificado').removeClass('d-none');

            if(!_show){
                $('#listadoComunidadesDocumentosCertificado').addClass('d-none');
                $('.nombreComunidad').text('');
                // $('.infoComunidad').addClass('d-none');
            }else{
                $('.infoComunidad').addClass('d-none');
            }
        }
    },

    Model: {

        entidad: null,
        id: null,
        caduca: null,
        comunidad: null,
        comunidadId: null,
        documentoSeleccionado: Object(),

    },

    Render: {

        /**
         * Carga el listado de comunidades cuya documentación ha sido aprobada pero están pendientes de solicitar certificado
         */
        CertificadosPendientes: function(){
            if($('#listadoCertificadosPendientesSolicitud').length)
            {
    
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Código de comunidad
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     return row.codigo;
                    // },"Cód.", null, 'text-left');

                //  Nombre de la comunidad
                    CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                        return row.nombre;
                    },"Comunidad", null, 'text-left');                

                //  Nombre del administrador
                    CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                        return row.administrador;
                    },"Administrador", null, 'text-left');   

                //  Técnico que ha realizado la aprobación
                    CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                        return row.tecnicoaprobacion;
                    },"Técnico Operador de Registro", null, 'text-left');  
                
                //


                //  Fecha de solicitud
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     return moment(row.fechasolicitud).locale('es').format('L');
                    // },"Fecha de solicitud", null, 'text-center');                

                //  Fecha de aprobación
                    CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                        return moment(row.fechaaprobacion).locale('es').format('L');
                    },"Fecha de aprobación", null, 'text-center');                       

                //  Acción individual
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     let boton = `<a href="javascript:void(0);" data-idsolicitud="${row.id}" class="badge rounded-pill bg-success d-block pt-2 pb-2 text-white btnSolicitarCertificado">Solicitar certificado</a>`;
                    //     return boton;
                    // },'', null, 'text-center');

                    $('#listadoCertificadosPendientesSolicitud').addClass('no-clicable');
                    CoreUI.tableData.render("listadoCertificadosPendientesSolicitud", "comunidad", "certificadodigital/comunidad/pendientessolicitud/list", null, false, false);
            }
        },

        CertificadosEmitidos: function(){
            if($('#listadoCertificadosEmitidos').length)
            {
    
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Nombre de la comunidad
                    CoreUI.tableData.addColumn('listadoCertificadosEmitidos', function(row, type, val, meta){
                        return row.nombre;
                    },"Comunidad", null, 'text-left');                

                //  Nombre del administrador
                    CoreUI.tableData.addColumn('listadoCertificadosEmitidos', function(row, type, val, meta){
                        return row.administrador;
                    },"Representante legal", null, 'text-left');   

                //  Técnico que ha realizado la aprobación
                    CoreUI.tableData.addColumn('listadoCertificadosEmitidos', function(row, type, val, meta){
                        return row.tecnicoaprobacion;
                    },"Técnico Operador de Registro", null, 'text-left');  

                //  Fecha de aprobación
                    CoreUI.tableData.addColumn('listadoCertificadosEmitidos', function(row, type, val, meta){
                        return moment(row.fechaaprobacion).locale('es').format('L');
                    },"Fecha de aprobación", null, 'text-center');                       

                //  ID Solicitud
                    CoreUI.tableData.addColumn('listadoCertificadosEmitidos', function(row, type, val, meta){
                        return row.uanatacaid;
                    },"ID Solicitud", null, 'text-center');   

                //  Acción individual
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     let boton = `<a href="javascript:void(0);" data-idsolicitud="${row.id}" class="badge rounded-pill bg-success d-block pt-2 pb-2 text-white btnSolicitarCertificado">Solicitar certificado</a>`;
                    //     return boton;
                    // },'', null, 'text-center');

                    $('#listadoCertificadosEmitidos').addClass('no-clicable');
                    CoreUI.tableData.render("listadoCertificadosEmitidos", "comunidad", "certificadodigital/comunidad/emitidos/list", null, false, false);
            }
        },

        ComunidadesDocumentacionAportada: async function(idComunidad){

            if($('#listadoComunidadesDocumentosCertificado').length)
            {

                //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

                var tituloColumnaRequerimiento = 'Requerimiento';

                //  Requerimiento
                CoreUI.tableData.addColumn('listadoComunidadesDocumentosCertificado', "requerimiento", tituloColumnaRequerimiento, null, 'text-justify', '30%');

                //  Fecha de caducidad
                CoreUI.tableData.addColumn('listadoComunidadesDocumentosCertificado', function(row, type, val, meta){
                var fechaCaducidad = row.fechacaducidad;
                if(!fechaCaducidad)
                {
                    return 'No caduca';
                }else{
                    return moment(fechaCaducidad).locale('es').format('L');
                }
                },"Fecha de caducidad", null, 'text-center', '30%'); 

                //  Estado del requerimiento
                CoreUI.tableData.addColumn('listadoComunidadesDocumentosCertificado', 
                    function(row, type, val, meta)
                    {
                        if(row.idficherorequerimiento == null)
                        {
                            return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                        }else{
                            // Reflejar motivos estado
                            estadoDocumento = documentalCore.Render.RenderEstadoRequerimiento(row.idestado);
                            return estadoDocumento;
                        }
                    },
                "Estado", null, 'text-center', '10%');

                //  Acción sobre el fichero
                CoreUI.tableData.addColumn('listadoComunidadesDocumentosCertificado', function(row, type, val, meta)
                {
                        var enlaceSalida;
                        var ruta = '';

                        //  Comprobamos si es un .doc o .docx
                        if(!row.nombreficherorequerimiento)
                        {
                            ruta = '';
                        }else{
                            if(row.nombreficherorequerimiento.indexOf('.doc') > 0 )
                            {
                                ruta = 'https://view.officeapps.live.com/op/embed.aspx?src=';
                            }
                        }

                        enlaceSalida = `<p class="text-center mb-0"><a href="javascript:void(0);" data-rowid="${meta.row}" data-idrelacionrequerimiento="${row.idrelacion}" data-caduca="${row.caduca}" data-entidad="certificadorequerimiento" data-nombrefichero="${ruta}${config.baseURL + 'public/storage/'+row.storageficherorequerimiento}" data-nombrestorage="${row.nombreficherorequerimiento}" class="text-center small btnCambiarEstadoRequerimiento"><i class="bi bi-file-earmark-diff text-success" style="font-size:26px;"></i></a></p>`;
                        return enlaceSalida;
    
                }, 'Acción', null, 'text-center');                

                $('#listadoComunidadesDocumentosCertificado').addClass('no-clicable');
                CoreUI.tableData.render("listadoComunidadesDocumentosCertificado", "documentacioncertificado", `comunidad/${idComunidad}/documentacioncertificado`, false, false, false );                

                TecnicoCertificado.GUI.MostrarListadoDocumentacion(true);

            }
        },

        /**
         * Carga el listado de comunidades cuyo administrador ha solicitado el certificado digital
         */
        ComunidadesCertificadoSolicitado: async function(){
            if($('#listadoComunidadesPendientesCertificado').length)
            {
    
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Código de comunidad
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return row.codigo;
                    },"Cód.", null, 'text-left');

                //  Nombre de la comunidad
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return row.nombre;
                    },"Comunidad", null, 'text-left');                

                //  Nombre del administrador
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return row.administrador;
                    },"Representante legal", null, 'text-left');   

                //  CIF del administrador
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return row.documento;
                    },"CIF/NIF", null, 'text-left');  

                //  Teléfono del administrador
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return row.telefono;
                    },"Teléfono", null, 'text-left');  

                //  Fecha de solicitud
                    CoreUI.tableData.addColumn('listadoComunidadesPendientesCertificado', function(row, type, val, meta){
                        return moment(row.fechasolicitud).locale('es').format('L');
                    },"Fecha de solicitud", null, 'text-center');                
    
                    $('#listadoComunidadesPendientesCertificado').addClass('no-clicable');
                    await CoreUI.tableData.render("listadoComunidadesPendientesCertificado", "comunidad", "certificadodigital/comunidad/pendientesvalidacion/list", null, false, false);
            }
        }

    },

    Validation: {

        /**
         * Valida el estado de un requerimiento de certificado digital
         * @returns String Si devuelve nulo es que se ha aprobado el requerimiento con sus valores obligatorios
         */
        EstadoRequerimiento: function(){
            var resultado='';

            //  Validamos que si tiene fecha de caducidad haya proporcionado al menos una fecha superior a 1 semana
            if(TecnicoCertificado.Model.documentoSeleccionado.caduca == 1)
            {

                var fechaCaducidad = $('body .fechaCaducidad').val();
                var fechaActual = new moment().format('YYYY-MM-DD');

                if(fechaCaducidad == '')
                {
                    resultado += 'La Fecha de caducidad no puede estar vacía<br>';
                }else{
                    var given = moment($('body .fechaCaducidad').val(), "YYYY-MM-DD");
                    var current = moment().startOf('day');
                    var duration = moment.duration(given.diff(current)).asDays();
                    // var fechaParseada = moment(fechaCaducidad);
                    // var duration = moment.duration(moment(fecConvert).diff(moment(fechaActual)));
                    // console.log('Días: ' + duration.days());
                    if(duration < 0){
                        resultado += 'La fecha de caducidad no puede ser inferior a la actual<br>';
                    }

                    if(duration >=0 && duration < 10)
                    {
                        resultado += 'La fecha de caducidad no puede ser inferior a 10 días vista<br>';
                    }

                }

            }

            //  Si ha seleccionado "Rechazado", debe rellenar el campo Observaciones
            if( $('body input[name=rbEstado]:checked').val() == '7' && $('body .observacionesRechazo').val() == '')
                resultado += 'Observaciones del motivo de rechazo';

            return resultado;
        },
        
        /**
         * Valida que una comunidad tenga toda la documentación aportada y aprobada relativa a la solicitud del certificado digital
         * @param {*} idComunidad 
         */
        DocumentacionObligatoriaAportada : function(idComunidad)
        {
            apiFincatech.get(`certificadodigital/comunidad/${idComunidad}/validate/documentacion`).then((result)=>{
                let resultado = JSON.parse(result);
                if(resultado.data === 'ok')
                {
                    CoreUI.Modal.Success('La comunidad ya tiene aprobada toda la documentación necesaria para la solicitud del certificado digital.<br><br>En consecuencia el certificado digital ha sido solicitado a UANATACA.', 'Certificado digital',
                    (()=>{
                        //  Recargamos el listado de comunidades que tienen documentación pendiente de aprobar
                        TecnicoCertificado.Render.ComunidadesCertificadoSolicitado();
                        //  Ocultamos el listado de documentación aportada por la comunidad
                        TecnicoCertificado.GUI.MostrarListadoDocumentacion(false);
                    }));
                }
            });
        }
    }
}


$(()=>{
    TecnicoCertificado.Init();
})