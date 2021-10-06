
let documentalCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    idrequerimiento: null,
    idcomunidad: null,
    idempleado: null,
    idempresa: null,
    entidad: null,

    init: async function()
    {

        //  Notas informativas
        if($('#listadoNotasinformativas').length)
        {
            notasInformativasCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;        
        }

        //  Documentación básica
        if($('#listadoDocumentacionBasica').length)
        {
            documentalCore.renderTablaDocumentacionBasica();
        }

        documentalCore.Events();
        core.Files.init();

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

                //  Bindemos el evento del botón procesar importación


            }});

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
                entidad: documentalCore.entidad,
                fichero: core.Files.fichero
            };
// console.log(data);
// console.log('endpoint requerimiento ' + `requerimiento/${documentalCore.entidad}/${documentalCore.idrequerimiento}`);
        await apiFincatech.post(`requerimiento/${documentalCore.entidad}/${documentalCore.idrequerimiento}`, data).then(async (response) =>
        {

            var responseData = JSON.parse(response);

            if(responseData.status['response'] == "ok")
            {
                CoreUI.Modal.Success("El documento se ha registrado correctamente");
                //  Recargamos la tabla de empresas para reflejar el cambio
                    empresaCore.renderTabla();

                //  Recargamos el listado de comunidades
                
                
                //  Actualizamos la tabla del listado de comunidades
                if($('body #listadoComunidad').length)
                {
                    window['tablelistadoComunidad'].ajax.reload();               
                }

                if($('body #listadoDocumentacionComunidad').length)
                {
                    window['tablelistadoDocumentacionComunidad'].ajax.reload();
                    // CoreUI.tableData.tableEventsClick('listadoDocumentacionComunidad');
                }

                if($('body #listadoEmpresa').length)
                    window['tablelistadoEmpresa'].ajax.reload();
          
                if($('body #listadoEmpleadosComunidad').length)
                    window['tablelistadoEmpleadosComunidad'].ajax.reload();

            }else{
                //  TODO: Ver cuál es el error en el json
                Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);

            }

        });


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

                //  Requerimiento
                    CoreUI.tableData.addColumn('listadoDocumentacionComunidad', "requerimiento","Documento", null, 'text-justify', '70%');

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
                                htmlSalida += `<a href="${config.baseURL}public/storage/${row.storagefichero}" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>`;
                            }

                            //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                            if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                            {
                                htmlSalida += `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>`;
                            }

                            return htmlSalida; // row.requerimiento;

                        }, 
                    "Documento", null, 'text-center', '20%');

                    $('#listadoDocumentacionComunidad').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentacionComunidad", "documentacioncomunidad", `comunidad/${id}/documentacioncomunidad`);
            }    
        },

    },

    CAE:{

        verModalAdjuntarFichero: async function(idtiporequerimiento, idempresa)
        {
        
        },

    },

    PRL:{
    
        verModalAdjuntarFichero: async function()
        {
        
        }

    },

    /** Carga los datos del listado de documentación básica */
    renderTablaDocumentacionBasica: async function()
    {
        if($('#listadoDocumentacionBasica').length)
        {

            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();

            //  Titulo
                CoreUI.tableData.addColumn('listadoDocumentacionBasica', "nombre","Nombre documento", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoDocumentacionBasica', null, "Fichero", html, 'text-center');

                $('#listadoDocumentacionBasica').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionBasica", "Requerimiento", "rgpd/documentacionbasica");
        }  
    },

    /** Carga los datos del listado */
    renderTabla: async function()
    {
        if($('#listadoNotasinformativas').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoNotasinformativas', null, "Fecha", html, 'text-center');

            //  Titulo
            CoreUI.tableData.addColumn('listadoNotasinformativas', "titulo","TITULO", null, 'text-justify');

            //  Descripcion
            CoreUI.tableData.addColumn('listadoNotasinformativas', "descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoNotasinformativas', null, "Fichero", html, 'text-center');

                // $('#listadoNotasinformativas').addClass('no-clicable');
                CoreUI.tableData.render("listadoNotasinformativas", "Notasinformativas", "notasinformativas/list");
        }
    },

}

$(()=>{
    documentalCore.init();
});