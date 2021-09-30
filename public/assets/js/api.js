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
        $('.loading').hide();
        CoreUI.Modal.Error(mensaje);
    },

    procesarRespuesta:function(respuesta)
    {
        $('.loading').hide();
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

        $('.loading').show();

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + 'getview',
            method: "POST",
            data: datosVista,
            success: function(respuesta)
            {
                $('.loading').hide();
                $(`${elementoDestino}`).html(respuesta);
            },
            error: function()
            {
                $('.loading').hide();
                apiFincatech.procesarError('Error de endpoint');
            }
        });

    },

    /** Lanza una petición GET al webservice y devuelve los datos en formato JSON
     * @param endpoint String Nombre del endpoint que se va a consultar
     */
    get: async function(entity)
    {

        $('.loading').show();

        // https://es.stackoverflow.com/questions/3582/diferencias-entre-ajax-anidadas-y-promises
        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity,
            success: function(respuesta)
            {
                apiFincatech.response = respuesta.data;
                $('.loading').hide();
                if(respuesta.status=='error')
                {
                    return 'error';
                }else{
                    return JSON.stringify(respuesta);
                }
                 
            },
            error: function()
            {
                $('.loading').hide();
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
        $('.loading').show();

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity + "/" + id,
            type: 'delete',
            success: function(respuesta)
            {
                apiFincatech.response = respuesta.data;
                $('.loading').hide();

                if(respuesta.status=='error')
                {
                    return 'error';
                }else{
                    return JSON.stringify(respuesta);
                }
            },
            error: function()
            {
                $('.loading').hide();
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    },

    /** Llamada POST al restful api */
    post: async function(entity, datosPost)
    {
        $('.loading').show();

        //  Si tiene fichero adjuntado adjuntamos el objeto
        if(core.Files.Fichero.base64 != '')
        {
            // console.log(core.Files.Fichero);
            // console.log(datosPost);
            datosPost.fichero = core.Files.Fichero;
        }

        datosPost = JSON.stringify(datosPost);

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity ,
            method: "POST",
            contentType: "application/json; charset=utf-8",
            data: datosPost,
            success: function(respuesta)
            {
                $('.loading').hide();
                return respuesta;
            },
            error: function()
            {
                $('.loading').hide();
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

        $('.loading').show();

        //  Si tiene fichero adjuntado adjuntamos el objeto
        if(core.Files.Fichero.base64 != '')
            datosPut.fichero = core.Files.Fichero;

        datosPut = JSON.stringify(datosPut);

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity ,
            method: "PUT",
            contentType: "application/json; charset=utf-8",
            data: datosPut,
            success: function(respuesta)
            {
                $('.loading').hide();
                return respuesta;
            },
            error: function()
            {
                $('.loading').hide();
                apiFincatech.procesarError('Error de endpoint');
            }
        });
    }

}
