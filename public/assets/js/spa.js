let spaCore = {

    spas: Object(),
    spa: Object(),

    init: async function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        this.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "spa")
        {
           await spaCore.listadoDashboard();
        }

        //  Título del módulo
            if($('.titulo-modulo').length && core.model == 'Spa')
                CoreUI.setTitulo('nombre');        

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerSpa', (evt)=>{
            evt.stopImmediatePropagation();
            spaCore.verModalSpa( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarSpa', (evt)=>{
            evt.stopImmediatePropagation();
            spaCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("spa", id, nombre, "listadoSpa");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalSpa: async function(id)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        spaCore.getSpa(id).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "spa/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, spaCore.spa);
                CoreUI.Modal.GetHTML('modalInfoSpa', result, spaCore.spa.nombre);
                // Swal.fire({
                //     title:`${spaCore.comunidad.nombre}`,
                //     html: result,
                //     customClass: 'modal-lg'
                // })
            });

        });
    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTabla: async function()
    {
        if($('#listadoSpa').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn('listadoSpa', "cif","CIF");

            //  Nombre
            CoreUI.tableData.addColumn('listadoSpa', "nombre", "NOMBRE");

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoSpa', "personacontacto", "PERSONA DE CONTACTO");

            //  Localidad
            CoreUI.tableData.addColumn('listadoSpa', "localidad", "Localidad");

            //  Localidad
            CoreUI.tableData.addColumn('listadoSpa', "provincia[0].Nombre", "Provincia");

            //  Email
                var html = '<a href="mailto:data:email$" class="pl-1 pr-1">data:email$</a>';
                CoreUI.tableData.addColumn('listadoSpa', null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn('listadoSpa', "telefono", "TELEFONO");

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoSpa', null, "Fecha alta", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerSpa d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}spa/data:id$" class="btnEditarSpa d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarSpa d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoSpa', null, "", html);

            CoreUI.tableData.render("listadoSpa", "Spa", "spa/list");
        }

    },

    /** Recupera el listado */
    listadoDashboard: async function()
    {
        await spaCore.getAll().then(async (data)=>{
            //    $('.statscomunidades .total').html(spaCore.comunidades.total);
               this.renderTabla();
        });
  
    },

    getSpa: async function(id)
    {
        await apiFincatech.get('spa/' + id).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            spaCore.spa = responseData.Spa[0];

            return spaCore.spa;
        });
    },

    /** Recupera listado desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('spa/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            spaCore.spas = responseData.Spa;
            spaCore.spas.total = spaCore.spas.length;
        });

    }

}

$(()=>{
    spaCore.init();
});