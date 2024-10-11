let SeguimientoProveedores = {

    Init: function() {

        if( $('body #listadoEmpresasRegistradas').length > 0)
        {
            SeguimientoProveedores.Controller.RenderTablaSeguimientoProveedores();
        }

        if($('body #listadoSeguimientoEmpresas').length > 0)
        {
            SeguimientoProveedores.Controller.RenderTablaActuaciones();

            $('body').on('hsFormDataLoaded', function(ev, formularioId){
                SeguimientoProveedores.View.RenderEstadoActuacion();
            });
        }

        SeguimientoProveedores.Events();

    },

    Events: function(){

        //  Creación nueva actuación
        $('body').on(core.helper.clickEventType, '.btnSaveActuacion', function(evt){
            //  Generamos la actuación para el proveedor
            SeguimientoProveedores.Model.fecha = $('#seguimientoFecha').val();
            SeguimientoProveedores.Model.tipo = $('#seguimientoTipo').val();
            SeguimientoProveedores.Model.observaciones = $('#seguimientoObservaciones').val();
            SeguimientoProveedores.Controller.Create();
        });

        //  Limpieza formulario de actuacion
        $('body').on(core.helper.clickEventType, '.btnResetActuacion', function(evt){
            SeguimientoProveedores.Controller.ResetForm();
        });

        //  Eliminación de actuación
        $('body').on(core.helper.clickEventType, '.btnDeleteSeguimiento', function(evt){
            SeguimientoProveedores.Model.actuacionId = $(this).attr('data-id');
            SeguimientoProveedores.Controller.Delete();
        });

        //  Finalización de actuacion
        $('body').on(core.helper.clickEventType, '.btnFinishSeguimiento', function(ev){
            if(window['tablelistadoSeguimientoEmpresas'].tables(0).data().length <= 0)
            {
                CoreUI.Modal.Error('Para finalizar el seguimiento debe haber al menos 1 actuación registrada');
            }else{
                SeguimientoProveedores.Controller.SetFinishedFollow();
            }
        });

    },

    Controller: {

        ResetForm: function()
        {
            $('#seguimientoFecha').val('');
            $('#seguimientoTipo').val('');
            $('#seguimientoObservaciones').val('');
            SeguimientoProveedores.Model.fecha = '';
            SeguimientoProveedores.Model.tipo = '';
            SeguimientoProveedores.Model.observaciones = '';
        },

        Create: async function()
        {
            let validacion = SeguimientoProveedores.Validator.Creation();
            if(validacion === true)
            {
                let data = Object();
                data.fecha = SeguimientoProveedores.Model.fecha;
                data.tipo = SeguimientoProveedores.Model.tipo;
                data.observaciones = SeguimientoProveedores.Model.observaciones;
                await apiFincatech.post(`empresa/${core.modelId}/actuacion`,data).then((result)=>{
                    // Avisamos al usuario de la inserción
                    CoreUI.Modal.Success('La actuación se ha generado correctamente');
                    // Recargamos el listado
                    SeguimientoProveedores.Controller.RenderTablaActuaciones();
                    SeguimientoProveedores.Controller.ResetForm();
                });
            }else{
                CoreUI.Modal.Error('<p class="text-left">Para guardar la actuación, corrija los siguientes errores<br><br>' + validacion + '</p>','Error');
            }
        },

        Delete: async function()
        {
            Swal.fire({
                title: `¿Desea eliminar la actuación seleccionada?`,
                text:  'Esta acción es irreversible',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                    //  Llamamos al endpoint de eliminar
                    apiFincatech.delete(`empresa/${core.modelId}/actuacion`, SeguimientoProveedores.Model.actuacionId).then((result) =>{
                        Swal.fire(
                            'Actuación eliminada correctamente',
                            '',
                            'success'
                          );
                          CoreUI.Modal.Success('La actuación se ha eliminado correctamente');
                          SeguimientoProveedores.Controller.RenderTablaActuaciones();
                   
                    });
                }
            });            
        },

        SetFinishedFollow: async function()
        {
            Swal.fire({
                title: `¿Desea dar por terminado el seguimiento para esta empresa?`,
                text:  'Esta acción es irreversible',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Terminar seguimiento',
                cancelButtonText: 'Cancelar'
              }).then((result) => {
                if (result.isConfirmed) {
                    //  Llamamos al endpoint de eliminar
                    apiFincatech.get(`empresa/${core.modelId}/seguimiento/finished`).then((result) =>{
                        CoreUI.Modal.Success('El seguimiento se ha finalizado correctamente');
                        core.Modelo.entity.Empresa[0].estadoprotocolo = '1';
                        SeguimientoProveedores.View.RenderEstadoActuacion();
                   
                    });
                }
            });  
        },

        /**
         * Renderiza la tabla de empresas susceptibles de seguimiento
         */
        RenderTablaSeguimientoProveedores: function() 
        {
            if($('#listadoEmpresasRegistradas').length)
            {
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                //  Nombre
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "razonsocial", "Empresa", null, 'text-left');

                //  CIF
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "cif", "CIF", null, 'text-left');

                //  E-mail
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "email", "E-mail", null, 'text-left');

                //  Teléfono
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "telefono", "Teléfono", null, 'text-left');

                //  Número de actuaciones
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "totalactuaciones", "Nº Actuaciones realizadas", null, 'text-center');
                    
                //  Fecha última actuación
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', function(row, type, val, meta)
                    {                     
                        let fecha = '-';
                        let timeStamp = '';
                        if(row.actuaciones.length > 0)
                        {
                            timeStamp = moment(row.actuaciones[0].created, 'YYYY-MM-DD hh:mm').unix();
                            fecha = moment(row.created).locale('es').format('L');
                        }
                        return `<span style="display:none;">${timeStamp}</span><p class="mb-0 text-center">${fecha}</p>`;
                    }, "Última actuación", null, 'text-center', '10%');  
                    
                //  Estado protocolo
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', function(row, type, val, meta)
                    {                     
                    //  Renderizamos el icono de descarga junto con la fecha
                        let protocolo = row.estadoprotocolo == '1' ? `<span class="badge badge-pill bg-success">Finalizado</span>` : '<span class="badge badge-pill bg-primary">En seguimiento</span>';
                        return `<p class="mb-0 text-center">${protocolo}</p>`;
                    },"Estado Protocolo", null, 'text-center');                  

                //  Solo para debug -> ID
                    // CoreUI.tableData.addColumn('listadoEmpresasRegistradas', "id","ID", null, 'text-left');

                //  Fecha de creación
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', function(row, type, val, meta)
                    {                     
                    //  Renderizamos el icono de descarga junto con la fecha
                        timeStamp = moment(row.created, 'YYYY-MM-DD hh:mm').unix();
                        return `<span style="display:none;">${timeStamp}</span><p class="mb-0 text-center">${moment(row.created).locale('es').format('L')}</p>`;
                    }, "Fecha de registro", null, 'text-center', '10%');      


                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += '<li class="nav-item"><a href="empresa/data:id$" class="btnSeguimiento d-inline-block" data-id="data:id$"><i data-feather="edit" class="text-success img-fluid"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoEmpresasRegistradas', null, "", html);
    
                    $('#listadoEmpresasRegistradas').addClass('no-clicable');
                    CoreUI.tableData.render("listadoEmpresasRegistradas", "empresas", `empresa/seguimiento/list`, false, true, true);
            }
        },

        /**
         * Renderiza la tabla de actuaciones sobre una empresa
         */
        RenderTablaActuaciones: function()
        {
            if($('#listadoSeguimientoEmpresas').length)
            {
                let empresaId = core.modelId;
                //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

                //  Fecha
                CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', function(row, type, val, meta)
                {                     
                //  Renderizamos el icono de descarga junto con la fecha
                    timeStamp = moment(row.created, 'YYYY-MM-DD hh:mm').unix();
                    return `<span style="display:none;">${timeStamp}</span><p class="mb-0 text-center">${moment(row.created).locale('es').format('L')}</p>`;
                }, "Fecha", null, 'text-center', '15%');

                //  Tipo
                CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', function(row, type, val, meta)
                {                     
                //  Renderizamos el icono de descarga junto con la fecha
                    if(!row.subject){
                        return row.tipo;
                    }else{
                        return 'E-mail';
                    }
                }, "Tipo", null, 'text-center','15%');

                // CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', "tipo", "Tipo", null, 'text-left', '15%');
                //  Observaciones
                CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', function(row, type, val, meta)
                {                     
                //  Renderizamos el icono de descarga junto con la fecha
                    if(!row.subject){
                        return row.observaciones;
                    }else{
                        return 'Envío E-mail de Alta en la plataforma';
                    }
                }, "Observaciones", null, 'text-left');                
                // CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', "observaciones", "Observaciones", null, 'text-left');

                //  Columna de acciones
                CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', function(row, type, val, meta)
                {                     
                //  Renderizamos el icono de descarga junto con la fecha
                    if(!row.subject){
                        let html = '<ul class="nav justify-content-center accionesTabla">';
                        html += '<li class="nav-item"><a href="javascript:void(0);"" class="btnDeleteSeguimiento d-inline-block" data-id="data:id$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                        return html;
                    }else{
                        return '';
                    }
                }, "&nbsp;", null, 'text-left');                   
                // CoreUI.tableData.addColumn('listadoSeguimientoEmpresas', null, "", html);

                $('#listadoSeguimientoEmpresas').addClass('no-clicable');
                CoreUI.tableData.render("listadoSeguimientoEmpresas", "actuaciones", `empresa/${empresaId}/seguimiento`, false, false, false);

            }
        }

    },

    Model: {
        actuacionId: null,
        fecha: null,
        tipo: null,
        observaciones: null,
    },

    Validator: {

        /**
         * Valida los datos obligatorios para la creación de una actuación en el sistema
         * @returns 
         */
        Creation: function()
        {
            let res = '';
            if(SeguimientoProveedores.Model.fecha == '')
                res = '- Fecha de la actuación<br>';

            if(SeguimientoProveedores.Model.tipo == '')
                res = `${res}- Tipo de actuación realizada<br>`;

            if(SeguimientoProveedores.Model.observaciones == '')
                res = `${res}- Observaciones<br>`;

            return (res == '' ? true : res);
        }

    },

    View:{
        RenderEstadoActuacion: function()
        {
            $('#estadoActuacion').removeClass();
            let estadoTexto;
            let clases;
            if(core.Modelo.entity.Empresa[0].estadoprotocolo == '1')
            {
                clases = 'bg-success';
                estadoTexto = 'Seguimiento Finalizado';
                $('.btnResetActuacion').addClass('disabled');
                $('.btnSaveActuacion').addClass('disabled');
                $('.btnFinishSeguimiento').addClass('disabled');
            }else{
                estadoTexto = 'En seguimiento';
                clases = 'bg-primary';
            }
            $('#estadoActuacion').html(estadoTexto);
            $('#estadoActuacion').addClass(clases).addClass('badge badge-pill p-2');
        }
    }

}

$(()=>{
    SeguimientoProveedores.Init();
})