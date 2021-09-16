let administradorCore = {

    administradores: Object(),
    administrador: Object(),

    init: async function()
    {
        
        //  Bindeamos los eventos de los diferentes botones de administradores
        administradorCore.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "administrador")
        {
            //  Recuperamos el listado de administradores
            await administradorCore.listadoDashboard();
        }else if( core.model.toLowerCase() == "administrador" && core.actionModel != "list"){
            core.model = "Usuario";
        }

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerAdministrador', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.verModalAdministrador( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarAdministrador', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("administrador", id, nombre, "listadoAdministrador");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalAdministrador: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        administradorCore.getAdministrador(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "administradores/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, administradorCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, administradorCore.comunidad.nombre);
            });

        });
    },

    /**
     * Carga los datos del listado de administradores en la tabla listadoAdministradores
     */
    renderTabla: async function()
    {
        if($('#listadoAdministrador').length)
        {
            //  Cargamos el listado de administradores
            CoreUI.tableData.init();

            //  Nombre
            CoreUI.tableData.addColumn("nombre", "NOMBRE");

            CoreUI.tableData.addColumn("cif", "CIF");

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn(null, "EMAIL", html);

            //  Teléfono
                CoreUI.tableData.addColumn("telefono", "TELEFONO");

            //  Comunidades
                var html = '';
                CoreUI.tableData.addColumn(null, "Comunidades", html);

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html);
            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha de alta", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerAdministrador d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}administrador/data:id$" class="btnEditarAdministrador d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarAdministrador d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

            CoreUI.tableData.render("listadoAdministrador", "Usuario", "administrador/list");
        }

    },

    /** Recupera el listado de administradores en el dashboard */
    listadoDashboard: async function()
    {
        await administradorCore.getAll().then(async (data)=>{
               this.renderTabla();
        });
  
    },

    getAdministrador: async function(comunidadId)
    {
        await apiFincatech.get('administrador/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            administradorCore.administrador = responseData.Usuario[0];
            console.log(administradorCore.comunidad);
            return administradorCore.comunidad;
        });
    },

    /** Recupera los datos de las administradores desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('administrador/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            administradorCore.administradores = responseData.Usuario;
            // administradorCore.administradores.total = administradorCore.administradores.length;
        });

    }

}

$(()=>{
    administradorCore.init();
});