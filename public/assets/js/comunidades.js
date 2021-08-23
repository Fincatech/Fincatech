let comunidadesCore = {

    comunidades: Object(),
    comunidad: Object(),

    init: function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        this.events();
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
            comunidadesCore.eliminarComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    /** Elimina una comunidad previa confirmación */
    eliminarComunidad: function(idComunidad, nombreComunidad)
    {
        Swal.fire({
            title:`¿Desea eliminar la comunidad:<br>${nombreComunidad}?`,
            text: "Se va a eliminar la comunidad y toda la información asociada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                //  Llamamos al endpoint de eliminar
                apiFincatech.delete("comunidad", idComunidad).then((result) =>{
                    Swal.fire(
                        'Comunidad eliminada correctamente',
                        '',
                        'success'
                      );
                      $('#listadoComunidades').DataTable().ajax.reload();
                });
            }
        });
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
                CoreUI.Modals.show('modalInfoComunidad', result, comunidadesCore.comunidad.nombre);
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
                        <a class="sidebar-link" href="comunidad/${valor['id']}">
                            <img src="public/assets/img/icon_edificio.png" class="img-responsive feather">
                            <span class="align-middle pl-3">${valor['codigo']} - ${valor['nombre']}</span>
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
        if($('#listadoComunidades').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            CoreUI.tableData.addColumn("codigo");
            CoreUI.tableData.addColumn("nombre");
                var html = '<a href="mailto:data[emailcontacto]" class="pl-1 pr-1">data[emailcontacto]</a>';
            CoreUI.tableData.addColumn(null, html);
            CoreUI.tableData.addColumn("telefono");
            CoreUI.tableData.addColumn("nombre");
            CoreUI.tableData.addColumn("nombre");
            CoreUI.tableData.addColumn("nombre");
            //  Columna de acciones
                var html = '<a href="javascript:void(0);" class="btnVerComunidad pl-1 pr-1" data-id="data[id]" data-nombre="data[nombre]"><i data-feather="eye" class="text-info"></i></a>';
                    html += '<a href="comunidad/data[id]" class="btnEditarComunidad pl-1 pr-1" data-id="data[id]" data-nombre="data[nombre]"><i data-feather="edit" class="text-success"></i></a>';
                    html += '<a href="javascript:void(0);" class="btnEliminarComunidad pl-1 pr-1" data-id="data[id]" data-nombre="data[nombre]"><i data-feather="trash-2" class="text-danger"></i>';
                CoreUI.tableData.addColumn(null, html);
            CoreUI.tableData.render("listadoComunidades", "Comunidad", "comunidad/list");
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
})