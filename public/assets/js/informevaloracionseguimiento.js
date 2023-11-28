
let informeValoracionSeguimientoCore = {

    InformeValoracionSeguimiento: Object(),
    informeValoracionSeguimiento: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoInformevaloracionseguimiento').length && core.model.toLowerCase() == 'informevaloracionseguimiento')
        {
            await informeValoracionSeguimientoCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId; 
            if(core.actionModel=='add' && core.model.toLowerCase() == 'informevaloracionseguimiento')
            {
                $('#nombre').val('Informe de evaluación y seguimiento ');
            }      
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

                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', 
                    function(row, type, val, meta)
                    {
                        var timeStamp = moment(row.fecha, 'YYYY-MM-DD').unix();
                        var fechaCreacion = 'N/D';
                        if(row.fecha !== null && row.fecha != undefined){
                            console.log('Row Fecha: ' + row.fecha);
                            fechaCreacion = moment(row.fecha).locale('es').format('L');
                        }
                        //console.log('row.Fecha: ' + fechaCreacion);
                        return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
                    },
                "Fecha", null, 'text-center', '80px');

            //  Nombre
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', "titulo","TITULO", null, 'text-justify');

            //  Tipo
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', "usuario[0].nombre", "Administrador de fincas", null,'text-left');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" download="data:ficheroscomunes.nombre$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:26px;"></i></a>'
                CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', null, "Fichero", html, 'text-center', '120px');

            //  Columna de acciones sólo para sudo y dpd
                if(core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'DPD')
                {
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += `<li class="nav-item"><a href="${baseURL}informevaloracionseguimiento/data:id$" class="d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="edit" class="text-success img-fluid" style="height:26px;width:26px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarInformeValoracionSeguimiento d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid" style="height:26px;width:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoInformevaloracionseguimiento', null, "", html);
                }

                $('#listadoInformevaloracionseguimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoInformevaloracionseguimiento", "Informevaloracionseguimiento", "informevaloracionseguimiento/list");
        }
    }


}

$(()=>{
    informeValoracionSeguimientoCore.init();
});