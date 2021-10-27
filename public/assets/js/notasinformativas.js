
let notasInformativasCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoNotasInformativas').length)
        {
            // notasInformativasCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;        
        }

    },

    events: async function()
    {   
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoNotasInformativas').length)
        {
            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];
                
            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoNotasInformativas', null, "Fecha", html, 'text-center', '80px');

            //  Titulo
                CoreUI.tableData.addColumn('listadoNotasInformativas', "titulo","TITULO", null, 'text-justify');

            //  Descripcion
                CoreUI.tableData.addColumn('listadoNotasInformativas', "descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoNotasInformativas', null, "Fichero", html, 'text-center');

                // $('#listadoNotasinformativas').addClass('no-clicable');
                await CoreUI.tableData.render("listadoNotasInformativas", "Notasinformativas", "notasinformativas/list");
        }
    }


}

$(()=>{
    notasInformativasCore.init();
});