let serviciosCore = {

    servicios: Object(),
    servicio: Object(),

    caeContratado: false,
    rgpdContratado: false,

    Model: {
        actualizarServicios: function()
        {

            var dataServicios = Object();

            dataServicios['type'] = 'bulk';
            dataServicios['servicesdata'] = [];
 
            //  Por cada servicio contratado se recupera toda la información necesaria
            $('body #listadoServiciosContratadosComunidades tbody tr').each(function()
            {
               
                let comunidadId = $(this).attr('data-idcomunidad');
                let fila = $(this);
                //  Procesamos todos los servicios para esa comunidad
                $(this).find(`.servicio_contratado`).each(function(){

                    let idServicio = $(this).attr('data-id');
                    let idTipoServicio = $(this).attr('data-idtiposervicio');
                    let sTipoServicio = $(this).attr('data-tipo');

                    let servicio = Object();

                    //  ID Comunidad
                        servicio.idcomunidad = comunidadId;

                    //  ID del servicio
                        servicio.id = idServicio;
                        servicio.idtiposervicio = idTipoServicio;

                    //  Estado de contratación del servicio
                        servicio.contratado = $(this).is(':checked') ? true : false;

                    //  Mes de facturación
                        // servicio.mesfacturacion = $(`.mes-facturacion-${idServicio} option:selected`).val();
                        servicio.mesfacturacion = $(fila).find(`.mes-facturacion-${sTipoServicio} option:selected`).val();

                    //  Precio PVP
                        servicio.precio = $(fila).find(`.precio-${sTipoServicio}`).val();

                    //  Precio comunidad
                        servicio.preciocomunidad = $(fila).find(`.precio-comunidad-${sTipoServicio}`).val();

                    //  Añadimos al objeto principal
                        dataServicios['servicesdata'].push(servicio);

                });

                //  Añadimos la información al objeto
                //dataServicios['servicesdata'].push(oServicio);

            });

            // console.log(dataServicios);

            if(dataServicios['servicesdata'].length > 0)
            {

                //  Enviamos la información al endpoint
                apiFincatech.put('servicios/0', dataServicios).then( (result) =>{

                    var data = JSON.parse(result);
                    console.log(data);
                    CoreUI.Modal.Success('Los servicios modificados se han actualizado satisfactoriamente','Actualización de Servicios Contratados', function(){
                        //  Actualizamos el listado de servicios por los posibles ID para evitar posibles errores
                        comunidadesCore.Render.tablaServiciosContratadosComunidades();
                    });

                });

            }

        }
    },

    init: async function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
            serviciosCore.events();

        await serviciosCore.renderServicios();

    },

    // Gestión de eventos
    events: function()
    {
        $('body').on(core.helper.clickEventType, '.btnGuardarPreciosServicios', function(e)
        {
            //  Guardamos los servicios
            serviciosCore.Model.actualizarServicios();
        });
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
            var servicioMesFacturacion = 12;

            //  Buscamos si el check de este servicio está checado
                servicioContratado = ( $(this).find('.servicioContratado').is(':checked') ? 1 : 0 );

            //  Buscamos el pvp del servicio
                servicioPrecio = $(this).find('.servicioPrecio').val();

            //  Buscamos el mes de facturación del servicio
                servicioMesFacturacion = $(this).find('.servicio-mesfacturacion option:selected').val();
            
            //  Buscamos el precio para la comunidad de este servicio
                servicioPrecioComunidad = $(this).find('.servicioPrecioComunidad').val();
            
            infoServicio = Object();
            infoServicio['idserviciocomunidad'] = servicioComunidadId;
            infoServicio['idcomunidad'] = core.modelId;
            infoServicio['idservicio'] = servicioID;
            infoServicio['precio'] = servicioPrecio;
            infoServicio['preciocomunidad'] = servicioPrecioComunidad;
            infoServicio['contratado']  = servicioContratado;
            infoServicio['servicio-mesfacturacion']  = (servicioMesFacturacion == '' ? '12' : servicioMesFacturacion);

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
            var retorno = parseFloat(servicioData.preciocomunidad) - parseFloat(servicioData.precio);
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
                <td class="mb-0 pb-0 text-center">
                    <select id="mesfacturacion-${servicioData.id}" name="mesfacturacion-${servicioData.id}" class=" servicio-mesfacturacion custom-select select-picker form-control w-100">
                        <option value="1" ${servicioData.mesfacturacion == 1 ? 'selected': ''}>Enero</option>
                        <option value="2" ${servicioData.mesfacturacion == 2 ? 'selected': ''}>Febrero</option>
                        <option value="3" ${servicioData.mesfacturacion == 3 ? 'selected': ''}>Marzo</option>
                        <option value="4" ${servicioData.mesfacturacion == 4 ? 'selected': ''}>Abril</option>
                        <option value="5" ${servicioData.mesfacturacion == 5 ? 'selected': ''}>Mayo</option>
                        <option value="6" ${servicioData.mesfacturacion == 6 ? 'selected': ''}>Junio</option>
                        <option value="7" ${servicioData.mesfacturacion == 7 ? 'selected': ''}>Julio</option>
                        <option value="8" ${servicioData.mesfacturacion == 8 ? 'selected': ''}>Agosto</option>
                        <option value="9" ${servicioData.mesfacturacion == 9 ? 'selected': ''}>Septiembre</option>
                        <option value="10" ${servicioData.mesfacturacion == 10 ? 'selected': ''}>Octubre</option>
                        <option value="11" ${servicioData.mesfacturacion == 11 ? 'selected': ''}>Noviembre</option>
                        <option value="12" ${servicioData.mesfacturacion == 12 ? 'selected': ''}>Diciembre</option>
                    </select>          
                </td>
                <td class="mb-0 pb-0 text-right">
                    <label class="retorno badge bg-success badge-pill text-white font-weight-normal d-block text-right" style="font-size: 16px;">${retorno.toFixed(2)}€</label>
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
                            // console.log(responseData[nombreEntidad][x]);
                            var idServicioComunidad = '';
                            if(responseData[nombreEntidad][x].idserviciocomunidad !== undefined)
                            {
                                idServicioComunidad = responseData[nombreEntidad][x].idserviciocomunidad;
                            }
                            serviciosCore.addServiceToTable(responseData[nombreEntidad][x], idServicioComunidad, true);
                        }

                        $('.servicio-mesfacturacion').each(function(){
                            let valor = $(this).children('option:selected').val();
                            console.log($(this).attr('id'), ' - ' ,valor);
                            $(this).select2({
                                theme:'bootstrap4'
                            });
                            $(this).val(valor).trigger('change');
                        });

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
                        certificadoDigitalContratado = false;
                    //  Si el usuario es de tipo ADMINFINCAS COMPROBAMOS SI TIENE CONTRATADO
                    //  CAE Y/O RGPD PARA ANULAR LOS ENLACES
                    if(core.Security.getRole() == 'ADMINFINCAS')
                    {
                        //  Servicio de CAE
                        if(typeof responseData.comunidadservicioscontratados[0]['contratado'] !== 'undefined')
                        {
                            // console.log('CAE Contratado ---> ' + responseData.comunidadservicioscontratados[0]['contratado']);
                            caeContratado = ( responseData.comunidadservicioscontratados[0]['contratado'] == '0' ? false : true);
                        }

                        //  Servicio de Certificado digital
                        if(typeof responseData.comunidadservicioscontratados[1]['contratado'] !== 'undefined')
                        {
                            // console.log('CAE Contratado ---> ' + responseData.comunidadservicioscontratados[0]['contratado']);
                            certificadoDigitalContratado = ( responseData.comunidadservicioscontratados[1]['contratado'] == '0' ? false : true);
                        }                        

                        //  Servicio de RGPD
                        if(typeof responseData.comunidadservicioscontratados[4]['contratado'] !== 'undefined')
                        {
                            // console.log('RGPD Contratado ---> ' + responseData.comunidadservicioscontratados[4]['contratado']);
                            rgpdContratado = (responseData.comunidadservicioscontratados[4]['contratado'] == '0' ? false : true);
                        }
                    }


                    if(core.Security.getRole() == 'CONTRATISTA' || core.Security.getRole() == 'TECNICOCAE')
                    {
                        $('.enlaceRGPD').remove();
                        $('.enlaceCertificadoDigital').remove();
                        $('.btnAsociarEmpresaCAE').remove();
                        $('.empresasComunidadHeader').remove();
                        $('.wrapperEmpresasComunidad').remove();
                        $('.wrapperEmpleadosEmpresaComunidad').remove();
                        return;
                    }

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

                    //  Validdación de servicio de certificado digital contratado
                    if(certificadoDigitalContratado === false)
                    {
                        
                        $('.enlaceCertificadoDigital').attr('href','#');
                        $('.enlaceCertificadoDigital').removeClass('enlaceCertificadoDigital').addClass('enlaceKOCertificadoDigital');
                        $('.enlaceKOCertificadoDigital').addClass('text-danger');
                    }else{
                        $('.enlaceCertificadoDigital').addClass('text-success');
                    }

                    $('body .form-servicioscontratados-info table tbody').html('');
                    //  Recorremos todos los servicios devueltos por el sistema
                    
                    for(x = 0; x < responseData[nombreEntidad].length; x++ )
                    {
                        var idServicioComunidad = '';
                        serviciosCore.addServiceToTable(responseData[nombreEntidad][x], null, false);
                    }

                    $('.servicio-mesfacturacion').each(function(){
                        let valor = $(this).children('option:selected').val();
                        console.log($(this).attr('id'), ' - ' ,valor);
                        $(this).select2({
                            theme:'bootstrap4'
                        });
                        $(this).val(valor).trigger('change');
                    });

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

    },

    Render: {

        Meses: ['Enero', 'Febrero', 'Marzo','Abril','Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre','Noviembre','Diciembre'],

        /**
         * Devuelve el html para el estado de contratación, precio, precio comunidad y mes de facturación para un servicio
         * @param {*} serviceName 
         * @param {*} serviceId 
         * @param {*} serviceStatus 
         * @param {*} comunidadId 
         * @param {*} tipoServicioId 
         * @param {*} mesFacturacion 
         * @param {*} precio 
         * @param {*} precioComunidad 
         * @returns 
         */
        ServiceInfo: function(serviceName, serviceId, serviceStatus, comunidadId, tipoServicioId, mesFacturacion, precio, precioComunidad )
        {
            let htmlTR;

            htmlTR = `
                <td class="bg-white text-center">
                    ${serviciosCore.Render.StatusCheckbox(serviceName, serviceId, comunidadId, tipoServicioId, serviceStatus)}
                </td>
                <td class="bg-white text-center pl-2 pr-2">
                    ${serviciosCore.Render.MonthSelect(serviceName, comunidadId, tipoServicioId, serviceId, mesFacturacion)}
                </td>
                <td class="bg-white text-right pl-3 pr-3">
                    ${serviciosCore.Render.InputPrecioPVP(serviceName, serviceId, comunidadId, tipoServicioId, precio)}
                </td>
                <td class="bg-white text-right pl-3 pr-3">
                    ${serviciosCore.Render.InputPrecioComunidad(serviceName, serviceId, comunidadId, tipoServicioId, precioComunidad)}
                </td>
            `;

            return htmlTR;
        },

        StatusCheckbox: function(serviceName, serviceId, comunidadId, tipoServicioId, serviceStatus)
        {
            let htmlCB = `
                <input data-id="${serviceId}" data-idcomunidad="${comunidadId}" data-idtiposervicio="${tipoServicioId}" data-tipo="${serviceName}" class="servicio_contratado servicio-contratado-${serviceId}" type="checkbox" ${serviceStatus}>
            `;
            return htmlCB;
        },

        MonthSelect: function(serviceName, comunidadId, tipoServicioId, serviceId, value)
        {
            let htmlOptions;
            let htmlSelect = `<select data-idcomunidad="${comunidadId}" data-id="${serviceId}" data-idtiposervicio="${tipoServicioId}" class=" servicio-mesfacturacion mes-facturacion-${serviceId} mes-facturacion-${serviceName} custom-select select-picker form-control w-100" style="width: 150px;">`;
            for(let iMonth = 1; iMonth < 13; iMonth++)
            {
                htmlOptions = `${htmlOptions}
                    <option value="${iMonth}" ${value == iMonth ? 'selected': ''}>${serviciosCore.Render.Meses[iMonth-1]}</option>
                `;
            }
            htmlSelect = `${htmlSelect}${htmlOptions}</select>`;
            return htmlSelect;
        },

        InputPrecioPVP: function(serviceName, serviceId, comunidadId, tipoServicioId, precio )
        {
            let htmlPrecioPVP;
            htmlPrecioPVP = `
            <input data-id="${serviceId}" data-idcomunidad="${comunidadId}" data-idtiposervicio="${tipoServicioId}" type="number" class="servicio_precio precio-${serviceId} precio-${serviceName} text-right" style="max-width:70px;" data-precio="precio" data-servicio="instalaciones" value="${precio}">
            `;
            return htmlPrecioPVP
        },

        InputPrecioComunidad: function(serviceName, serviceId, comunidadId, tipoServicioId, precio){
            let htmlPrecioComunidad;
            htmlPrecioComunidad = `
            <input data-id="${serviceId}" data-idcomunidad="${comunidadId}" data-idtiposervicio="${tipoServicioId}" type="number" class="servicio_precio precio-comunidad-${serviceId} precio-comunidad-${serviceName} text-right" style="max-width:70px;" data-precio="comunidad" data-servicio="rgpd" value="${precio}">            
            `;
            return htmlPrecioComunidad;
        },
    }

}

$(()=>{
   serviciosCore.init();
});