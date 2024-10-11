//  TOFIX: Mejorar en la medida de lo posible este módulo. De ser posible, valorar la opción de utilizar 

let apiFincatech = 
{
    baseUrlEndpoint:'',
    response:null,
    emailSoporte: 'soporte@fincatech.es',
    emailTextoSoporte: '',

    init: function()
    {
        apiFincatech.baseUrlEndpoint = config.baseURLEndpoint;
        apiFincatech.emailTextoSoporte = `<p class="mt-2" style="font-size: 12px;"><br>Si el problema persiste, por favor, contacte con <a href="mailto:${apiFincatech.emailSoporte}" target="_blank" title="E-mail Soporte">${apiFincatech.emailSoporte}</a>`
        apiFincatech.emailTextoSoporte = `${apiFincatech.emailTextoSoporte}<p style="font-size: 12px;">En el e-mail especifique el error mostrado y los pasos realizados para poder comprobar la incidencia. El plazo máximo de resolución es de 48h tras recibir el e-mail.</p>`;
        apiFincatech.emailTextoSoporte = `${apiFincatech.emailTextoSoporte}<p class="mb-0" style="font-size: 12px;">Gracias por su colaboración</p>`;
    },

    parseMultipleJSONObjects: function(response) {
        const objects = [];
        let currentObject = '';
        let depth = 0;
    
        for (let i = 0; i < response.length; i++) {
            const char = response[i];
    
            if (char === '{') {
                if (depth === 0) {
                    currentObject = ''; // Reset when we encounter a new object
                }
                depth++;
            }
    
            if (depth > 0) {
                currentObject += char;
            }
    
            if (char === '}') {
                depth--;
                if (depth === 0) {
                    objects.push(JSON.parse(currentObject)); // Parse the complete object
                }
            }
        }
    
        msg = '<p class="text-danger"><span class="font-weight-bold">Error Interno</span>:<br><br>';
        //  Construimos el mensaje de salida basándonos en la información del JSON
        if(objects.length > 0)
        {
            for(let i = 0; i < objects.length; i++)
            {
                // console.log(objects[i]['error']['description']);
                msg = `${msg}<p>${objects[i].error.description}</p>`;    
            }
            msg = `${msg}</p>`;
        }

        return msg;
    },

    /**
     * 
     * @param {*} mensaje 
     * @param {*} xhr 
     */
    procesarError: function(mensaje, xhr='')
    {
        $('.loading').hide();
        
        let msg = mensaje;
        
        if(xhr !== ''){
            let responseText = !xhr.responseText ? xhr : xhr.responseText;
            let response = apiFincatech.parseMultipleJSONObjects(responseText);           
            msg = `${msg}${response}`;
        }

        msg = `${msg}${apiFincatech.emailTextoSoporte}`;
        CoreUI.Modal.Error(msg);
    },

    /**
     * 
     * @param {*} respuesta 
     * @param {*} xhr 
     * @returns 
     */
    procesarRespuesta:function(respuesta, xhr = '')
    {
        $('.loading').hide();
        //  Comprobamos si devuelve
        if(respuesta.status == 'ok')
            return respuesta;

        let msg = respuesta.mensaje;
        console.log(xhr);
        if(xhr !== ''){
            msg = '<p class="text-danger"><span class="font-weight-bold">Error Interno</span>:<br><br>' + xhr.responseJSON.error.description + '</p>';
        }
        msg = `${msg}${apiFincatech.emailTextoSoporte}`;
        CoreUI.Modal.error(msg);

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
                apiFincatech.procesarError(`Error de endpoint: ${respuesta.status.error}`);
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
            async: true,
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
                apiFincatech.procesarError(`Error de endpoint: ${respuesta.status.error}`);
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
                    console.log('Ha saltado el error desde el success');
                    return 'error';
                }else{
                    return JSON.stringify(respuesta);
                }
            },
            error: function(xhr)
            {
                $('.loading').hide();
                apiFincatech.procesarError(`Error de endpoint: ${respuesta.status.error}`, xhr);
            }
        });
    },

    postWithProgress: async function(entity, datosPost, showLoading = true) {
        if (showLoading)
            $('.loading').show();
    
        // Si tiene fichero adjuntado adjuntamos el objeto
        if (core.Files.Fichero.base64 != '' && core.Files.Fichero.base64 != null) {
            datosPost.fichero = core.Files.Fichero;
        }
    
        datosPost = JSON.stringify(datosPost);
        let respuestaFinal = '';
        let p = await new Promise(async (resolve, reject) => {

            const xhr = new XMLHttpRequest();
            xhr.open("POST", apiFincatech.baseUrlEndpoint + entity, true);
            xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");
    
            let lastProcessedIndex = 0;
            
            let dataString = '';

            xhr.onprogress = function(e) {
                let currentResponse = e.currentTarget.responseText;
                let newResponsePart = currentResponse.substring(lastProcessedIndex);
                lastProcessedIndex = currentResponse.length;
    
                let parts = newResponsePart.split('\n\n');
                let procesar = true;
                parts.forEach(part => {

                    if (part.trim() !== '' && procesar) {

                        dataString = part.replace('data: ', '');

                        try {
                            let parsedData = JSON.parse(dataString);

                            let isError = apiFincatech.isValidJson(parsedData.data);
                            if(isError){
                                parsedData = JSON.parse(parsedData.data);
                            }

                            switch(parsedData.data)
                            {
                                case 'progress':
                                    CoreUI.Progress.SetProgress(parsedData.progress);
                                    // console.log("Progreso:", parsedData.progress);
                                    // Actualiza tu interfaz con el progreso
                                    CoreUI.Progress.SetMessage(parsedData.message);
                                    break;
                                case 'completed':
                                    CoreUI.Progress.SetProgress(parsedData.progress);
                                    // Actualiza tu interfaz con el progreso
                                    CoreUI.Progress.SetMessage(parsedData.message);                                    
                                    // console.log("Proceso completado");
                                    break;
                                case 'error':
                                    dataString = parsedData;
                                    break;
                            }

                        } catch (err) {
                            respuestaFinal = dataString;                           
                            CoreUI.Progress.Hide();
                            // Pinta información detallada del error en la consola
                            console.error('Error al procesar JSON:', err.message); // Mensaje del error
                            console.error('Cadena con el problema:', dataString); // Cadena que intentabas procesar
                            console.error('Detalles del error:', err); // Objeto completo del error
                            // procesar = false;
                        }
                    }

                });
            };
    
            //  Al iniciar la petición
            xhr.onload = function() {
                $('.loading').hide();
                if (xhr.status === 200) {
                    try {
                        // console.log('onload terminado');
                        respuestaFinal = dataString;
                        //  Comprobamos si hay error en la respuesta
                        if(respuestaFinal.data == 'error')
                        {
                            reject(dataString);
                        }else{
                            resolve(dataString);
                        }
                    } catch (err) {
                        reject("Error procesando respuesta final: " + err);
                    }
                } else {
                    reject("Error de endpoint");
                }
            };
    
            //  Capturamos el posible error que pueda tener
            xhr.onerror = function() {
                $('.loading').hide();
                apiFincatech.procesarError('Error de endpoint');
                reject("Error de endpoint");
            };

            //  Lanzamos al WS
            xhr.send(datosPost);
        });

        return respuestaFinal;

    },

    /** Llamada POST al restful api */
    post: async function(entity, datosPost, showLoading = true)
    {
        if(showLoading)
            $('.loading').show();

        //  Si tiene fichero adjuntado adjuntamos el objeto
        if(core.Files.Fichero.base64 != '' && core.Files.Fichero.base64 != null)
            datosPost.fichero = core.Files.Fichero;

        datosPost = JSON.stringify(datosPost);

        return await $.ajax({
            url: apiFincatech.baseUrlEndpoint + entity ,
            method: "POST",
            contentType: "application/json; charset=utf-8",
            data: datosPost,
            success: function(respuesta)
            {
                $('.loading').hide();
                if(apiFincatech.isValidJson(respuesta)){
                    apiFincatech.response = respuesta.data;
                    if(respuesta.status=='error'){
                        return 'error';
                    }else{
                        return JSON.stringify(respuesta);
                    }                      
                }else{
                    apiFincatech.procesarError('', respuesta );
                }
                
                return respuesta;

              
            },
            error: function(respuesta, xhr)
            {
                $('.loading').hide();
                if(apiFincatech.isValidJson(respuesta)){
                    respuesta = JSON.parse(respuesta);
                    apiFincatech.procesarError(`Error de endpoint: ${respuesta.status.error}`, xhr );
                }else{
                    apiFincatech.procesarError('', respuesta );
                }               
            }
        });
            
        // });
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
    },

    /**
     * Comprueba si el texto pasado es un json válido o no
     * @param {*} jsonString 
     * @returns 
     */
    isValidJson: function(jsonString) {
        try {
            JSON.parse(jsonString);
            return true;
        } catch (e) {
            return false;
        }
    }

}
