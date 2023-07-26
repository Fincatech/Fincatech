
let notasInformativasCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoNotasInformativas').length && core.model.toLowerCase() == 'notasinformativas')
        {
            notasInformativasCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;        
        }

    },

    events: async function()
    {   
        $('body').on(core.helper.clickEventType, '.btnEliminarNotaInformativa', function(evt){
            //  Eliminamos la nota informativa previa confirmación
            notasInformativasCore.Modelo.Eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-titulo') );
        });
    },

    Modelo:{

        Eliminar: function(id, nombre)
        {
            core.Modelo.Delete("notasinformativas", id, nombre, "listadoNotasInformativas");
        }

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
                    CoreUI.tableData.addColumn('listadoNotasInformativas', 
                    function(row, type, val, meta)
                    {
                        var timeStamp = moment(row.fecha, 'YYYY-MM-DD hh:mm').unix();
                        var fechaCreacion = moment(row.fecha).locale('es').format('L')
                        return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
                    },
                "Fecha", null, 'text-center', '80px');

            //  Titulo
                CoreUI.tableData.addColumn('listadoNotasInformativas', "titulo","TITULO", null, 'text-justify', '30%');

            //  Descripcion
                CoreUI.tableData.addColumn('listadoNotasInformativas', "descripcion", "NOTA", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" download="data:ficheroscomunes.nombre$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoNotasInformativas', null, "Fichero", html, 'text-center');

            //  Columna de acciones sólo para sudo y dpd
                if(core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'DPD')
                {
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += `<li class="nav-item"><a href="${baseURL}notasinformativas/data:id$" class="d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="edit" class="text-success img-fluid" style="height:26px;width:26px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarNotaInformativa d-inline-block" data-id="data:id$" data-titulo="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid" style="height:26px;width:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoNotasInformativas', null, "", html);
                }

                await CoreUI.tableData.render("listadoNotasInformativas", "Notasinformativas", "notasinformativas/list", false, true, true, null, false);
        }
    }


}

$(()=>{
    notasInformativasCore.init();
});