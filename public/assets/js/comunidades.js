let comunidadesCore = {

    comunidades: Object(),
    comunidad: Object(),

    init: async function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        this.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "comunidad")
        {

            //  Recuperamos el listado de comunidades
            await comunidadesCore.listadoDashboard();
            await comunidadesCore.renderMenuLateral();
        }

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.verModalComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("comunidad", id, nombre, "listadoComunidades");
        // Swal.fire({
        //     title:`¿Desea eliminar la comunidad:<br>${nombreComunidad}?`,
        //     text: "Se va a eliminar la comunidad y toda la información asociada",
        //     icon: 'question',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Eliminar',
        //     cancelButtonText: 'Cancelar'
        //   }).then((result) => {
        //     if (result.isConfirmed) {
        //         //  Llamamos al endpoint de eliminar
        //         apiFincatech.delete("comunidad", idComunidad).then((result) =>{
        //             Swal.fire(
        //                 'Comunidad eliminada correctamente',
        //                 '',
        //                 'success'
        //               );
        //               $('#listadoComunidades').DataTable().ajax.reload();
        //         });
        //     }
        // });
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalComunidad: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        comunidadesCore.getComunidad(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "comunidades/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, comunidadesCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, comunidadesCore.comunidad.nombre);
                // Swal.fire({
                //     title:`${comunidadesCore.comunidad.nombre}`,
                //     html: result,
                //     customClass: 'modal-lg'
                // })
            });

        });
    },

    renderMenuLateral: async function()
    {
        $('.navComunidades').append('<li class="sidebar-header">Comunidades</li>');
        comunidadesCore.comunidades.forEach( function(valor, indice, array){
            var html = `<li class="sidebar-item">
                            <a class="sidebar-link" href="/fincatech/comunidad/${valor['id']}">
                                <div class="row">
                                    <div class="col-2">
                                        <img src="/fincatech/public/assets/img/icon_edificio.png" class="img-responsive feather">
                                    </div>
                                    <div class="col-10 pr-0">
                                        <span class="align-middle">${valor['codigo']} - ${valor['nombre']}</span>
                                    </div>
                                </div>
                            </a>
                        </li>`;
            $('.navComunidades').append(html);
            
        });
        feather.replace();
    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTabla: async function()
    {
        if($('#listadoComunidad').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn("codigo","COD");
            //  Nombre
            CoreUI.tableData.addColumn("nombre", "NOMBRE");

            // //  Administrador
            //     var html = `<a href="${baseURL}administrador/data:usuarioId$" class="pl-1 pr-1">data:usuario.nombre$</a>`;
            //     CoreUI.tableData.addColumn(null, "ADMINISTRADOR", html);

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn(null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn("telefono", "TELEFONO");

            //  Documentos pendientes de subir
            CoreUI.tableData.addColumn("nombre", "doc pend. de subir");

            //  Pendientes de verificar
            CoreUI.tableData.addColumn("nombre", "doc pend. de verificar");

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha alta", html);

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

            CoreUI.tableData.render("listadoComunidad", "Comunidad", "comunidad/list");
        }

    },

    /** Recupera el listado de comunidades en el dashboard */
    listadoDashboard: async function()
    {
        await comunidadesCore.getAll().then(async (data)=>{
               $('.statscomunidades .total').html(comunidadesCore.comunidades.total);
               this.renderTabla();
        });
  
    },

    getComunidad: async function(comunidadId)
    {
        await apiFincatech.get('comunidad/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidad = responseData.Comunidad[0];
            console.log(comunidadesCore.comunidad);
            return comunidadesCore.comunidad;
        });
    },

    /** Recupera los datos de las comunidades desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('comunidad/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidades = responseData.Comunidad;
            comunidadesCore.comunidades.total = comunidadesCore.comunidades.length;
        });

    }

}

$(()=>{
    comunidadesCore.init();
});