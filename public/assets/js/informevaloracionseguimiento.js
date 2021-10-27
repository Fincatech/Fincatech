
let informeValoracionSeguimientoCore = {

    InformeValoracionSeguimiento: Object(),
    informeValoracionSeguimiento: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoInformevaloracionseguimiento').length)
        {
            // await informeValoracionSeguimientoCore.renderTabla();
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
                CoreUI.tableData.columns = [];
                
            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', null, "Fecha", html, 'text-center', '80px');

            //  Nombre
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', "titulo","TITULO", null, 'text-justify');

            //  Tipo
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', "usuario[0].nombre", "Administrador de fincas", null,'text-left');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', null, "Fichero", html, 'text-center', '120px');

            //  Columna de acciones sólo para sudo y dpd
                if(core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'DPD')
                {
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += `<li class="nav-item"><a href="${baseURL}informevaloracionseguimiento/data:id$" class="d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarInformeValoracionSeguimiento d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', null, "", html);
                }

                // $('#listadoInformevaloracionseguimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoInformevaloracionseguimiento", "InformeValoracionSeguimiento", "informevaloracionseguimiento/list");
        }
    }


}

$(()=>{
    informeValoracionSeguimientoCore.init();
});