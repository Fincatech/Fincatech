let apiFincatech = 
{
    baseUrlEndpoint:'',
    response:null,

    init: function()
    {
        apiFincatech.baseUrlEndpoint = config.baseURLEndpoint;
        // console.log(apiFincatech.baseUrlEndpoint);
    },

    procesarError: function(mensaje)
    {
        CoreUI.Modal.Error(mensaje);
    },

    procesarRespuesta:function(respuesta)
    {
        //  Comprobamos si devuelve
        if(respuesta.status == 'ok')
        {
            return respuesta;
        }else{
            CoreUI.Modal.error('error de endpoint: ' + respuesta.mensaje);
        }
    },

    /** Devuelve la vista solicitada con los datos que se envían por post */
    getView: async function(modulo, entidad, datos, elementoDestino, paginacion = false, numeropagina = 0 )
    {

        datosVista = {
            'viewfolder': modulo,
            'view' : entidad,
            'entidad' : datos,
            'paginacion': paginacion,
            'numeropagina': numeropagina
        }; 

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + 'getview',
            method: "POST",
            data: datosVista,
            success: function(respuesta)
            {
                $(`${elementoDestino}`).html(respuesta);
            },
            error: function()
            {
                apiFincatech.procesarError('Error de endpoint');
            }
        });

    },

    /** Lanza una petición GET al webservice y devuelve los datos en formato JSON
     * @param endpoint String Nombre del endpoint que se va a consultar
     */
    get: async function(entity)
    {
        // https://es.stackoverflow.com/questions/3582/diferencias-entre-ajax-anidadas-y-promises
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity,
            success: function(respuesta)
            {
                apiFincatech.response = respuesta.data;

                if(respuesta.status=='error')
                {
                    return 'error';
                }else{
                    return JSON.stringify(respuesta);
                }
                 
            },
            error: function()
            {
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    },

    /**
     * Lanza una petición de tipo DELETE al webservice
     * @param {*} entity Nombre de la entidad
     * @param {*} id ID de la entidad que se quiere eliminar
     * @returns 
     */
    delete: async function(entity, id)
    {
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity + "/" + id,
            type: 'delete',
            success: function(respuesta)
            {
                apiFincatech.response = respuesta.data;

                if(respuesta.status=='error')
                {
                    return 'error';
                }else{
                    return JSON.stringify(respuesta);
                }
                 
            },
            error: function()
            {
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    },

    /** Llamada POST al restful api */
    post: async function(entity, datosPost)
    {
    console.log('api post: ' + datosPost);
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity ,
            method: "POST",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(datosPost),
            success: function(respuesta)
            {
                return respuesta;
            },
            error: function()
            {
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    },

    /**
     * Llamada PUT al websrevice
     * @param {*} endpoint 
     * @param {*} datosPut 
     * @returns 
     */
    put: async function(entity, datosPut)
    {
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity ,
            method: "PUT",
            contentType: "application/json; charset=utf-8",
            data: datosPut,
            success: function(respuesta)
            {
                return respuesta;
            },
            error: function()
            {
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    }

}
