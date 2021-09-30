
let informeValoracionSeguimientoCore = {

    InformeValoracionSeguimiento: Object(),
    informeValoracionSeguimiento: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoInformevaloracionseguimiento').length)
        {
            informeValoracionSeguimientoCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;       
        }

    },

    events: async function()
    {   

        $('body').on(core.helper.clickEventType, '.btnEliminarInformeValoracionSeguimiento', (evt)=>{
            evt.stopImmediatePropagation();
            informeValoracionSeguimientoCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-titulo') );
        });

    },

    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("informevaloracionseguimiento", id, nombre, "listadoInformevaloracionseguimiento");
    },   

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoInformevaloracionseguimiento').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center', '80px');

            //  Nombre
                CoreUI.tableData.addColumn("titulo","TITULO", null, 'text-justify');

            //  Tipo
                CoreUI.tableData.addColumn("usuario[0].nombre", "Administrador de fincas", null,'text-left');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn(null, "Fichero", html, 'text-center', '120px');

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}informevaloracionseguimiento/data:id$" class="d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarInformeValoracionSeguimiento d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

                // $('#listadoInformevaloracionseguimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoInformevaloracionseguimiento", "InformeValoracionSeguimiento", "informevaloracionseguimiento/list");
        }
    }


}

$(()=>{
    informeValoracionSeguimientoCore.init();
});