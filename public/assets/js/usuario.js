let usuarioCore = {

    usuarios: Object(),
    usuario: Object(),

    init: async function()
    {

        //  Bindeamos los eventos de los diferentes botones de administradores
        usuarioCore.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "usuario")
        {
            //  Recuperamos el listado de administradores
            await usuarioCore.listadoDashboard();
        }

        //  Título del módulo
            if($('.titulo-modulo').length && core.model == 'Usuario')
                CoreUI.setTitulo('nombre');    

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerUsuario', (evt)=>{
            evt.stopImmediatePropagation();
            usuarioCore.verModalAdministrador( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarUsuario', (evt)=>{
            evt.stopImmediatePropagation();
            usuarioCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },



    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("usuario", id, nombre, "listadoUsuario");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalAdministrador: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        usuarioCore.getAdministrador(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "administradores/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, usuarioCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, usuarioCore.comunidad.nombre);
                // Swal.fire({
                //     title:`${usuarioCore.comunidad.nombre}`,
                //     html: result,
                //     customClass: 'modal-lg'
                // })
            });

        });
    },

    /**
     * Carga los datos del listado de administradores en la tabla listadoUsuarioes
     */
    renderTabla: async function()
    {
        if($('#listadoUsuario').length)
        {
            //  Cargamos el listado de administradores
            CoreUI.tableData.init();

            //  Nombre
            CoreUI.tableData.addColumn('listadoUsuario', "nombre", "NOMBRE");

            //  Rol
                var html = 'data:rol.nombre$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "ROL", html);

            CoreUI.tableData.addColumn('listadoUsuario', "cif", "CIF");

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn('listadoUsuario', null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn('listadoUsuario', "telefono", "TELEFONO");


            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "Estado", html);
            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "Fecha de alta", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}usuario/data:id$" class="btnEditarUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoUsuario', null, "", html);

            CoreUI.tableData.render("listadoUsuario", "Usuario", "usuario/list");
        }

    },

    /** Recupera el listado de administradores en el dashboard */
    listadoDashboard: async function()
    {
        await usuarioCore.getAll().then(async (data)=>{
               this.renderTabla();
        });
  
    },

    getAdministrador: async function(comunidadId)
    {
        await apiFincatech.get('usuario/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            usuarioCore.comunidad = responseData.Comunidad[0];
            console.log(usuarioCore.comunidad);
            return usuarioCore.comunidad;
        });
    },

    /** Recupera los datos de las administradores desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('usuario/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            usuarioCore.usuarios = responseData.Usuario;
            // usuarioCore.administradores.total = usuarioCore.administradores.length;
        });

    }

}

$(()=>{
    usuarioCore.init();
});