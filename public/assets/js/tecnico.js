let tecnicoRevision = {

    Init: function(){
        tecnicoRevision.Events();
        if($('body #listadoDocumentosPendientesVerificacion').length > 0)
        {
            tecnicoRevision.GUI.RenderTablaDocumentosPendientesVerificacion();
        }
    },

    Events: function(){

        //  Click sobre un documento para ver/actuar sobre él
        $('body').on(core.helper.clickEventType, '.btnCambiarEstadoRequerimiento', function(ev)
        {

            tecnicoRevision.Model.entidad = $(this).attr('entidad');
            tecnicoRevision.Model.id = $(this).attr('entidad');
            tecnicoRevision.Model.caduca = $(this).attr('caduca');
            tecnicoRevision.GUI.ModalRevisionRequerimiento($(this).attr('data-rowid'), $(this).attr('data-nombrefichero'));
        });

        //  Cambiar el estado a un documento
        $('body').on(core.helper.clickEventType, '.btnGuardarCambioEstadoDocumento', function(ev)
        {
            var resultado = tecnicoRevision.Model.ValidarEstadoRequerimiento();

            $('body .mensajeError').html('');

            if(resultado == '')
            {
                var _idEstado = $('body input[name=rbEstado]:checked').val();
                var _Observaciones = $('body .observacionesRechazo').val();
                var _fechaCaducidad = $('body .fechaCaducidad').val();

                documentalCore.Crud.cambiarEstadoRequerimiento(tecnicoRevision.Model.documentoSeleccionado, _idEstado, _Observaciones, _fechaCaducidad);
            }else{
                $('body .mensajeError').html('Errores detectados:<br><br>'+resultado);
            }

        });
    },

    Model: {

        entidad: null,
        id: null,
        caduca: null,
        comunidad: null,
        documentoSeleccionado: Object(),
        /**
         * Cambia el estado de un requerimiento
         * @param {*} _entidad 
         * @param {*} _idrequerimiento 
         */
        CambiarEstadoRequerimiento: function(_entidad, _idrequerimiento)
        {


        },

        ValidarEstadoRequerimiento: function()
        {
            var resultado='';

            //  Validamos que si tiene fecha de caducidad haya proporcionado al menos una fecha superior a 1 semana
            if(tecnicoRevision.Model.documentoSeleccionado.caduca == 1)
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
        }

    },

    GUI: {
        /**
         * Render tabla documentos pendientes verificación
         */
        RenderTablaDocumentosPendientesVerificacion: async function()
        {

            if($('#listadoDocumentosPendientesVerificacion').length)
            {
    
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Fecha de creación
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', function(row, type, val, meta){
                        return moment(row.created).locale('es').format('L');
                    },"Fecha de subida", null, 'text-center');

                //  Fecha de caducidad
                //  Fecha de creación
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', function(row, type, val, meta){
                        var fechaCaducidad = row.fechacaducidad;
                        if(!fechaCaducidad)
                        {
                            return 'N/D';
                        }else{
                            return moment(row.fechacaducidad).locale('es').format('L');
                        }
                    },"Fecha de caducidad", null, 'text-center');                

                //  Nombre requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', "nombrerequerimiento","Nombre Requerimiento", null, 'text-justify');

                //  Nombre empresa
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', "nombreempresa","Empresa", null, 'text-justify');

                //  Empleado
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', "nombreempleado","Empleado", null, 'text-justify');

                //  Comunidad
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', "comunidad","Comunidad", null, 'text-justify');

                //  CIF
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', "cif","NIF/CIF", null, 'text-justify');

                //  Acción sobre el fichero
                CoreUI.tableData.addColumn('listadoDocumentosPendientesVerificacion', function(row, type, val, meta)
                {
                        var enlaceSalida;
                        var ruta = '';

                        //  Comprobamos si es un .doc o .docx
                        if(row.nombrestorage.indexOf('.doc') > 0 )
                        {
                            ruta = 'https://view.officeapps.live.com/op/embed.aspx?src=';
                        }

                        enlaceSalida = `<p class="text-center mb-0"><a href="javascript:void(0);" data-rowid="${meta.row}" data-idrelacionrequerimiento="${row.idrelacionrequerimiento}" data-caduca="${row.caduca}" data-entidad="${row.entidad}" data-nombrefichero="${ruta}${config.baseURL + 'public/storage/'+row.nombrestorage}" data-nombrestorage="${row.nombrefichero}" class="text-center small btnCambiarEstadoRequerimiento"><i class="bi bi-file-earmark-diff text-success" style="font-size:26px;"></i></a></p>`;
                        return enlaceSalida;
    
                }, 'Acción', null, 'text-center');
    
                    $('#listadoDocumentosPendientesVerificacion').addClass('no-clicable');
                    await CoreUI.tableData.render("listadoDocumentosPendientesVerificacion", "Requerimiento", "revision/pendientes", null, false, false);
    
            }  
        },

        /**
         * Muestra el modal de revisión de requerimiento
         */
        ModalRevisionRequerimiento: function(_rowId, rutaFichero)
        {
            apiFincatech.getView('modals','modal_revision_requerimiento').then( (result)=>{

                CoreUI.Modal.CustomHTML(result, null, null,'90%');

                var datos = window['tablelistadoDocumentosPendientesVerificacion'].row(_rowId).data();
                tecnicoRevision.Model.documentoSeleccionado = datos;
                
                $('body .visorDocumento').attr('src', rutaFichero);

                if(datos.comunidad != '' && datos.comunidad)
                {
                    $('body .datosValidacion .nombreComunidad').text(datos.comunidad);
                }else{
                    console.log('no tiene comunidad');
                    $('body .wrapperComunidad').hide();
                }

                if(datos.nombreempresa != '')
                {
                    $('body .datosValidacion .nombreEmpresa').text(datos.nombreempresa);
                }else{
                    $('body .wrapperEmpresa').hide();
                }

                if(datos.nombreempleado != '')
                {
                    $('body .datosValidacion .nombreEmpleado').text(datos.nombreempleado);
                }else{
                    $('body .wrapperEmpleado').hide();

                }

                $('body .datosValidacion .nombreDocumento').text(datos.nombrerequerimiento);

                if(datos.caduca == 0)
                {
                    $('body .wrapperFechaCaducidad').hide();
                }else{
                    $('body .datosValidacion .fechaCaducidad').text(datos.fechacaducidad);
                }

                $('body .datosValidacion .cif').text(datos.cif);   

            });
        },
    }

}

$(()=>{
    tecnicoRevision.Init();
});