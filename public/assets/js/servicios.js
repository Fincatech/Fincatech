let serviciosCore = {

    servicios: Object(),
    servicio: Object(),

    caeContratado: false,
    rgpdContratado: false,

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

    /**
     * Mapea los servicios contratados por una comunidad antes de ser guardada
     */
    mapServiciosContratados: function()
    {

        core.Forms.data['comunidadservicioscontratados'] = [];

        $('body .dataServicioContratado').each(function()
        {

            var servicioContratado = 0;
            var servicioID = $(this).attr('data-idservicio');
            var servicioComunidadId = $(this).attr('data-idserviciocomunidad');
            var servicioPrecio = 0;
            var servicioPrecioComunidad = 0;

            //  Buscamos si el check de este servicio está checado
                servicioContratado = ( $(this).find('.servicioContratado').is(':checked') ? 1 : 0 );

            //  Buscamos el pvp del servicio
                servicioPrecio = $(this).find('.servicioPrecio').val();

            //  Buscamos el precio para la comunidad de este servicio
                servicioPrecioComunidad = $(this).find('.servicioPrecioComunidad').val();
            
            infoServicio = Object();
            infoServicio['idserviciocomunidad'] = servicioComunidadId;
            infoServicio['idcomunidad'] = core.modelId;
            infoServicio['idservicio'] = servicioID;
            infoServicio['precio'] = servicioPrecio;
            infoServicio['preciocomunidad'] = servicioPrecioComunidad;
            infoServicio['contratado']  = servicioContratado;

            core.Forms.data['comunidadservicioscontratados'].push(infoServicio);

        });

        // console.log(core.Forms.data['comunidadservicioscontratados']);

    },

    /** Añade una fila con la información del servicio a la tabla de servicios */
    addServiceToTable: function (servicioData, idServicioComunidad, fullInfo = false)
    {
        var checked = (servicioData.contratado == 1 ? ' checked="checked" ' : '');
        if(fullInfo)
        {
            var html = `
            <tr class="dataServicioContratado" data-idservicio="${servicioData.id}" data-idserviciocomunidad="${idServicioComunidad}">
                <td class="mb-0 pb-0">
                    <div class="form-check">
                    <input type="hidden" class="data" hs-entity-related="comunidadservicioscontratados" hs-entity="Comunidad" hs-field="idservicio" value="${servicioData.id}">
                        <input class="form-check-input servicioContratado" type="checkbox" ${checked} value="${servicioData.id}" data-id="${servicioData.id}" id="id-${servicioData.id}">
                        <label class="form-check-label" for="id-${servicioData.id}">${servicioData.nombre}</label>
                    </div>
                </td>
                <td class="mb-0 pb-0">
                    <input type="number" class="form-control text-center data servicioPrecio" maxlength="6" hs-entity-related="comunidadservicioscontratados" hs-entity="Comunidad" hs-field="precio" value="${servicioData.precio}">
                </td>
                <td class="mb-0 pb-0">
                    <input type="number" class="form-control text-center data servicioPrecioComunidad" maxlength="6" hs-entity-related="comunidadservicioscontratados" hs-entity="Comunidad" hs-field="preciocomunidad" value="${servicioData.preciocomunidad}">
                </td>
                <td class="mb-0 pb-0 text-right">
                    <label class="retorno">${servicioData.retorno}€</label>
                </td>
            </tr>
        `;
        $('body .form-servicioscontratados table tbody').append(html);
        }else{

            var iconoEstadoServicio = (servicioData.contratado == 0 || typeof servicioData.contratado === 'undefined' ? '<i class="bi bi-x-circle text-danger" style="font-size:21px;"></i>' : '<i class="bi bi-check-circle text-success" style="font-size:21px;"></i>');
            var html = `
            <tr>
                <td class="mb-0 pb-0">
                        <label class="form-check-label">${servicioData.nombre}</label>
                </td>
                <td class="mb-0 pb-0 text-center">${iconoEstadoServicio}</td>
            </tr>
        `;     
            $('body .form-servicioscontratados-info table tbody').append(html);      
        }

        
    },

    /** FIXME: Hay que modificar la lógica de este método y hacerla más legible, eficiente y utilizando good practises
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
                // console.log('sudo servicios contratados');
                if($('.form-servicioscontratados').length)
                {
                    // console.log('servicios detectados');
                    //  Comprobamos si está editando o creando una comunidad
                    switch(core.actionModel)
                    {

                        case 'add':
                            endpointServicios = 'servicios/list';
                            nombreEntidad = 'Tiposservicios';
                            _isAdding = true;
                            break;

                        case 'get':
                            endpointServicios = `comunidad/${core.modelId}/servicioscontratados`;
                            nombreEntidad = 'comunidadservicioscontratados';
                            _isAdding = false;
                            break;

                    }

                    //  Recuperamos el listado de servicios
                    await apiFincatech.get(endpointServicios).then( (data)=>
                    {
                        result = JSON.parse(data);
                        responseStatus = result.status;

                        if(_isAdding)
                        {
                            responseData = result.data;
                        }else{
                            responseData = result.data.Comunidad[0];
                        }
                        $('body .form-servicioscontratados table tbody').html('');
                        //  Recorremos todos los servicios devueltos por el sistema
                        
                        for(x = 0; x < responseData[nombreEntidad].length; x++ )
                        {
                            console.log(responseData[nombreEntidad][x]);
                            var idServicioComunidad = '';
                            if(responseData[nombreEntidad][x].idserviciocomunidad !== undefined)
                            {
                                idServicioComunidad = responseData[nombreEntidad][x].idserviciocomunidad;
                            }
                            serviciosCore.addServiceToTable(responseData[nombreEntidad][x], idServicioComunidad, true);
                        }

                    }); 
                    
                }
                break;

            //  Si es admin de fincas recuperamos el listado de servicios contratados por una comunidad
            default:
                //  Recuperamos los datos del modelo y pintamos los valores
                if(typeof core.modelId === 'undefined' || core.modelId === '')
                    return;

                endpointServicios = `comunidad/${core.modelId}/servicioscontratados`;
                // console.log(endpointServicios);
                nombreEntidad = 'comunidadservicioscontratados';

                await apiFincatech.get(endpointServicios).then(async (data)=>
                {
                    // console.log(data);
                    result = JSON.parse(data);
                    responseStatus = result.status;
                    responseData = result.data.Comunidad[0];

                    //  0: CAE
                    //  4: RGPD
                        caeContratado = false;
                        rgpdContratado = false;
                    //  Si el usuario es de tipo ADMINFINCAS COMPROBAMOS SI TIENE CONTRATADO
                    //  CAE Y/O RGPD PARA ANULAR LOS ENLACES
                    if(core.Security.getRole() == 'ADMINFINCAS')
                    {
                        if(typeof responseData.comunidadservicioscontratados[0]['contratado'] !== 'undefined')
                        {
                            // console.log('CAE Contratado ---> ' + responseData.comunidadservicioscontratados[0]['contratado']);
                            caeContratado = ( responseData.comunidadservicioscontratados[0]['contratado'] == '0' ? false : true);
                        }

                        if(typeof responseData.comunidadservicioscontratados[4]['contratado'] !== 'undefined')
                        {
                            // console.log('RGPD Contratado ---> ' + responseData.comunidadservicioscontratados[4]['contratado']);
                            rgpdContratado = (responseData.comunidadservicioscontratados[4]['contratado'] == '0' ? false : true);
                        }
                    }


                    if(core.Security.getRole() == 'CONTRATISTA' || core.Security.getRole() == 'TECNICOCAE')
                    {
                        // $('.enlaceCae').remove();
                        $('.enlaceRGPD').remove();
                        $('.btnAsociarEmpresaCAE').remove();
                        $('.empresasComunidadHeader').remove();
                        $('.wrapperEmpresasComunidad').remove();
                        $('.wrapperEmpleadosEmpresaComunidad').remove();
                        return;
                    }

                    // console.log( responseData.comunidadservicioscontratados[0] );
                    // console.log('Cae Contratado: ' + caeContratado);
                    // console.log( responseData.comunidadservicioscontratados[4] );
                    // console.log('RGPD Contratado: ' + rgpdContratado);
                    $('.enlaceCae').removeClass('text-success');
                    $('.enlaceCae').removeClass('text-danger');

                    if(caeContratado === false)
                    {
                        $('.enlaceCae').attr('href','#');
                        $('.enlaceCae').removeClass('enlaceCae').addClass('enlaceKOCae');
                        $('.enlaceKOCae').addClass('text-danger');
                    }else{
                        $('.enlaceCae').addClass('text-success');
                    }

                    $('.enlaceRGPD').removeClass('text-success');
                    $('.enlaceRGPD').removeClass('text-danger');
                    if(rgpdContratado === false)
                    {
                        $('.enlaceRGPD').attr('href','#');
                        $('.enlaceRGPD').removeClass('enlaceRGPD').addClass('enlaceKORGPD');
                        $('.enlaceKORGPD').addClass('text-danger');
                    }else{
                        $('.enlaceRGPD').addClass('text-success');
                    }

                    $('body .form-servicioscontratados-info table tbody').html('');
                    //  Recorremos todos los servicios devueltos por el sistema
                    
                    for(x = 0; x < responseData[nombreEntidad].length; x++ )
                    {
                        var idServicioComunidad = '';
                        serviciosCore.addServiceToTable(responseData[nombreEntidad][x], null, false);
                    }

                });                 
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