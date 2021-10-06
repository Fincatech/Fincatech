
let requerimientoCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoRequerimiento').length)
        {
            requerimientoCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;       
 
        }

        if( $('#listadoDocumentacionContratosCesion').length)
            requerimientoCore.renderTablaDocumentacionContratosCesion();

        if( $('#listadoContratosCesion'))
            requerimientoCore.renderTablaContratosCesion();

        if( $('#listadoDocumentacionCamarasSeguridad'))
            requerimientoCore.renderTablaDocumentacionCamarasSeguridad();

        if( $('#listadoCamarasSeguridad'))
            requerimientoCore.renderTablaCamarasSeguridad();
    

    },

    events: async function()
    {   
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoRequerimiento').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Fecha", html, 'text-center', '80px');

            //  Nombre
                CoreUI.tableData.addColumn('listadoRequerimiento', "nombre","NOMBRE", null, 'text-justify');

            //  Tipo
                CoreUI.tableData.addColumn('listadoRequerimiento', "requerimientotipo[0].nombre", "TIPO", null,'text-left');

            //  Comunidad asociada
                CoreUI.tableData.addColumn('listadoRequerimiento', "comunidad[0].nombre","Comunidad asociada", null, 'text-left');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Fichero", html, 'text-center', '120px');

            //  Estado activado
                var html = 'data:activado$';
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Activo", html, 'text-center', '100px');

                // $('#listadoRequerimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoRequerimiento", "Requerimiento", "requerimiento/list");
        }
    },

    renderTablaDocumentacionContratosCesion: async function()
    {
        if($('#listadoDocumentacionContratosCesion').length)
        {

            //  Cargamos el listado de documentos de contratos de cesión
                CoreUI.tableData.init();

            //  Nombre
                CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', "nombre","NOMBRE", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', null, "Fichero", html, 'text-center', '120px');

                $('#listadoDocumentacionContratosCesion').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionContratosCesion", "Requerimiento", "rgpd/requerimiento/3/list", false, false, false);
        }
    },

    renderTablaDocumentacionCamarasSeguridad: async function()
    {
        if($('#listadoDocumentacionCamarasSeguridad').length)
        {

            //  Cargamos el listado de documentos de contratos de cesión
                CoreUI.tableData.init();

            //  Nombre
                CoreUI.tableData.addColumn('listadoDocumentacionCamarasSeguridad', "nombre","NOMBRE", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoDocumentacionCamarasSeguridad', null, "Fichero", html, 'text-center', '120px');

                $('#listadoDocumentacionCamarasSeguridad').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionCamarasSeguridad", "Requerimiento", "rgpd/requerimiento/2/list", false, false, false);
        }
    },

    renderTablaContratosCesion: async function()
    {

    },

    renderTablaCamarasSeguridad: async function()
    {

    },

}

$(()=>{
    requerimientoCore.init();
});