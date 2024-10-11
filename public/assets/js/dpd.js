
let dpdCore = {

    dpds: Object(),
    dpd: Object(),

    consultaId: null,
    consultaTexto: null,
    consultaRespuesta: null,

    init: async function()
    {

        this.events();
        if($('#listadoDpd').length && core.model == 'Dpd')
        {
            dpdCore.renderTabla();
        }

    },

    events: async function()
    {
        
        //  Si está la lista cargamos los datos
        if($('#listadoDpd').length)
        {
        
        }

        //  Responer consulta de dpd
        $('body').on(core.helper.clickEventType, '.btnResponderConsulta', function()
        {
            //  Recuperamos la consulta por su ID para montar la información en el modal
            apiFincatech.get(`dpd/` + $(this).attr('data-id')).then( (response) =>{
            
                var respuesta = JSON.parse(response); 

                dpdCore.consultaId = $(this).attr('data-id');
                dpdCore.consultaTexto = respuesta.data.Dpd[0].consulta;
                dpdCore.consultaRespuesta = respuesta.data.Dpd[0].respuesta;

                dpdCore.verModalContestarConsulta( dpdCore.consultaId, dpdCore.consultaTexto, dpdCore.consultaRespuesta );

            });
            
        });

        //  Enviar respuesta a consulta
        $('body').on(core.helper.clickEventType, '.btnEnviarRespuestaDPD', function()
        {
            dpdCore.responderConsulta($(this).attr('data-id'));
        });

        //  Enviar consulta al DPD
        $('body').on(core.helper.clickEventType, '.btnEnviarConsultaDPD', function()
        {
            dpdCore.crearConsulta();
        });

        //  Crear consulta
        $('body').on(core.helper.clickEventType, '.btnCrearConsultaDPD', function(e)
        {
            dpdCore.verModalCrearConsulta();
        });

        //  Cancelar consulta
        $('body').on(core.helper.clickEventType, '.btnCancelarConsultaDPD', function(e)
        {
            Swal.close();
        });

        //  DPD Exportar
        $('body').on(core.helper.clickEventType, '.dpd-exportar', function(e){
            dpdCore.Controller.ExportarAdministradoresDPD();
        });

    },

    Controller:{

        /**
         * Exporta los administradores 
         */
        ExportarAdministradoresDPD: function()
        {

            Swal.fire({
                title: 'Exportación de administradores',
                html: `<p>Clique el botón <strong>descargar</strong> para generar y descargar un fichero Excel con la información de todos los administradores que tienen contratado el servicio DPD para al menos 1 comunidad.</p>`,
                grow:'false',
                width: '60rem',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-save mr-2"></i> Descargar',
                cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    actions: 'text-center p-3 shadow-inset rounded-pill border-light border-2 bg-white',
                    confirmButton: 'btn btn-success rounded-pill shadow d-block pb-2 pt-2 cofirmButtonModal',
                    cancelButton: ' btn btn-danger btnCancelSave rounded-pill shadow d-block pb-2 pt-2 mr-3 cacelButtonModal',
                    popup: 'bdg-transparent'
                }            
              }).then((result) => {
                if (result.isConfirmed) 
                {
                    //  Llamamos al endpoint de exportar
                    apiFincatech.get(`administrador/exportar/dpd`).then( ( result ) =>
                    {
          
                        let res = JSON.parse(result);
                        if(res.data == 'Error')
                        {
                            CoreUI.Modal.Error(res.status.error,'Exportación de administradores');
                        }else{
                            // Decodificar el Base64
                            let binaryData = atob(res.data);
    
                            // Crear un array de bytes
                            let bytes = new Uint8Array(binaryData.length);
                            for (let i = 0; i < binaryData.length; i++) {
                                bytes[i] = binaryData.charCodeAt(i);
                            }
    
                            // Crear un Blob
                            let blob = new Blob([bytes], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    
                            // Crear un enlace de descarga
                            let a = document.createElement("a");
                            a.href = window.URL.createObjectURL(blob);
    
                            // Establecer el nombre del archivo
                            a.download = `exportacion_administradores_dpd_contratado_${moment().format('YYYY_MM_DD')}.xlsx`;
    
                            // Simular clic en el enlace para descargar el archivo
                            a.click();
                            //  Quitamos el enlace que hemos creado
                            a.remove();
                        }                       
                    });
                }
            }); 
        }

    },

    /**
     * Muestra el modal para generar una nueva consulta al DPD
     */
    verModalCrearConsulta: function()
    {
        var html = `
            <div class="row">
                <div class="col-12 text-left">
                    <p><i data-feather="message-square"></i> NUEVA CONSULTA AL DPD</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <textarea id="textoconsulta" rows="10" name="textoconsulta" class="textoconsulta form-control shadow-inset border-0 rounded mb-3"></textarea>
                </div>
            </div>            
            <div class="row">
                <div class="col-6 pr-1">
                    <a href="javascript:void(0);" class="btn btn-danger d-block shadow d-block pb-2 pt-2 btnCancelarConsultaDPD"><i class="bi bi-x-circle"></i> Cancelar</a>
                </div>
                <div class="col-6 pl-1">
                    <a href="javascript:void(0);" class="btn btn-success d-block shadow d-block pb-2 pt-2 btnEnviarConsultaDPD"><i class="bi bi-check-square"></i> Enviar consulta</a>
                </div>
            </div>
        `;
        CoreUI.Modal.CustomHTML(html,'', function()
        {
            dpdCore.renderTabla();
        });
    },

    /**
     * Muestra el modal para contestar una consulta al DPD
     */
    verModalContestarConsulta: function(id, textoConsulta, textoRespuesta)
    {
        if(textoRespuesta == '' || textoRespuesta === null)
        {
            textoRespuesta = '';
        }
        var html = `
            <div class="row">
                <div class="col-12 text-center shadow-neumorphic mb-3 rounded-lg p-2">
                    <p class="display-6 m-0"><i data-feather="message-square"></i> CONSULTA DPD</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-left">
                    <label for="textoconsulta" class="mb-2 text-uppercase">Consulta</label>
                    <div class="alert alert-warning rounded shadow-neumorphic" style="border-radius: 10px !important;" role="alert">                         
                        <div class="alert-message text-justify" style="font-size: 14px;">${textoConsulta}</div>
                    </div>                
                </div>
            </div>            
            <div class="row">
                <div class="col-12 text-left">
                    <label for="textoconsulta" class="mb-2 text-uppercase">Respuesta</label>
                    <textarea id="textoconsulta" rows="10" name="textoconsulta" class="textoconsulta form-control shadow-inset border-0 rounded mb-3">${textoRespuesta}</textarea>
                </div>
            </div>    
            <div class="row">
                <div class="col-12 text-left">
                    <p class="mb-1 text-left text-uppercase font-weight-normal pr-3 pt-2" style="font-size:14px;">Adjuntar fichero</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-3 shadow-inset border-1 pt-3 pb-2">
                        <div class="col-1 align-self-center h-100 text-center">
                            <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
                        </div>
                        <div class="col-11 pl-0">
                            <input accept=".pdf" class="form-control form-control-sm ficheroadjuntar border-0" hs-fichero-entity="Dpd" id="ficheroadjuntar" type="file">
                        </div>       
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 pr-1">
                    <a href="javascript:void(0);" class="btn btn-danger d-block shadow d-block pb-2 pt-2 btnCancelarConsultaDPD"><i class="bi bi-x-circle"></i> Cancelar</a>
                </div>
                <div class="col-6 pl-1">
                    <a href="javascript:void(0);" class="btn btn-success d-block shadow d-block pb-2 pt-2 btnEnviarRespuestaDPD" data-id="${id}"><i class="bi bi-check-square"></i> Enviar respuesta</a>
                </div>
            </div>
        `;
        CoreUI.Modal.CustomHTML(html,'', null, '50%').then( () =>{
            
        });
        core.Files.init();
        core.Files.Fichero.entidadId = id;
    },

    /**
     * Envía una consulta al DPD
     */
    crearConsulta: async function()
    {
        //  Validamos que el usuario haya escrito texto en el campo correspondiente
        if($('body .textoconsulta').val() == '')
        {
            CoreUI.Modal.Error("Escribe la consulta para poder enviarla", "Error", function()
            {
                dpdCore.verModalCrearConsulta();
            });
        }else{
            var postData = Object();
            postData = {
                idcomunidad: $('body').attr('hs-model-id'),
                consulta: $('body .textoconsulta').val()
            }

            //  Enviamos la consulta al endpoint
            await apiFincatech.post(`dpd/create`, postData).then(async (response) =>
            {

                var responseData = JSON.parse(response);

                if(responseData.status['response'] == "ok")
                {
                    CoreUI.Modal.Success("La consulta ha sido registrada correctamente");
                    //  Recargamos la tabla de consultas al dpd
                    dpdCore.renderTabla();
                }else{
                    //  TODO: Ver cuál es el error en el json
                    Modal.Error("No se ha podido enviar la consulta por el siguiente motivo:<br><br>" + responseData.status.response);

                }

            });
        }

    },

    /**
     * Responde a una consulta pendiente de responder
     * @param {*} consultaId 
     */  
    responderConsulta: async function(consultaId)
    {
        if($('body .textoconsulta').val() == '')
        {
            CoreUI.Modal.Error("Escribe la respuesta para poder contestar la consulta", "Error", function()
            {
                dpdCore.verModalContestarConsulta(dpdCore.consultaId, dpdCore.consultaTexto, dpdCore.consultaRespuesta);
            });
        }else{
            var postData = Object();
            postData = {
                id: consultaId,
                respuesta: $('body .textoconsulta').val(),
                fecharesolucion: moment().format("Y-MM-DD HH:mm"),
                solucionado: '1'
            }

            //  Enviamos la consulta al endpoint
            await apiFincatech.put(`dpd/${consultaId}`, postData).then(async (response) =>
            {

                var responseData = JSON.parse(response);

                if(responseData.status['response'] == "ok")
                {
                    CoreUI.Modal.Success("La respuesta ha sido registrada correctamente",null, function()
                    {
                        //  Recargamos la tabla de consultas al dpd
                            // dpdCore.renderTabla();
                        window['tablelistadoDpd'].ajax.reload()
                    });
                }else{
                    //  TODO: Ver cuál es el error en el json
                    Modal.Error("No se ha podido enviar la consulta por el siguiente motivo:<br><br>" + responseData.status.response);

                }

            });
        }
    },

    /**
     * Carga los datos del listado de consultas al dpd 
     */
    renderTabla: async function()
    {
        if($('#listadoDpd').length)
        {

            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];
                
            //  Comunidad
            // CoreUI.tableData.addColumn('listadoDpd', "comunidad[0].nombre","COMUNIDAD");

            if(core.Security.getRole() == 'DPD')
            {
                //  Administrador
                CoreUI.tableData.addColumn('listadoDpd', 
                    function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        htmlSalida = `<p class="mb-0">${row.usuario[0].nombre}</p>`;
                        return htmlSalida; // row.requerimiento;

                    }, 
                    "Administrador", null, 'text-left');

                //  E-mail
                CoreUI.tableData.addColumn('listadoDpd', 
                    function(row, type, val, meta)
                    {
                        var htmlSalida = '';
                        htmlSalida = `<p class="mb-0 text-left"><small>${row.usuario[0].email}</small></p>`;

                        return htmlSalida; // row.requerimiento;

                    }, 
                    "E-mail", null, 'text-center');                    
            }

            //  Consulta
            CoreUI.tableData.addColumn('listadoDpd', "consulta", "Consulta");

            //  Respuesta
            CoreUI.tableData.addColumn('listadoDpd', "respuesta", "Respuesta");

            //  Fecha resolución 
            // CoreUI.tableData.addColumn("fecharesolucion", "Fecha esResolución");

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoDpd', null, "Fecha", html, 'text-center', '80px');

            //  Fichero asociado
            CoreUI.tableData.addColumn('listadoDpd', 
                function(row, type, val, meta)
                {
                    var ficheroAdjuntado = false;
                    var htmlSalida = '';
                    var estado = '';

                    //  Enlace de descarga
                    if(row.idfichero != null)
                    {
                        ficheroAdjuntado = true;
                        //  Tiene fichero ya subido
                        htmlSalida += `<a href="${config.baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" target="_blank" download="${row.ficheroscomunes[0].nombre}" ><i class="bi bi-file-earmark-arrow-down" style="font-size:24px;"></i></a>`;
                    }

                    //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                    return htmlSalida; // row.requerimiento;

                }, 
                "Fichero", null, 'text-center');

            //  Estado
                var html = 'data:solucionado$';
                CoreUI.tableData.addColumn('listadoDpd', null, "Estado", html, 'text-center');

                if($('body').attr('hs-role') == 'DPD')
                {
                    //  Columna de acciones
                        var html = '<ul class="nav justify-content-center accionesTabla">';
                            html += '<li class="nav-item"><a href="javascript:void(0);" class="btnResponderConsulta d-inline-block" data-id="data:id$"><i data-feather="send" class="text-info img-fluid"  style="height:26px; width: 26px;"></i></a></li>';
                            CoreUI.tableData.addColumn('listadoDpd', null, "", html, 'text-center');
                }

            $('#listadoDpd').addClass('no-clicable');
            await CoreUI.tableData.render("listadoDpd", "Dpd", "dpd/list");
        }
        return true;
    }


}

$(()=>{
    dpdCore.init();
});