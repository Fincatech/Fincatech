
let documentalCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoNotasinformativas').length)
        {
            notasInformativasCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;        
        }

        if($('#listadoDocumentacionBasica').length)
        {
            documentalCore.renderTablaDocumentacionBasica();
        }

    },

    events: async function()
    {   
    },

    renderTablaDocumentacionBasica: async function()
    {
          if($('#listadoDocumentacionBasica').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                // var html = 'data:created$';
                // CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');

            //  Titulo
            CoreUI.tableData.addColumn("nombre","Nombre documento", null, 'text-justify');

            //  Descripcion
            // CoreUI.tableData.addColumn("descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn(null, "Fichero", html, 'text-center');

                $('#listadoDocumentacionBasica').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionBasica", "Requerimiento", "documentacion/basica/list");
        }  
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoNotasinformativas').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');

            //  Titulo
            CoreUI.tableData.addColumn("titulo","TITULO", null, 'text-justify');

            //  Descripcion
            CoreUI.tableData.addColumn("descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn(null, "Fichero", html, 'text-center');

                // $('#listadoNotasinformativas').addClass('no-clicable');
                CoreUI.tableData.render("listadoNotasinformativas", "Notasinformativas", "notasinformativas/list");
        }
    }


}

$(()=>{
    documentalCore.init();
});