let adminFincas = {

    Init: async function()
    {

        comunidadesCore.renderMenuLateral();
        adminFincas.Events();

    },

    Events: function()
    {

        if($('body #listadoSMSAdministrador').length > 0)
        {
           adminFincas.SMS.RenderTableSMSCertified();
        }

        if($('body #listadoEmailCertificadoAdministrador').length > 0)
        {
            adminFincas.Email.RenderTableEmailCertified();
        }

        // Envío sms certificado
        $('body').on(core.helper.clickEventType,'.btnSendSimpleSMS', ((evt)=>{
            evt.stopImmediatePropagation();
            if(adminFincas.SMS.ValidateSMSSimple())
            {
                let phoneNumber = $('#telefonoDestinatario').val();
                let messageText = $('#mensajeSMS').val();
                adminFincas.SMS.SendSimpleSMSCertified(phoneNumber, messageText);
            };
        }));

        //  Envío SMS Certificado con contrato
        $('body').on(core.helper.clickEventType,'.btnEnviarSMSCertificadoContrato', (async (evt)=>{
            if(adminFincas.SMS.ValidateSMSContrato())
            {
                let _fileContract = document.getElementById('ficheroadjuntarcontrato');
                let phoneNumber = $('#telefonoDestinatarioContrato').val();
                let messageText = $('#mensajeSMSContrato').val();
                let fileContractBase64 = await CoreUI.Utils.getSelectedFileInBase64Format('ficheroadjuntarcontrato');
                let fileContractName = _fileContract.files[0].name;

                //  Enviamos el fichero al endpoint para procesarlo y enviarlo
                adminFincas.SMS.SendContractSMSCertified(phoneNumber, messageText, fileContractBase64, fileContractName);
            };
        }));        

        //  Ver mensaje enviado
        $('body').on(core.helper.clickEventType, '.btnVerMensajeEnviado', function(evt){
            adminFincas.Email.VerEmailEnviado($(this).attr('data-id'));
        });

        //  Caracteres restantes sms certificado
        $('body').on('keyup', '.mensajeSMS',((evt)=>{

            let fieldId = evt.target.id;
            let messageText = $(`body #${fieldId}`).val();
            let messageOrigin = $(`body #${fieldId}`).attr('data-origin');
            adminFincas.SMS.CalcRemainChars(messageOrigin, messageText);
        }));

        //  Envío email certificado
        $('body').on(core.helper.clickEventType, '.btnEnviarEmailCertificado',(()=>{
            adminFincas.Email.EnviarEmailCertificado( $('#nombreDestinatario').val(), $('#emailDestinatario').val(), $('#emailAsunto').val(), $('#emailBody').val(), core.Files.Fichero);
        })); 
    },

    Helper:{
        htmlEntities: function(rawStr) {
            var textArea = document.createElement('textarea');
            textArea.innerHTML = rawStr;
            return textArea.value;
        }
    },

    /** Componente de Email */
    Email: {

        /**
         * Carga un modal con la información del e-mail enviado
         * @param {*} emailId 
         */
        VerEmailEnviado: async function(emailId)
        {
                await apiFincatech.get(`mensaje/${emailId}`).then( async (result) =>{

                    let resultado = JSON.parse(result);
                    //let body = resultado.data.Mensaje[0]['body'];

                    resultado.data.Mensaje[0]['body'] = adminFincas.Helper.htmlEntities( resultado.data.Mensaje[0]['body'] );
                    resultado.data.Mensaje[0]['body'] = resultado.data.Mensaje[0]['body'].replaceAll('https:/app.fincatech.es/','https://app.fincatech.es/');

                    let datosMensaje = JSON.stringify(resultado.data.Mensaje[0]);
                    //  Recuperamos la vista para el mensaje
                    await apiFincatech.getView('modals','email/view_email',datosMensaje).then((result)=>{
                        CoreUI.Modal.CustomHTML(result,'E-mail certificado');
                    });
                    
                });
        },

        /**
         * 
         * @returns 
         */
        validarEmailCertificado: function()
        {
            let error = '';
            let emailValido = core.helper.validarEmail($('#emailDestinatario').val());

            $('form .form-control').removeClass('form-error');

            //  Campo e-mail
            if($('#emailDestinatario').val() == '' || emailValido == false){
                error = `El e-mail del destinatario no es válido<br>`;
                $('#emailDestinatario').addClass('form-error');
            }

            //  Asunto del e-mail
            if($('#emailAsunto').val() == ''){
                error = `${error}El asunto del e-mail no puede estar vacío<br>`;
                $('#emailAsunto').addClass('form-error');
            }

            //  Cuerpo del mensaje
            if($('#emailBody').val() == ''){
                error = `${error}El cuerpo del e-mail está vacío<br>`;
                $('#emailBody').addClass('form-error');                
            }

            if(error !== ''){
                CoreUI.Modal.Error(`<p class="text-left">No se ha podido enviar el e-mail por los siguientes motivos:<br><br>${error}</p>`,'Envío E-mail certificado');
                return false;
            }

            return true;
        },
        
        /**
         * 
         * @param {*} emailto 
         * @param {*} subject 
         * @param {*} message 
         */
        EnviarEmailCertificado: async function(recipient, emailto, subject, message, file = null)
        {
            if(adminFincas.Email.validarEmailCertificado())
            {

                var data = Object();
                data = {
                    senderid: -1,
                    destinatario: emailto,
                    comunidad: recipient,
                    mensaje: message,
                    subject: subject,
                    attachment: file
                };            

                await apiFincatech.post(`certificadodigital/administrador/envioemailcertificado`, data).then(async (response) =>{
                    var responseData = JSON.parse(response);

                    if(responseData.status['response'] == "ok")
                    {
                        CoreUI.Modal.Success("El E-mail se ha enviado correctamente.");

                        //  Limpiamos el formulario para enviar nuevo correo
                        $('#nombreDestinatario').val('');
                        $('#emailDestinatario').val('');
                        $('#emailAsunto').val('');
                        $('#emailBody').val('');

                        //  Renderizamos la tabla
                        adminFincas.Email.RenderTableEmailCertified();
                    }else{
                        CoreUI.Modal.Error(`No se ha podido enviar el E-mail<br><br>${responseData.status['error']}`);
                    }
                });
            }
        },
        /**
         * Renderizado tabla e-mails certificados
         */
        RenderTableEmailCertified: function()
        {
            if($('#listadoEmailCertificadoAdministrador').length)
            {
                //  Cargamos el listado de sms enviados
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Destinatario
                    //CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', "email","Destinatario", null, 'text-left');
                    CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', function(row, type, val, meta)
                    {
                        let outhtml = '';
                        if(!row.destinatarionombre)
                        {
                            outhtml = row.email;
                        }else{
                            outhtml = `${row.destinatarionombre} ( ${row.email} )`;
                        }
                        return outhtml;
                    }, 'Destinatario', null, 'text-left');

                //  Asunto
                    CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', "subject","Asunto", null, 'text-left');

                //  Mensaje
                    CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', function(row, type, val, meta){
                        let outhtml = `<a href="javascript:void(0);" class="btnVerMensajeEnviado pr-4" data-id="${row.id}"><i class="bi bi-envelope-open"></i></a>`;
                        return outhtml;
                    },'Mensaje',null,'text-center');
    
                //  Fecha de envío
                    CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', function(row, type, val, meta){
                        fecha = row.created;
                        //  Renderizamos el icono de descarga junto con la fecha
                        fechaSubida = `<span>${moment(fecha).locale('es').format('DD/MM/YYYY')}</span>`;
                        return `<p class="mb-0 text-center">${fechaSubida}</p>`;

                    },"Fecha envío", null, 'text-center');      

                //  Hora de envío
                CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', function(row, type, val, meta){
                    fecha = row.created;
                    //  Renderizamos el icono de descarga junto con la fecha
                    fechaSubida = `<span>${moment(fecha).locale('es').format('h:mm')}</span>`;
                    return `<p class="mb-0 text-center">${fechaSubida}</p>`;

                },"Hora envío", null, 'text-center');      

                //  Estado
                    CoreUI.tableData.addColumn('listadoEmailCertificadoAdministrador', function(row, type, val, meta){
                        let salida = '';
                        // console.log(row.filename);
                        if(!row.filename)
                        {
                            salida = '<span class="badge rounded-pill bg-success pl-2 pr-2 pt-1 pb-1 d-block">Entregado</span>';
                        }else{
                            salida = `<a class="btnDescargarEmailCertificado" href="${baseURL}public/storage/emailcertificados/${row.filename}" target="_blank" title="Email certificado"><i data-feather="mail" class="text-success img-fluid"></i></a>`;
                        }
                        
                        //  Renderizamos el icono de descarga 
                        return `<p class="mb-0 text-center">${salida}</p>`;

                    },"Estado", null, 'text-center'); 

                    $('#listadoEmailCertificadoAdministrador').addClass('no-clicable');
                    CoreUI.tableData.render("listadoEmailCertificadoAdministrador", "Mensaje", "certificadodigital/administrador/emailcertificado/list", false, true, true);
            }
        }
    },

    SMS: {

        CalcRemainChars: function(smsOrigin, smsText){
            let maxLength=160;
            let totalRemaining = maxLength - smsText.length;
            $(`body .${smsOrigin} .smsCaracteresRestantes`).text(totalRemaining);
        },

        RenderTableSMSCertified: function()
        {
            if($('#listadoSMSAdministrador').length)
            {
                //  Cargamos el listado de sms enviados
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Nº de teléfono
                    CoreUI.tableData.addColumn('listadoSMSAdministrador', "phone","Teléfono", null, 'text-left');
    
                //  Mensaje
                    CoreUI.tableData.addColumn('listadoSMSAdministrador', "message","Mensaje", null, 'text-left');
    
                //  Fecha de envío
                    CoreUI.tableData.addColumn('listadoSMSAdministrador', function(row, type, val, meta){
                        fecha = row.created;
                        //  Renderizamos el icono de descarga junto con la fecha
                        fechaSubida = `<span>${moment(fecha).locale('es').format('LLL')}</span>`;
                        return `<p class="mb-0 text-left">${fechaSubida}</p>`;

                    },"Fecha envío", null, 'text-center', '20%');      

                //  Estado
                    CoreUI.tableData.addColumn('listadoSMSAdministrador', function(row, type, val, meta){

                        let salida = '';
                        // console.log(row);
                        if(!row.filename)
                        {
                            salida = '<span class="badge rounded-pill bg-success pl-2 pr-2 pt-1 pb-1">Entregado</span>';
                        }else{
                            salida = `<a class="btnDescargarEmailCertificado" href="${baseURL}public/storage/emailcertificados/${row.filename}" target="_blank" title="Email certificado"><i data-feather="mail" class="text-success img-fluid"></i></a>`;
                        }
                        // salida = row.filename;
                        //  Renderizamos el icono de descarga 
                        return `<p class="mb-0 text-center">${salida}</p>`;

                    },"Estado", null, 'text-center', '10%');                     

                    $('#listadoSMSAdministrador').addClass('no-clicable');
                    CoreUI.tableData.render("listadoSMSAdministrador", "Sms", "sms/list", false, true, true);
            }
        },

        /**
         * Send Simple SMS Certified
         * @param {string} targetPhone 
         * @param {string} messageText 
         */
        SendSimpleSMSCertified: async function(targetPhone, messageText)
        {

            var data = Object();
            data = {
                sender: core.Security.user,
                phonenumber: targetPhone,
                message: messageText,
                contrato: 0
            };            

            await apiFincatech.post(`certificadodigital/administrador/enviosms`, data).then(async (response) =>{
                var responseData = JSON.parse(response);

                if(responseData.status['response'] == "ok")
                {
                    CoreUI.Modal.Success("El SMS se ha enviado correctamente al número de teléfono " + targetPhone);
                    adminFincas.SMS.RenderTableSMSCertified();
                    //  Limpiamos los campos de datos
                    $('#telefonoDestinatario').val('');
                    $('#mensajeSMS').val('');
  
                }else{
                    CoreUI.Modal.Error("No se ha podido enviar el SMS por el siguiente motivo:<br><br>" + responseData.status.response);
                }
            });
        },

        /**
         * Envía un contrato para ser firmado por SMS
         */
        SendContractSMSCertified: async function(targetPhone, messageText, fileContractBase64, fileContractName){

            var data = Object();
            data = {
                sender: core.Security.user,
                phonenumber: targetPhone,
                message: messageText,
                filebase64: fileContractBase64,
                filename: fileContractName,
            };            

            await apiFincatech.post(`certificadodigital/administrador/enviosmscontrato`, data).then(async (response) =>{
                var responseData = JSON.parse(response);

                if(responseData.status['response'] == "ok")
                {
                    CoreUI.Modal.Success("El SMS se ha enviado correctamente al número de teléfono " + targetPhone);
                    adminFincas.SMS.RenderTableSMSCertified();
  
                }else{
                    CoreUI.Modal.Error("No se ha podido enviar el SMS por el siguiente motivo:<br><br>" + responseData.status.response);
                }
            });
        },

        /**
         * Validate phone number 
         * @param {string} num 
         * @returns 
         */
        ValidatePhoneNumber: function(num)
        {
            var ph = new RegExp(/^[0-9]{11}$/);
            return (ph.test(num));
        },

        /**
         * Validate SMS Simple for send
         * @returns bool True or False
         */
        ValidateSMSSimple: function()
        {
            let validationError = '';

            $('form .form-control').removeClass('form-error');

            //  Validación escritura teléfono
            if($('#telefonoDestinatario').val() == ''){
                validationError = '- El teléfono no puede estar vacío<br>';
                $('#telefonoDestinatario').addClass('form-error');
            }

            //  Validación del número de teléfono
            if(adminFincas.SMS.ValidatePhoneNumber( $('#telefonoDestinatario').val() ) == false)
            {
                validationError += '- El número de teléfono no es válido<br>';
                $('#telefonoDestinatario').addClass('form-error');
            }
            
            //  Validación texto del sms
            if($('#mensajeSMS').val() == ''){
                validationError += '- El texto del mensaje no puede estar vacío<br>';
                $('#mensajeSMS').addClass('form-error');
            }

            if(validationError != '')
            {
                CoreUI.Modal.Error(`<p class="text-left">No se ha podido enviar el sms certificado por los siguientes motivos:<br><br>${validationError}</p>`,'No se ha podido enviar el SMS');
                return false;
            }else{
                return true;
            }
        },

        /**
         * Validación de envío de SMS con contrato
         * @returns Boolean. Resultado de la validación
         */
        ValidateSMSContrato: async function()
        {
            let validationError = '';

            //  Validación escritura teléfono
            if($('#telefonoDestinatarioContrato').val() == '')
                validationError = '- El teléfono no puede estar vacío<br>';

            //  Validación del número de teléfono
            if(adminFincas.SMS.ValidatePhoneNumber( $('#telefonoDestinatarioContrato').val() ) == false)
                validationError += '- El número de teléfono no es válido<br>';
            
            //  Validación texto del sms
            if($('#mensajeSMSContrato').val() == '')
                validationError += '- El texto del mensaje no puede estar vacío<br>';

            //  Validación de fichero seleccionado
            var ficheroContrato = await CoreUI.Utils.getSelectedFileInBase64Format('ficheroadjuntarcontrato');
            if(ficheroContrato === false)
            {
                validationError += '- Debe adjuntar un fichero en formato PDF<br>';
            }else{
                //  Validamos que el fichero sea PDF
                if(ficheroContrato.indexOf('data:application/pdf;base64') < 0)
                {
                    validationError += '- Debe adjuntar un fichero en formato PDF<br>';
                }
            }

            if(validationError != '')
            {
                CoreUI.Modal.Error(`<p class="text-left">No se ha podido enviar el sms certificado de contrato por los siguientes motivos:<br><br>${validationError}</p>`,'No se ha podido enviar el SMS');
                return false;
            }else{
                return true;
            }
        }

    },


}

$(()=>
{
    adminFincas.Init();
})