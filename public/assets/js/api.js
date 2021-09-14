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

    },

    procesarRespuesta:function(respuesta)
    {
        //  Comprobamos si devuelve
        if(respuesta.status == 'ok')
        {
            return respuesta;
        }else{
            console.log('error de endpoint: ' + respuesta.mensaje);
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

    // /** Devuelve la vista solicitada con los datos que se envían por post */
    // getView: async function(entidad, vista, datos, elementoDestino, paginacion = false, numeropagina = 0 )
    // {

    //     datosVista = {
    //         'viewfolder': entidad,
    //         'view' : vista,
    //         'entidad' : datos,
    //         'paginacion': paginacion,
    //         'numeropagina': numeropagina
    //     }; 

    //     return await $.ajax({
    //         url: apiFincatech.baseUrlEndpoint + 'getview',
    //         type: "POST",
    //         data: JSON.stringify(datosVista),
    //         success: function(respuesta)
    //         {
    //             $(`${elementoDestino}`).html(respuesta);
    //         },
    //         error: function()
    //         {
    //             apiFincatech.procesarError('Error de endpoint');
    //         }
    //     });

    // },

    /** Lanza una petición GET al webservice y devuelve los datos en formato JSON
     * @param endpoint String Nombre del endpoint que se va a consultar
     */
    get: async function(endpoint)
    {
        // https://es.stackoverflow.com/questions/3582/diferencias-entre-ajax-anidadas-y-promises
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + endpoint,
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
                console.log('Error de endpoint');
            }
        });
    },

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
                console.log('Error de endpoint');
            }
        });
    },

    /** Llamada POST al restful api */
    post: async function(endpoint, datosPost)
    {
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + endpoint ,
            method: "POST",
            data: datosPost,
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

    update:function(endpoint, datosPut, id)
    {

    }

}
