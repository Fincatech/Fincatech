
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
            e.preventDefault();
            documentalCore.uploadRequerimiento();
        });

        $('body').on(core.helper.clickEventType, '.btnAdjuntarFicheroDocumento', async function()
        {

            console.log('idcomunidad: ' +  $(this).attr('data-idcomunidad'));
            console.log('idempresa = ' + $(this).attr('data-idempresa'));
            console.log('.idempleado = ' + $(this).attr('data-idempleado'));
            console.log('.idrequerimiento = ' + $(this).attr('data-idrequerimiento'));
            console.log('.idrelacionrequerimiento = ' + $(this).attr('data-idrelacionrequerimiento'));
            console.log('.entidad = ' + $(this).attr('data-entidad'));

            documentalCore.idcomunidad = $(this).attr('data-idcomunidad');
            documentalCore.idempresa = $(this).attr('data-idempresa');
            documentalCore.idempleado = $(this).attr('data-idempleado');
            documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');
            documentalCore.idrelacionrequerimiento = $(this).attr('data-idrelacionrequerimiento');
            documentalCore.entidad = $(this).attr('data-entidad');

            console.log('documentalCore.idcomunidad ' + documentalCore.idcomunidad);
            console.log('documentalCore.idempresa : ' + documentalCore.idempresa) ;
            console.log('documentalCore.idempleado  ' + documentalCore.idempleado);
            console.log('documentalCore.idrequerimiento  ' + documentalCore.idrequerimiento);
            console.log('documentalCore.idrelacionrequerimiento  ' + documentalCore.idrelacionrequerimiento);
            console.log('documentalCore.entidad ' + documentalCore.entidad);

            const { value: file } = await Swal.fire({
            title: '',
            html: Constantes.CargaDocumento,
            showCancelButton: false,
            showConfirmButton: false,
            // grow: 'row',
            showCloseButton: true,
            didOpen: function(e)
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
            }else{
                //  TODO: Ver cuál es el error en el json
                Modal.Error("No se ha podido registrar el documento por el siguiente motivo:<br><br>" + responseData.status.response);

            }

        });


    },

    COMUNIDAD: {
    
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

    /**  */
    renderTablaDocumentacionComunidad: async function(id)
    {
        if($('#listadoDocumentacionComunidad').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoDocumentacionComunidad', null, "Fecha", html, 'text-center');

            //  Titulo
                CoreUI.tableData.addColumn('listadoDocumentacionComunidad', "titulo","TITULO", null, 'text-justify');

            //  Descripcion
                CoreUI.tableData.addColumn('listadoDocumentacionComunidad', "descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoDocumentacionComunidad', null, "Fichero", html, 'text-center');

                // $('#listadoNotasinformativas').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionComunidad", "Notasinformativas", "notasinformativas/list");
        }    
    },

}

$(()=>{
    documentalCore.init();
});