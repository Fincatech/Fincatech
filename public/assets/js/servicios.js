let serviciosCore = {

    servicios: Object(),
    servicio: Object(),

    init: async function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        // this.events();

        await serviciosCore.renderServicios();

    },

    // Gestión de eventos
    events: function()
    {

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {

    },

    /** Añade una fila con la información del servicio a la tabla de servicios */
    addServiceToTable: function (servicioData)
    {
        var html = `
            <tr data-idservicio="${servicioData.id}">
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${servicioData.id}" id="id-${servicioData.id}">
                        <label class="form-check-label" for="id-${servicioData.id}">${servicioData.nombre}</label>
                    </div>
                </td>
                <td class="mb-0 pb-0">
                    <input type="number" class="form-control text-center" value="${servicioData.precio}">
                </td>
                <td class="mb-0 pb-0">
                    <input type="number" class="form-control text-center" value="${servicioData.preciocomunidad}">
                </td>
                <td class="mb-0 pb-0 text-right">
                    <label class="retorno">${servicioData.retorno}€</label>
                </td>
            </tr>
        `;
        $('body .form-servicioscontratados table tbody').append(html);
    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderServicios: async function()
    {

        var rol = $('body').attr('hs-role');
        var endpointServicios = '';

        switch(rol)
        {
            //  Si es un sudo recuperamos el listado de servicios
            case 'SUDO':
            console.log('sudo');

                if($('.form-servicioscontratados').length)
                {

                    //  Comprobamos si está editando o creando una comunidad
                    switch(core.actionModel)
                    {

                        case 'add':
                            await apiFincatech.get('servicios/list').then(async (data)=>
                            {
                                result = JSON.parse(data);
                                responseStatus = result.status;
                                responseData = result.data;
                                $('body .form-servicioscontratados table tbody').html('');
                                //  Recorremos todos los servicios devueltos por el sistema
                                for(x = 0; x < responseData['Tiposservicios'].length; x++ )
                                {
                                    serviciosCore.addServiceToTable(responseData['Tiposservicios'][x]);
                                }

                            });                        
                            endpointServicios = 'servicios/list';
                            break;

                        case 'get':
                            endpointServicios = `servicios/${core.modelId}/list`;
                            break;

                    }

                    //  Recuperamos el listado de servicios
        
                }
                break;
            //  Si es admin de fincas recuperamos el listado de servicios contratados por una comunidad
            case 'ADMINFINCAS':

                break;
        }



    },

    getSpa: async function(id)
    {
        await apiFincatech.get('spa/' + id).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            serviciosCore.spa = responseData.Spa[0];

            return serviciosCore.spa;
        });
    },

    /** Recupera listado desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('servicios/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            serviciosCore.servicios = responseData.Tiposservicios;
            serviciosCore.servicios.total = serviciosCore.Tiposservicios.length;
        });

    }

}

$(()=>{
    serviciosCore.init();
});