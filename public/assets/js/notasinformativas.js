
let notasInformativasCore = {

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

    },

    events: async function()
    {   
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
                CoreUI.tableData.addColumn('listadoNotasinformativas', null, "Fecha", html, 'text-center', '80px');

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
    }


}

$(()=>{
    notasInformativasCore.init();
});