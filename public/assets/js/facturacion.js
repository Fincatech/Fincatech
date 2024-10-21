let facturacion = {

    Init: function(){
        facturacion.Events();
        if(core.model.toLowerCase() == 'liquidaciones'){
            facturacion.Liquidaciones.Init();
        }
    },

    Events: function()
    {

        //  Inicialización del masked para la cuenta IBAN
        if($('#iban').length)
        {
            $('#iban').mask('SS00 0000 0000 00 0000000000');
            //  Validación de cuando pierde el foco
            $('#iban').on('blur', function(evt)
            {
                if($('#iban').val().length > 0){
                    if(!core.Validator.checkIBAN($('#iban').val())){
                        evt.stopImmediatePropagation();
                        CoreUI.Modal.Error('El código IBAN no es correcto. Por favor, revíselo.');
                    }    
                }
            });
        }

        /**
         * Generación informe de prefacturación
         */
        $('body').on(core.helper.clickEventType, '.btnGenerarInformePrefacturacion', function(){
            facturacion.Prefacturacion.generarInformePrefacturacion();
        });

        //  Inicialización del componente de Banco
        facturacion.Bank.Events();
        //  Inicialización de los eventos de remesas
        facturacion.Remesa.Events();
    },

    Bank: {

        Events: function(){
            //  Selección de banco
            $('#bancoRemesa').on('change', function(ev)
            {
                
                let p = new Promise(async(resolve, reject)=>{
                    await facturacion.Bank.Controller.Get($('#bancoRemesa option:selected').val());
                    resolve(true);
                });

                p.then(()=>{
                    let banco = facturacion.Bank.Model.Bank.nombre;
                    let cuenta = facturacion.Bank.Model.Bank.iban == null ? 'No establecido' : facturacion.Bank.Model.Bank.iban;
                    let creditorid = facturacion.Bank.Model.Bank.creditorid == null ? 'No establecido' : facturacion.Bank.Model.Bank.creditorid;
                    $('.info-banco').html(`${banco}`);   
                    $('.info-iban').html(`${cuenta}`);   
                    $('.info-creditorid').html(`${creditorid}`);   
                });

            });
        },

        Controller: {

            Get: async function(id){
                await apiFincatech.get(`bank/${id}`).then((result)=>{
                    let res = JSON.parse(result);
                    facturacion.Bank.Model.Bank = res.data.Bank[0];
                    facturacion.Bank.Model.CuentaAsociada = (facturacion.Bank.Model.Bank.iban !== null);
                });
            }

        },

        Model: {
            Bank: null,
            CuentaAsociada: false,
        },

        View: {

        }

    },

    /**
     * Facturación
     */
    Facturacion: {

        Constants: {
            //  Estados de facturación
            ESTADO_FACTURADO: 'F',
            ESTADO_PENDIENTE_FACTURACION: 'P',
            ESTADO_FACTURAS_DEVUELTAS: 'D'
        },

        Events: function(){

            if(core.actionModel == 'emision')
                core.Forms.initializeSelectData();

            //  Dashboard -> Total facturación anual
            if($('.stats-facturacion-anual').length)
            {
                facturacion.Facturacion.Controller.LoadStatsDashboard();
            }

            //  Renderización de tablas de listado
            facturacion.Facturacion.Controller.RenderList();

            //  Guardar Banco
            $('body .form-banco').off(core.helper.clickEventType).on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
                evt.stopImmediatePropagation();
                facturacion.Facturacion.Controller.Bank.Save();
            });    

            //  Eliminar banco
            $('body').on(core.helper.clickEventType, '.btnEliminarBanco', function(evt){
                let nombre = $(this).attr('data-nombre');
                let id = $(this).attr('data-id');
                facturacion.Facturacion.Controller.Bank.Delete(id, nombre);
            });

            //  Generación de facturas
            $('body').on(core.helper.clickEventType, '.btnGenerarFactura', function(ev)
            {
                facturacion.Facturacion.Controller.GenerarFacturacion();
            });

            //  Cambio de mes
            $('#mesFacturacion').on('change', function(ev)
            {
                $('.mes-facturacion').html($('#mesFacturacion option:selected').text());
                facturacion.Facturacion.Controller.InfoFacturacion();
            });

            //  Cambio de administrador
            $('#usuarioId').on('change', function(ev)
            {
                //  Solo es llamado si el modelo es inicialmente el de facturación
                if(core.model !== 'Facturacion')
                    return;

                //  Administrador seleccionado
                let idAdministrador  = $('#usuarioId option:selected').val();
                let nombreAdministrador = 'Ninguno';
                if(parseInt(idAdministrador) > 0){
                    nombreAdministrador = $('#usuarioId option:selected').text();
                }
                $('.administrador-seleccionado').html( nombreAdministrador );                
                facturacion.Facturacion.Controller.InfoFacturacion();
            });

            //  Selector de servicios a facturar
            $('.chkServicio').on('change', function(evt)
            {
                if($(this).is(':checked')){
                    facturacion.Facturacion.View.AddServicioToInfo( $(this).val(), $(this).attr('data-nombre') );
                }else{
                    facturacion.Facturacion.View.RemoveServicioFromInfo( $(this).val() );
                }

                //  Si hay algún servicio seleccionado, recuperamos la información de la posible facturación
                facturacion.Facturacion.Controller.InfoFacturacion();

            });

            //  Configuración
            if($('#formConfiguracion').length){

                //  Botón de guardar
                $('.btnSaveData').off(core.helper.clickEventType).on(core.helper.clickEventType, function(ev)
                {
                    facturacion.Facturacion.Controller.SaveConfiguration();
                });

                //  Cargamos la configuración
                facturacion.Facturacion.Controller.LoadConfiguration();

            }

            /**
             * Proceso terminado
             */
            $('body').on(core.helper.clickEventType, '.btnProcesoTerminado', function(evt){
                CoreUI.Progress.Hide();
            });

            //  Eliminación de ingreso a cuenta
            $('body').on(core.helper.clickEventType, '.btnEliminarIngresoCuenta', (evt)=>{
                evt.stopImmediatePropagation();
                facturacion.Facturacion.Controller.IngresosCuenta.Delete( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-concepto'), $(evt.currentTarget).attr('data-procesado'), $(evt.currentTarget).attr('data-importe'), $(evt.currentTarget).attr('data-fecha'), $(evt.currentTarget).attr('data-administrador') );
            });

            //  Guardar ingreso a cuenta
            $('body .form-ingreso-cuenta').off(core.helper.clickEventType).on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
                evt.stopImmediatePropagation();
                facturacion.Facturacion.Controller.IngresosCuenta.Save();
            });               

            //  Factura rectificativa
            $('body').on(core.helper.clickEventType, '.btnCrearFacturaRectificativa', function(ev)
            {
                let id = $(this).attr('data-id');
                let idRectificativa = $(this).attr('data-idrectificativa');

                if(!facturacion.Facturacion.Controller.ValidarFacturaRectificativa(idRectificativa))
                    return;

                //  Construimos el objeto que vamos a enviar al WS
                let datos = Object();
                datos.id = id;
                datos.numero = $(this).attr('data-numero');
                datos.total = $(this).attr('data-importe');
                datos.email = $(this).attr('data-email');
                datos.administrador = $(this).attr('data-administrador');
                datos.comunidad = $(this).attr('data-comunidad');

                let pFR = new Promise(async(resolve, reject) =>{
                    await apiFincatech.getView('facturacion','modal_rectificativa',JSON.stringify(datos)).then((result)=>{
                        CoreUI.Modal.CustomHTML(result,'Factura Rectificativa', function(){$('.emailbody').trumbowyg();}, '70%');
                        resolve(true);
                    });                    
                });

                

            });

            //  Generación de factura rectificativa
            $('body').on(core.helper.clickEventType, '.btnModalCrearFacturaRectificativa', function(ev){
                let id = $(this).attr('data-id');
                // let idRectificativa = $(this).attr('data-idrectificativa');                
                facturacion.Facturacion.Controller.GenerarFacturaRectificativa(id, 'null');
            });

            //  Mostrar modal de envío de factura a Administrador
            $('body').on(core.helper.clickEventType, '.btnEnviarFactura', function(ev)
            {

                let id = $(this).attr('data-id');
                let idRectificativa = $(this).attr('data-idrectificativa');
                let numeroRectificativa = $(this).attr('data-rectificativa');

                //  Construimos el objeto que vamos a enviar al WS
                let datos = Object();
                datos.id = id;
                datos.idrectificativa = idRectificativa;
                datos.numerorectificativa = numeroRectificativa;
                datos.numero = $(this).attr('data-numero');
                datos.total = $(this).attr('data-importe');
                datos.email = $(this).attr('data-email');
                datos.administrador = $(this).attr('data-administrador');
                datos.comunidad = $(this).attr('data-comunidad');
                datos.fecha = $(this).attr('data-fecha');

                let pFR = new Promise(async(resolve, reject) =>{
                    await apiFincatech.getView('facturacion','modal_envio',JSON.stringify(datos)).then((result)=>{
                        CoreUI.Modal.CustomHTML(result,'Enviar Factura por E-mail', function(){$('.emailbody').trumbowyg();}, '70%');
                        resolve(true);
                    });                    
                });
            });

            //  Botón de confirmación de envío desde modal de envío por e-mail de factura
            $('body').on(core.helper.clickEventType, '.btnModalEnviarFactura', function(ev)
            {
                let id = $(this).attr('data-id');
                facturacion.Facturacion.Controller.EnviarFactura(id);
            });

        },

        Controller:{

            /**
             * Devuelve el estado de la facturación con los parámetros seleccionados
             * @returns string
             */
            EstadoFacturacion: function(){

                if(typeof(facturacion.Facturacion.Model.InfoFacturacion.estadofacturacion.label_estado) !== 'undefined')
                {
                    return facturacion.Facturacion.Model.InfoFacturacion.estadofacturacion.label_estado;

                }else{
                    return facturacion.Facturacion.Constants.ESTADO_PENDIENTE_FACTURACION;
                }

            },

            /**
             * 
             * @returns 
            */
            Anyo: function(){
                return facturacion.Facturacion.Model.Anyo;
            },

            /**
             * 
             * @param {*} value 
             */
            SetAnyo: function(value){
                facturacion.Facturacion.Model.Anyo = value;
            },

            /**
             * 
             * @returns 
             */
            Mes: function(){
                return facturacion.Facturacion.Model.Mes;
            },

            SetMes: function(value){
                facturacion.Facturacion.Model.Mes = value;
            },

            SetFacturacionAnual: function(value){
                facturacion.Facturacion.Model.FacturacionAnual = value;
            },
            FacturacionAnual: function(){
                return facturacion.Facturacion.Model.FacturacionAnual;
            },

            SetFacturacionMes: function(value){
                facturacion.Facturacion.Model.FacturacionMes = value;
            },
            FacturacionMes: function(){
                return facturacion.Facturacion.Model.FacturacionMes;
            },

            SetMonthCalculo: function(value){
                facturacion.Facturacion.Model.MonthCalculo = value;
            },
            MonthCalculo: function(){
                return facturacion.Facturacion.Model.MonthCalculo;
            },

            SetYearCalculo: function(value){
                facturacion.Facturacion.Model.YearCalculo = value;
            },
            YearCalculo: function(){
                return facturacion.Facturacion.Model.YearCalculo;
            },            

            SetTotalIngresosCuentaPendiente: function(value){
                facturacion.Facturacion.Model.IngresosCuentaPendiente = value;
            },
            TotalIngresosCuentaPendiente: function(){
                return facturacion.Facturacion.Model.IngresosCuentaPendiente;
            },

            SetTotalFacturasMesEmitidas: function(value){
                facturacion.Facturacion.Model.FacturasMesEmitidas = value;
            },
            /**
             * Stats Total Mes en curso emitidas
             * @returns 
             */
            TotalFacturasMesEmitidas: function()
            {
                return facturacion.Facturacion.Model.FacturasMesEmitidas;
            },

            SetTotalFacturasMesDevueltas: function(value){
                facturacion.Facturacion.Model.FacturasMesDevueltas = value;
            },
            /**
             * Stats Total Mes en curso devueltas
             * @returns 
             */
            TotalFacturasMesDevueltas: function()
            {
                return facturacion.Facturacion.Model.FacturasMesDevueltas;
            },

            /**
             * Sets total liquidaciones realizadas
             * @param {*} value 
             */
            SetTotalLiquidaciones: function(value){
                facturacion.Facturacion.Model.TotalLiquidaciones = value;
            },
            /**
             * Total Liquidaciones realizadas a lo largo del tiempo
             * @returns 
             */
            TotalLiquidaciones: function(){
                return facturacion.Facturacion.Model.TotalLiquidaciones;
            },

            /**
             * Sets Total Remesas generadas
             * @param {*} value 
             */
            SetTotalRemesas: function(value){
                facturacion.Facturacion.Model.TotalRemesas = value;
            },
            /**
             * Return Total Remesas
             * @returns 
             */
            TotalRemesas: function(){
                return facturacion.Facturacion.Model.TotalRemesas;
            },

            SetBestCustomer: function(value){
                facturacion.Facturacion.Model.BestCustomer = value;
            },
            BestCustomer: function(){
                return facturacion.Facturacion.Model.BestCustomer;
            },
            SetBestCustomerTotal: function(value){
                facturacion.Facturacion.Model.BestCustomerTotal = value;               
            },
            BestCustomerTotal: function(){
                return facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacion.Facturacion.Model.BestCustomerTotal);
            },

            SetFacturasDevueltas: function(value){
                facturacion.Facturacion.Model.FacturasDevueltas = value;
            },
            FacturasDevueltas: function(){
                return facturacion.Facturacion.Model.FacturasDevueltas;
            },

            /**
             * 
             * @returns Stats Total Facturas Devueltas
            */
            FacturasDevueltasTotal: function(){
               return facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacion.Facturacion.Model.FacturasDevueltasTotal);
            },
            SetFacturasDevueltasTotal: function(value){facturacion.Facturacion.Model.FacturasDevueltasTotal = value;},
            
            /**
             * Recupera y renderiza las estadísticas para el dashboard de facturación
             */
            LoadStatsDashboard: async function()
            {
                //  Establecemos el año y el mes
                facturacion.Facturacion.Controller.SetAnyo(moment().format('Y'));
                facturacion.Facturacion.Controller.SetMes(moment().format('M'));
                
                let p = new Promise(async(resolve, reject) =>{

                    await facturacion.Facturacion.Controller.TotalFacturacionAnual(facturacion.Facturacion.Controller.Mes(), facturacion.Facturacion.Controller.Anyo());
                    resolve(true);

                });

                p.then( resolve =>{
                    facturacion.Facturacion.Controller.Render();
                });
            },

            TotalFacturacionAnual: async function(mes, anyo)
            {
                let data = Object();

                data.year = facturacion.Facturacion.Controller.Anyo();
                // data.month = 10;//facturacion.Facturacion.Controller.Mes();
                data.month = facturacion.Facturacion.Controller.Mes();

                await apiFincatech.post('facturacion/totalfacturacion', (data)).then((result)=>{
                    result = JSON.parse(result);
                    facturacion.Facturacion.Controller.SetFacturacionAnual(result.data.facturacion_anual);
                    facturacion.Facturacion.Controller.SetFacturacionMes(result.data.facturacion_mes);
                    facturacion.Facturacion.Controller.SetMonthCalculo(result.data.month);
                    facturacion.Facturacion.Controller.SetYearCalculo(result.data.year);
                    facturacion.Facturacion.Controller.SetTotalIngresosCuentaPendiente(result.data.ingresoscuentapendiente);
                    facturacion.Facturacion.Controller.SetTotalLiquidaciones(result.data.totalliquidaciones);
                    facturacion.Facturacion.Controller.SetTotalRemesas(result.data.totalremesas);
                    facturacion.Facturacion.Controller.SetFacturasDevueltasTotal(result.data.totalfacturasdevueltas);
                    facturacion.Facturacion.Controller.SetFacturasDevueltas(result.data.facturasdevueltas);
                    facturacion.Facturacion.Controller.SetTotalRemesas(result.data.totalremesas);
                    facturacion.Facturacion.Controller.SetBestCustomer(result.data.bestcustomer);
                    facturacion.Facturacion.Controller.SetBestCustomerTotal(result.data.bestcustomer_total_facturacion);
                });
            },

            /**
             * Recupera la configuración de la facturación
             */
            LoadConfiguration: async function()
            {
                await apiFincatech.get('facturacion/configuracion').then((result)=>{
                    result = JSON.parse(result);
                    core.Forms.mapDataFromModel('formConfiguracion', result.data);
                });
            },

            /**
             * Guarda la configuración para la facturación
             */
            SaveConfiguration: async function(){

                core.Forms.prepareFormDataBeforeSend('formConfiguracion');

                //  Comprobamos que haya informado toda la información
                if(core.Forms.Validate())
                {
                    let p = new Promise(async(resolve, reject) =>{
                        await apiFincatech.post('facturacion/configuracion', core.Forms.data).then((result)=>{
                            resolve(result);
                        });
                    });
    
                    p.then((result)=>{
                        let res = JSON.parse(result);
                        if(res.status.response == 'error'){
                            CoreUI.Modal.Error(res.status.error);
                        }else{
                            CoreUI.Modal.Success('La configuración se ha actualizado correctamente');
                        }
                    });
                }else{
                    CoreUI.Modal.Error(`<p class="text-left">Corrija los errores señalados para poder guardar la configuración actual:<br> ${core.Forms.errorMessage}</p>`,'Error de validación');
                }

            },

            /**
             * Construye el objeto con las opciones de facturación seleccionadas
             * @returns Object 
             */
            GetOptions: function(){
                let datos = Object();

                //  Servicios a facturar
                datos.servicios = Array();
                $('.chkServicio').each(function(ev){
                    if($(this).is(':checked')){
                        datos.servicios.push($(this).val());
                    }
                });

                //  Administrador seleccionado
                datos.idadministrador = $('#usuarioId').val() == null ? -1 : $('#usuarioId').val() ;
                //  Mes de facturación
                datos.mesfacturacion = $('#mesFacturacion').val();
                //  Banco seleccionado
                datos.idBanco = $('#bancoRemesa option:selected').val();
                //  Enviar e-mail al administrador
                datos.envioEmailAdministrador = 1;
                //  Agrupar servicios en una misma factura
                datos.agruparServicios = $('#chkAgrupaServicios').is(':checked');
                //  Agrupar facturas en un archivo comprimido en ZIP
                datos.agruparFacturas = 1;
                //  Envío a Tu comunidad mediante API
                datos.envioAPI = $('#chkAPIComunidad').is(':checked');
                //  Concepto CAE
                datos.conceptoCAE = $('#conceptoCAE').val();
                //  Concepto DOCCAE
                datos.conceptoDOCCAE = $('#conceptoDOCCAE').val();                
                //  Concepto DPD
                datos.conceptoDPD = $('#conceptoDPD').val();
                //  Certificados Digitales
                datos.conceptoCertificadoDigital = $('#conceptoCertificadosDigitales').val();
                //  Texto del e-mail
                datos.emailBody = $('.emailbody').trumbowyg('html');
                return datos;
            },

            /**
             * Genera facturación en el sistema
             */
            GenerarFacturacion: function() {
                $('body').removeClass('progress');
                if (facturacion.Facturacion.Controller.ValidateFacturacion() === true) {
                    CoreUI.Progress.Show();
            
                    // Construimos el objeto para enviar al WS
                    let datos = facturacion.Facturacion.Controller.GetOptions();
            
                    // Llamamos a postWithProgress directamente, ya que es una función asíncrona que retorna una promesa
                    apiFincatech.postWithProgress('facturacion/generar', datos, false)
                        .then((result) => {
                            try {
                                let resultado = JSON.parse(result);
                                if (resultado.data == 'error') {
                                    CoreUI.Modal.Error('Error: ' + resultado.data, 'Error');
                                } else {
                                    CoreUI.Progress.SetMessage(resultado.data);
                                    CoreUI.Progress.Completed();
                                    //  Recargamos la información de la facturación para los parámetros establecidos
                                    facturacion.Facturacion.Controller.InfoFacturacion();
                                }
                            } catch (error) {
                                CoreUI.Modal.Error('Error al procesar la respuesta: ' + error.message, 'Error');
                            }
                        })
                        .catch((error) => {
                            CoreUI.Progress.Hide();
                            CoreUI.Modal.Error('Error: ' + error.status.error, 'Error');
                        });
                }
            },

            /**
             * Valida que una factura no tenga ya asociada una factura rectificativa
             * @param {*} idFacturaRectificativa 
             * @returns 
             */
            ValidarFacturaRectificativa: function(idFacturaRectificativa)
            {
                if(idFacturaRectificativa !== 'null'){
                    CoreUI.Modal.Error('Esta factura ya tiene asociada una factura rectificativa por lo tanto no se puede generar una nueva');
                    return false;
                }
                return true;
            },

            /**
             * Genera una factura rectificativa en el sistema
             * @param {*} idFactura 
             * @param {*} idFacturaRectificativa
             */
            GenerarFacturaRectificativa: function(idFactura, idFacturaRectificativa)
            {
                if(idFacturaRectificativa === 'null'){

                    let data = Object();
                    
                    data.concepto = $('body #conceptoFacturaRectificativa').val();
                    data.importe = $('body #importeFacturaRectificativa').val();
                    data.cuerpo = $('.emailbody').trumbowyg('html');

                    //  Llamamos al WS para generar al rectificativa en el sistema
                    let p = new Promise(async(resolve, reject) =>{
                        await apiFincatech.post(`factura/${idFactura}/rectificativa/create`, data).then((result)=>{
                            resolve(JSON.parse(result));
                        });
                    });

                    p.then((result) =>{

                        if(result.data == 'error'){
                            CoreUI.Modal.Error(result.status.error);
                        }else{
                            CoreUI.Modal.Success('La factura rectificativa ha sido generada correctamente con número: ' + result.data, '', function(){
                                //  Facturas
                                if($('#listadoFacturacion').length)
                                    $('#listadoFacturacion').DataTable().ajax.reload();

                                //  Dashboard Facturas cobradas
                                if($('#listadoFacturasEmitidas').length)
                                    $('#listadoFacturasEmitidas').DataTable().ajax.reload();

                                //  Dashboard Facturas pendientes
                                if($('#listadoFacturasEmitidasPendiente').length)
                                    $('#listadoFacturasEmitidasPendiente').DataTable().ajax.reload();                                
                            });
                        }
                    });
                }else{
                    CoreUI.Modal.Error('Esta factura ya tiene asociada una factura rectificativa por lo tanto no se puede generar una nueva');
                }
            },

            /**
             * Envía una factura a un usuario
             * @param {*} idFactura     ID de la factura que se va a reenviar por e-mail
             */
            EnviarFactura: function(id)
            {
                let cuerpoEmail = $('body .emailbody').trumbowyg('html');
                let asunto = $('body #asuntoEnvioFactura').val();
                let email = $('body #emailEnvioFactura').val();

                // Validación de los datos introducidos
                if(facturacion.Facturacion.Controller.ValidateBeforeSendInvoice())
                {
                    //  Construimos el objeto que vamos a enviar
                    let datos = Object();
                    datos.id = id;
                    datos.asunto = asunto;
                    datos.email = email;
                    datos.cuerpo = cuerpoEmail;

                    //  Llamamos al endpoint que envía el e-mail
                    apiFincatech.post(`factura/${id}/send`, datos).then( (result) =>{
                        let res = JSON.parse(result);
                        if(res.data == 'ok'){
                            CoreUI.Modal.Success('La Factura ha sido enviada por e-mail satisfactoriamente','Factura enviada');
                        }else{
                            //  Seteamos el error en el campo correspondiente
                            CoreUI.Forms.Errors.SetMessage('resultado','Ha ocurrido un error al intentar enviar la factura por e-mail: '  + res.status.error)
                        }
                        
                    });
                }
            },

            /**
             * Recuepera la información de la facturación con los parámetros seleccionados
             */
            InfoFacturacion: async function()
            {
                let datos = facturacion.Facturacion.Controller.GetOptions();

                facturacion.Facturacion.Model.ContratadoCae = false;
                facturacion.Facturacion.Model.ContratadoDpd = false;
                facturacion.Facturacion.Model.ContratadoCertificadosDigitales = false;

                $('.numero-comunidades').html('0');
                $('.importe-facturacion').html('0,00');
                apiFincatech.post( 'facturacion/info', datos ).then((result) =>
                {
                    let resultado = JSON.parse(result);
                    if(resultado.status.response == 'error'){
                        //  Recuperamos los servicios que tiene activos/contratados el administrador
                        facturacion.Facturacion.Model.ContratadoCae = false;
                        facturacion.Facturacion.Model.ContratadoDocCae = false;
                        facturacion.Facturacion.Model.ContratadoDpd = false;
                        facturacion.Facturacion.Model.ContratadoCertificadosDigitales = false;
                        facturacion.Facturacion.View.RemoveAllSelectedServices();
                        facturacion.Facturacion.View.ShowAvailableServices();
                        CoreUI.Modal.Error(`<p class="text-center mb-0">${resultado.status.error}</p>`);
                    }else{
                        facturacion.Facturacion.Model.InfoFacturacion = resultado.data;
                        //  Renderizamos los datos en pantalla
                        facturacion.Facturacion.View.RenderInfoFacturacion();
                    }
                });
            },

            /**
             * Valida los datos obligatorios para procesar la facturación
             * @returns 
             */
            ValidateFacturacion: function()
            {
                let result = true;
                let mensaje = '';

                //  Validación de administrador seleccionado
                let id = $('#usuarioId').val() == null ? -1 : $('#usuarioId').val();
                if(parseInt(id) <= 0){
                    mensaje = `${mensaje}-No ha seleccionado ningún administrador<br>`;
                }

                //  Validamos que haya seleccionado al menos 1 servicio
                if($('.info-servicios .badge').length <= 0){
                    mensaje = `${mensaje}-Debe seleccionar al menos 1 servicio<br>`;
                }

                //  Validación de comunidades
                if(parseInt($('.numero-comunidades').html()) == 0){
                    mensaje = `${mensaje}-No hay comunidades para facturar<br>`;
                }

                //  Validamos que haya seleccionado el banco de destino y, que además, tenga cuenta asociada
                if(!facturacion.Bank.Model.CuentaAsociada){
                    mensaje = `${mensaje}-El banco seleccionado no tiene informado el IBAN<br>`;
                }

                //  Validación del creditor ID
                // if(!facturacion.Bank.Model.creditorid){
                //     mensaje = `${mensaje}-El banco seleccionado no tiene informado el Creditor ID<br>`;
                // }                

                if(mensaje !== ''){
                    result = false;
                    CoreUI.Modal.Error(`<p class="text-left">No se ha podido generar la facturación debido a los siguientes problemas:</p><p class="text-left mb-0">${mensaje}</p>`,'Error de validación');
                }

                return result;
            },

            Render: function()
            {

                //  Facturacion Anual
                let facturacionAnual = facturacion.Facturacion.Controller.FacturacionAnual();
                let totalCae = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionAnual.total_cae);
                let totalDocCae = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionAnual.total_doccae);
                let totalDpd = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionAnual.total_dpd);
                let totalCertificados = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionAnual.total_certificados);
                let total = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionAnual.total);

                $('.stats-facturacion-anual .total_cae').html(totalCae);
                $('.stats-facturacion-anual .total_doccae').html(totalDocCae);
                $('.stats-facturacion-anual .total_dpd').html(totalDpd);
                $('.stats-facturacion-anual .total_certificados').html(totalCertificados);
                $('.stats-facturacion-anual .total_anual').html(total);
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //  Facturación Estimada Mes en curso
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                let totalEmitidas = 0;
                let totalDevueltas = 0;
                let facturacionMes = facturacion.Facturacion.Controller.FacturacionMes();
                let totalFacturasMesEmitidas = 0;
                totalFacturasMesEmitidas += parseFloat(facturacionMes.facturacion_cobradas.total_cae) || 0;
                totalFacturasMesEmitidas += parseFloat(facturacionMes.facturacion_cobradas.total_dpd) || 0;
                totalFacturasMesEmitidas += parseFloat(facturacionMes.facturacion_cobradas.total_certificados) || 0;
                totalFacturasMesEmitidas += parseFloat(facturacionMes.facturacion_cobradas.total_doccae) || 0;
                totalFacturasMesEmitidas = facturacion.Facturacion.Controller.Utils.FormatearNumero(totalFacturasMesEmitidas);
                $('.stats-facturacion-mes .total_mes_facturas_emitidas').html(totalFacturasMesEmitidas);

                total = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_estimada.total);    
                //  CAE
                totalCae = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_estimada.total_cae);
                totalEmitidas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_cobradas.total_cae);
                totalDevueltas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_devueltas.total_cae);
                $('.stats-facturacion-mes .total_cae').html(totalCae);
                $('.stats-facturacion-mes .total_cae_emitidas').html(totalEmitidas);
                $('.stats-facturacion-mes .total_cae_devueltas').html(totalDevueltas);

                // DOC CAE
                totalDocCae = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_estimada.total_doccae);
                totalEmitidas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_cobradas.total_doccae);
                totalDevueltas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_devueltas.total_doccae);
                $('.stats-facturacion-mes .total_doccae').html(totalDocCae);
                $('.stats-facturacion-mes .total_doccae_emitidas').html(totalEmitidas);
                $('.stats-facturacion-mes .total_doccae_devueltas').html(totalDevueltas);
                
                //  DPD
                totalDpd = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_estimada.total_dpd);
                totalEmitidas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_cobradas.total_dpd);
                totalDevueltas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_devueltas.total_dpd);
                $('.stats-facturacion-mes .total_dpd').html(totalDpd);
                $('.stats-facturacion-mes .total_dpd_emitidas').html(totalEmitidas);
                $('.stats-facturacion-mes .total_dpd_devueltas').html(totalDevueltas);                
                //  Certificados digitales
                totalCertificados = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_estimada.total_certificados);
                totalEmitidas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_cobradas.total_certificados);
                totalDevueltas = facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacionMes.facturacion_devueltas.total_certificados);
                $('.stats-facturacion-mes .total_certificados').html(totalCertificados);
                $('.stats-facturacion-mes .total_certificados_emitidas').html(totalEmitidas);
                $('.stats-facturacion-mes .total_certificados_devueltas').html(totalDevueltas);

                $('.stats-facturacion-mes .total_mes').html(total);    
                $('.stats-facturacion-mes .month').html(facturacion.Facturacion.Controller.MonthCalculo());    
                $('.stats-facturacion-mes .year').html(facturacion.Facturacion.Controller.YearCalculo());    

                let totalIngresosCuentaPendientes = facturacion.Facturacion.Controller.TotalIngresosCuentaPendiente();
                let totalLiquidaciones = facturacion.Facturacion.Controller.TotalLiquidaciones();
                let facturasDevueltas = facturacion.Facturacion.Controller.FacturasDevueltas();
                let facturasDevueltasImporte = facturacion.Facturacion.Controller.FacturasDevueltasTotal();

                totalIngresosCuentaPendientes = facturacion.Facturacion.Controller.Utils.FormatearNumero(totalIngresosCuentaPendientes);
                totalLiquidaciones = facturacion.Facturacion.Controller.Utils.FormatearNumero(totalLiquidaciones);

                $('.stats-facturacion-anual .total_ingresoscuenta_pendiente').html(totalIngresosCuentaPendientes);
                $('.stats-facturacion-anual .total_liquidaciones').html(totalLiquidaciones);
                $('.stats-facturacion-anual .facturas_devueltas').html(facturasDevueltas);
                $('.stats-facturacion-anual .facturas_devueltas_importe').html(facturasDevueltasImporte);
                $('.stats-facturacion-anual .total_remesas').html(facturacion.Facturacion.Controller.TotalRemesas());
                //  Mejor Cliente
                let bestCustomer = facturacion.Facturacion.Controller.BestCustomer() + ' [ ' + facturacion.Facturacion.Controller.BestCustomerTotal() + '&euro; ]';
                $('.stats-facturacion-anual .best_customer').html(bestCustomer);

                
            },

            /**
             * Valida los datos capturados en el modal de envío de factura por e-mail antes de ser enviado.
             * @returns {boolean} Resultado de la validación
             */
            ValidateBeforeSendInvoice: function()
            {
                let msgError = '';

                $('.emailEnvioFactura-msg-error').removeClass('d-none').addClass('d-none');

                //  Validamos que haya proporcionado al menos un e-mail
                if($('#swal2-html-container #emailEnvioFactura').val() == ''){
                    //ERROR
                    msgError = 'Debe escribir el e-mail al que desea enviar la factura';
                }

                //  Validamos que los e-mails proporcionados sean válidos
                if($('#swal2-html-container #emailEnvioFactura').val() !== ''){
                    let emails = $('#swal2-html-container #emailEnvioFactura').val().split(';');
                    
                    for(let i = 0; i < emails.length; i++)
                    {
                        if(!core.helper.validarEmail(emails[i])){
                            msgError = `${msgError}El e-mail ${emails[i]} no es válido<br>`;
                        }
                    }
                }

                CoreUI.Forms.Errors.SetMessage('emailEnvioFactura', msgError);

                return msgError !== '' ? false : true;
            },

            /**
             * Renderiza las tablas de datos
             */
            RenderList: function()
            {
                //  Listado de bancos
                if($('#listadoBank').length){
                    facturacion.Facturacion.View.ListBanks();
                }

                //  Facturas emitidas
                if($('#listadoFacturasEmitidas').length){
                    facturacion.Facturacion.View.TableFacturasEmitidas();
                }

                //  Facturas emitidas y pendientes de cobro
                if($('#listadoFacturasEmitidasPendiente').length){
                    facturacion.Facturacion.View.TableFacturasEmitidasPendiente();
                }

                //  Facturas rectificativas
                if($('#listadoFacturacionRectificativas').length){
                    facturacion.Facturacion.View.TableFacturasRectificativas();
                }

                //  Remesas generadas
                if($('#listadoRemesas').length){
                    facturacion.Facturacion.View.TableRemesas();
                }  
                
                //  Listado de todas las facturas
                if($('#listadoFacturacion').length){
                    facturacion.Facturacion.View.TableFacturacion('listadoFacturacion')
                }

                //  Listado de recibos de remesa
                if($('#listadoRecibos').length){
                    facturacion.Facturacion.View.TableRecibos();
                }

                //  Listado de Ingresos a cuenta
                if($('#listadoIngresosCuenta').length){
                    facturacion.Facturacion.View.TableIngresosCuenta();
                }

                //  Listado de liquidaciones
                if($('#listadoLiquidaciones').length)
                {
                    facturacion.Facturacion.View.TableLiquidaciones();
                }
            },

            /**
             * Controller de Bancos
             */
            Bank: {

                /**
                 * Guarda un banco en el repositorio
                 */
                Save: function()
                {
                    let bic = $('#bic').val();

                    if(!facturacion.Facturacion.Controller.Utils.ValidateBIC(bic))
                    {
                        CoreUI.Modal.Error('El código SWIFT/BIC no es válido. Por favor, revíselo para poder continuar', 'Error BIC/Swift');
                        return;
                    }

                    if(core.Forms.Validate('form-banco'))
                    {
            
                    //  Mapeamos los datos
                        core.Forms.mapDataToSave();
            
                    //  Guardamos los datos ya mapeados correctamente
                        core.Forms.Save( true );
            
                    }else{
                        CoreUI.Modal.Error('Rellene los campos obligatorios para poder guardar el banco', 'Formulario incompleto');
                    }
                },

                /**
                 * Eliminar Banco
                 */
                Delete: function(id, nombre){
                    core.Modelo.Delete("bank", id, nombre, "listadoBank");                    
                }
            },


            /**
             * Controller de Ingresos a cuenta
             */
            IngresosCuenta: {

                ValidateBeforeSave: function()
                {

                    //  Validamos los datos del formulario
                    core.Forms.Validate();

                    //  Validamos que haya seleccionado al menos 1 administrador
                    if($('#usuarioId option:selected').val() == '-1')
                        core.Forms.SetError('Debe seleccionar un administrador');

                    //  Validamos que la fecha sea válida

                    //  Validamos que el importe sea válido
                    if(isNaN($('#total').val()))
                        core.Forms.SetError('El valor del importe no es correcto');

                    let errormsg = core.Forms.GetErrorMessage();
                    if(errormsg !== '')
                    {
                        core.Forms.ShowErrorMessage();
                        return false;
                    //   CoreUI.Modal.Error(`Se han detectado los siguientes errores, por favor, corríjalos para continuar:<br><br><p class="text-left">${errormsg}</p>`,'Error');
                    }else{
                        return true;
                    }

                },

                Save: function()
                {
                    if(facturacion.Facturacion.Controller.IngresosCuenta.ValidateBeforeSave())
                    {
                        //  Mapeamos los datos iniciales
                        core.Forms.mapFormDataToSave();
                        //  Guardamos los datos ya mapeados correctamente
                        core.Forms.Save( true );                            
                    }
                },

                /**
                 * Elimina un ingreso a cuenta del sistema
                 * @param {*} id ID del ingreso a cuenta
                 * @param {*} procesado Estado del ingreso. Procesado o pendiente de procesar
                 * @param {*} importe Importe
                 * @param {*} fecha Fecha de la anotación
                 * 
                 */
                Delete: function(id, concepto, procesado, importe, fecha, administrador)
                {
                    //  Si ya está procesado avisamos al usuario y evitamos su eliminación
                    if( Boolean(parseInt(procesado)) === true){
                        CoreUI.Modal.Error('Este ingreso ya ha sido procesado en una liquidación por lo tanto no puede ser eliminado');
                    }else{
                        let msg = `<p class="mb-0 text-left"><span class="font-weight-bold">Administrador</span>: ${administrador}</p>
                        <p class="mb-0 text-left"><span class="font-weight-bold">Concepto del ingreso a cuenta</span>: ${concepto}</p>
                        <p class="mb-0 text-left"><span class="font-weight-bold">Importe</span>: ${Number(importe).toFixed(2)}&euro;</p>
                        <p class="mb-0 text-left"><span class="font-weight-bold">Fecha del apunte</span>: ${fecha}</p>`;

                        //  Procedemos a su eliminación previa confirmación al usuario
                        Swal.fire({
                            title: `¿Desea eliminar el ingreso a cuenta?`,
                            html:  '<p class="font-weight-bold text-danger">Atención: Esta acción es irreversible</p>' + msg,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Eliminar',
                            cancelButtonText: 'Cancelar'
                          }).then((result) => {
                            if (result.isConfirmed) {
                                //  Llamamos al endpoint de eliminar
                                apiFincatech.delete(`ingresoscuenta`, id).then((result) =>{
                                    result = JSON.parse(result);
                                    if(result.data == 'error'){
                                        CoreUI.Modal.Error(result.status.error);
                                    }else{
                                        CoreUI.Modal.Success('El apunte de ingreso a cuenta se ha eliminado correctamente');
                                        facturacion.Facturacion.View.TableIngresosCuenta();
                                    }
                                });
                            }
                        });  
                    }
                }

            },

            Utils: {
                /**
                 * Formatea un número para añadirle separador de miles
                 * @param {*} numero 
                 * @returns 
                 */
                FormatearNumero: function(numero)
                {
                    let partes = parseFloat(numero).toFixed(2).split('.');
                    let entero = partes[0];
                    let decimales = partes[1];
                
                    // Añadir separador de miles
                    entero = entero.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                
                    // Combinar la parte entera con la parte decimal
                    return entero + ',' + decimales;
                    
                },

                /**
                 * Valida el BIC/Swift del Banco
                 * @param {*} bic 
                 * @returns Boolean
                 */
                ValidateBIC: function(bic){
                        // Expresión regular para validar BIC/SWIFT
                        const regex = /^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/;
                        return regex.test(bic);
                }
            }

        },

        Model: {
            Anyo: null,
            Mes: null,

            MonthCalculo: null,
            YearCalculo: null,

            FacturacionAnual: null,
            FacturacionMes: null,
            FacturasEmitidas: null,
            FacturasPendientesCobro: null,
            Remesas: null,

            //  Stats
            IngresosCuentaPendiente: 0,
            TotalLiquidaciones: 0,
            TotalRemesas: 0,
            BestCustomer: '',
            BestCustomerTotal: 0,
            FacturasDevueltas: 0,
            FacturasDevueltasTotal: 0,
            FacturasMesEmitidas: 0,
            FacturasMesDevueltas: 0,

            //  Modelo que se utiliza para almacenar la información previa a la generación de las facturas correspondientes
            InfoFacturacion: null,

            //  Control de servicios contratados
            ContratadoCae: false,
            ContratadoDocCae: false,
            ContratadoDpd: false,
            ContratadoCertificadosDigitales: false

        },

        View: {

            /**
             * Listado de bancos dados de alta en el sistema
             */
            ListBanks: function()
            {
                if($('#listadoBank').length)
                    {
                        //  Cargamos el listado de comunidades
                        CoreUI.tableData.init();
                        //  Nombre
                        CoreUI.tableData.addColumn('listadoBank', "nombre", "NOMBRE", null, '','60%');
            
                        //  BIC
                        CoreUI.tableData.addColumn('listadoBank', "bic", "BIC/SWIFT");
                       
                        //  Código entidad
                        CoreUI.tableData.addColumn('listadoBank', "codigo", "Código Entidad");

                        //  Creditor ID
                        CoreUI.tableData.addColumn('listadoBank', "creditorid", "Creditor ID");                        

                        //  IBAN
                        CoreUI.tableData.addColumn('listadoBank', "iban", "IBAN");

                        //  Columna de acciones
                        var html = '<ul class="nav justify-content-center accionesTabla">';
                        // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                        // html += `<li class="nav-item"><a href="${baseURL}bank/data:id$?view=1" class="btnEditarBanco d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarBanco d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>';
                        CoreUI.tableData.addColumn('listadoBank', null, "", html, '','80px');

                        //  Render
                        CoreUI.tableData.render("listadoBank", "Bank", `bank/list`);
                    }
            },

            /**
             * Renderiza la tabla con todas las facturas emitidas independientemente de su estado
             * @param {*} domTablaNombre 
             */
            TableFacturacion: function(domTablaNombre)
            {
                let nombreTabla = domTablaNombre;
                //  Inicializamos el datatable
                CoreUI.tableData.init();
                //  Serie de facturación
                CoreUI.tableData.addColumn(nombreTabla, "numero", "Nº Fra.", null, 'text-center','60px');
                //  Fichero PDF
                // CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                // {
                //     let salida;
                //     let rectificativa;
                //     if(row.idrectificativa){
                //         //TODO: Recuperamos el ID de la factura rectificativa
                //         rectificativa = `<p class="mb-0 text-center">
                //             <a href="" title="Ver PDF Fra. Rectificativa" target="_blank"><i class="bi bi-filetype-pdf text-danger" style="font-size: 21px;"></i></a>
                //         </p>`;
                //     }
                //     let fichero = `https://factura.fincatech.es/pdf/${row.fichero}.pdf`
                //     salida = `
                //     <p class="mb-0 text-center">
                //         <a href="${fichero}" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-primary" style="font-size: 21px;"></i></a>
                //     </p>`;
                //     return salida;
                // }, 'DOC.', null, 'text-center');

                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, "administrador", "Administrador");

                //  Comunidad
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    if(row.comunidad){
                        salida = `${row.comunidad[0]['codigo']} - ${row.comunidad[0]['nombre']}`;
                    }
                    return salida;

                }, 'Comunidad', null);                

                //  CIF Comunidad
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    if(row.comunidad){
                        salida = `${row.comunidad[0]['cif']}`;
                    }
                    return salida;

                }, 'CIF Comunidad', null, 'text-center');  

                //  Fecha factura
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    let fecha = moment(row.dateinvoice).locale('es').format('L');
                    salida = `${fecha}`;
                    return salida;

                }, 'Fecha Fra.', null, 'text-center', '120px');

                //  Fecha de cobro
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let fecha;
                    
                    if(row.datepaid){
                        fecha = moment(row.datepaid).locale('es').format('L');
                    }else{
                        fecha = '-';
                    }
                    salida = `${fecha}`;
                    return salida;

                }, 'Fecha de Cobro', null, 'text-center', '120px');

                //  Fecha de devolución
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let fecha;
                    
                    if(row.datereturned){
                        fecha = moment(row.datereturned).locale('es').format('L');
                    }else{
                        fecha = '-';
                    }
                    salida = `${fecha}`;
                    return salida;

                }, 'Fecha Devolución', null, 'text-center', '120px');

                //  Importe
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    let importe = parseFloat(row.total_taxes_inc).toFixed(2);
                    salida = `${importe}&euro;`;
                    return salida;

                }, 'Importe', null, 'text-center');

                //  Fra. Rectificativa.
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {

                    if(!row.idrectificativa){
                        return '<span class="badge bg-success">No</span>';
                    }else{
                        let fichero = `https://factura.fincatech.es/pdf/${row.invoicerectificativa[0].nombrefichero}`;
                        return `
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="${fichero}" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-dark img-fluid icono-accion" style="font-size: 21px;"></i></a>
                            </div>`;
                    }

                }, 'Fra. Rectificativa', null, 'text-center');

                //  Liquidada
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    if(row.liquidada == 1){
                        salida = `<span class="badge bg-success">Sí</span>`;
                    }else{
                        salida = `<span class="badge bg-warning">No</span>`;
                    }
                    return salida;

                }, 'Liquidada', null, 'text-center');  

                //  Estado
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let colorEstado;
                    let tituloEstado;

                    switch(row.estado)
                    {
                        case 'P':
                            colorEstado = 'warning';
                            tituloEstado = 'Pendiente';
                            break;
                        case 'D':
                            colorEstado = 'danger';
                            tituloEstado = 'Devuelta';
                            break;
                        case 'C':
                            colorEstado = 'success';
                            tituloEstado = 'Cobrada';
                            break;
                    }
                    salida = `<span class="badge bg-${colorEstado}">${tituloEstado}</span>`;
                    return salida;

                }, 'Estado', null, 'text-center');

                //  Columna de acciones
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let rectificativaNumero = '';
                    //  Comprobamos si tiene factura rectificativa asociada
                    if(row.invoicerectificativa.length > 0){
                        rectificativaNumero = row.invoicerectificativa[0].numero;
                    }

                    let data = `data-id="${row.id}" data-numero="${row.numero}" data-email="${row.email}" data-administrador="${row.administrador}" data-numero="${row.id}"`;
                    data =`${data} data-comunidad="${row.comunidad[0].nombre}" data-rectificativa="${rectificativaNumero}" data-fecha="${moment(row.dateinvoice).locale('es').format('L')}" data-importe="${row.total_taxes_exc}" data-idrectificativa="${row.idrectificativa}"`;

                    let fichero = `https://factura.fincatech.es/pdf/${row.fichero}.pdf`

                    var html = '<ul class="nav justify-content-center accionesTabla">';
                    //  Enlace documento pdf
                    html += `<li class="nav-item"><a href="${fichero}" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-success img-fluid icono-accion" style="font-size: 18px;"></i></a></li>`;
                    //  Enlace de ver factura
                    html += `<li class="nav-item"><a href="invoice/${row.id}" data-id="${row.id}" title="Ver factura"><i data-feather="eye" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    //  Enviar factura por e-mail
                    html += `<li class="nav-item"><a href="javascript:void(0);" class="btnEnviarFactura" ${data} title="Enviar factura por e-mail"><i data-feather="mail" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    //  Botón crear factura rectificativa
                    html += `<li class="nav-item"><a href="javascript:void(0);" class="btnCrearFacturaRectificativa ml-1" ${data} title="Generar factura rectificativa"><i data-feather="rotate-ccw" class="text-danger img-fluid"></i></a></li>`;
                    html += '</ul>';
                    return html;
                }, '&nbsp;', null, 'text-center', '100px');                

                // CoreUI.tableData.addColumn(nombreTabla, null, "", html, '','80px');
                $('#' + nombreTabla).addClass('no-clicable');
                //  Render
                CoreUI.tableData.render(nombreTabla, "Invoice", `invoice/list`, false, true, true, null, true, false, false, true, 'GET', false, true);  
            },

            TableFacturas: function(domTablaNombre, estado)
            {
                let nombreTabla = domTablaNombre;
                //  Inicializamos el datatable
                CoreUI.tableData.init();
    
                //  Número de factura
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){

                    let salida;
                    salida = `<p class="mb-0">${row.numero}</p>`;
                    return salida;
                }, 'Nº Fra.', null, 'text-center', '80px');
                //  "numero", "Nº Fra.", null, '','60px');
                  
                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, "administrador", "Administrador");

                //  Comunidad
                CoreUI.tableData.addColumn(nombreTabla, "comunidad[0].nombre", "Comunidad");

                //  Fecha factura
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){

                    let salida;
                    salida = moment(row.dateinvoice).format('DD/MM/YYYY');
                    return salida;
                },  'Fecha Fra.', null, 'text-center');

                //  Importe
                CoreUI.tableData.addColumn(nombreTabla, "total_taxes_inc", "Importe");

                //  Estado
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let colorEstado;
                    let tituloEstado;

                    switch(row.estado)
                    {
                        case 'P':
                            colorEstado = 'danger';
                            tituloEstado = 'Pendiente';
                            break;
                        case 'D':
                            colorEstado = 'warning';
                            tituloEstado = 'Devuelta';
                            break;
                        case 'C':
                            colorEstado = 'success';
                            tituloEstado = 'Cobrada';
                            break;
                    }
                    salida = `<span class="badge bg-${colorEstado}">${tituloEstado}</span>`;
                    return salida;

                }, 'Estado', null, 'text-center');                

                //  Fichero PDF
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let rectificativa;
                    if(row.idrectificativa){
                        //TODO: Recuperamos el ID de la factura rectificativa
                        rectificativa = `<p class="mb-0 text-center">
                            <a href="" title="Ver PDF Fra. Rectificativa" target="_blank"><i class="bi bi-filetype-pdf text-danger" style="font-size: 21px;"></i></a>
                        </p>`;
                    }
                    salida = `
                    <p class="mb-0 text-center">
                        <a href="" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-primary" style="font-size: 21px;"></i></a>
                    </p>`;
                    return salida;
                }, 'DOC.', null, 'text-center');

                //  Columna de acciones
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {

                    let rectificativaNumero= '';
                    //  Comprobamos si tiene factura rectificativa asociada
                    if(row.invoicerectificativa.length > 0){
                        rectificativaNumero = row.invoicerectificativa[0].numero;
                    }

                    let data = `data-id="${row.id}" data-numero="${row.numero}" data-email="${row.email}" data-administrador="${row.administrador}" data-numero="${row.id}"`;
                    data =`${data} data-comunidad="${row.comunidad[0].nombre}" data-rectificativa="${rectificativaNumero}" data-fecha="${moment(row.dateinvoice).locale('es').format('L')}" data-importe="${row.total_taxes_exc}" data-idrectificativa="${row.idrectificativa}"`;

                    var html = '<ul class="nav justify-content-center accionesTabla">';
                    //  Enlace de ver factura
                    html += `<li class="nav-item"><a href="invoice/${row.id}" data-id="${row.id}" title="Ver factura"><i data-feather="eye" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    //  Enviar factura por e-mail
                    html += `<li class="nav-item"><a href="javascript:void(0);" class="btnEnviarFactura" ${data} title="Enviar factura por e-mail"><i data-feather="mail" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    //  Botón crear factura rectificativa
                    html += `<li class="nav-item"><a href="javascript:void(0);" class="btnCrearFacturaRectificativa ml-1" ${data} title="Generar factura rectificativa"><i data-feather="rotate-ccw" class="text-danger img-fluid"></i></a></li>`;
                    html += '</ul>';
                    return html;
                }, '&nbsp;', null, 'text-center', '100px');   

                //  No Clicable
                $('#' + nombreTabla).addClass('no-clicable');
                //  Render
                CoreUI.tableData.render(nombreTabla, "Invoice", `facturacion/listado/${estado}`, false, true, true);  
                // CoreUI.tableData.render(nombreTabla, "Invoice", `facturacion/listado/${estado}`, false, true, true, null, true, false, null, true, 'GET', false, true);  
            },

            TableFacturasEmitidas: function()
            {
                facturacion.Facturacion.View.TableFacturas('listadoFacturasEmitidas','c');
            },

            /**
             * Tabla facturas emitidas pendientes de cobro
             */
            TableFacturasEmitidasPendiente: function()
            {
                facturacion.Facturacion.View.TableFacturas('listadoFacturasEmitidasPendiente','D');
            },

            TableFacturasRectificativas: function()
            {
                let nombreTabla = 'listadoFacturacionRectificativas';
                //  Inicializamos el datatable
                CoreUI.tableData.init();
                //  Serie de facturación
                CoreUI.tableData.addColumn(nombreTabla, "numero", "Nº Fra.", null, 'text-start','120px');

                //  Fra. Asociada
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {

                    let fichero = `https://factura.fincatech.es/pdf/${row.nombrefichero}.pdf`;
                    return `
                        <div class="d-flex align-items-center">
                            <a href="${fichero}" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-dark img-fluid icono-accion" style="font-size: 21px;"></i></a>
                            <p class="mb-0 text-center">${row.invoice[0].numero}</p>
                        </div>`;

                }, 'Fra. Asociada', null, 'text-start', '120px');

                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, "administrador", "Administrador");

                //  CIF Comunidad
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    if(row.comunidad){
                        salida = `${row.comunidad[0]['cif']}`;
                    }
                    return salida;

                }, 'CIF Comunidad', null, 'text-center');  

                //  Comunidad
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    if(row.comunidad){
                        salida = `${row.comunidad[0]['codigo']} - ${row.comunidad[0]['nombre']}`;
                    }
                    return salida;

                }, 'Comunidad', null);                

                //  Fecha factura
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    let fecha = moment(row.created).locale('es').format('L');
                    salida = `${fecha}`;
                    return salida;

                }, 'Fecha', null, 'text-center', '120px');

                //  Importe
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                    let importe = parseFloat(row.total_taxes_inc).toFixed(2);
                    salida = `${importe}&euro;`;
                    return salida;

                }, 'Importe', null, 'text-center');

                //  Columna de acciones
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {

                    let data = `data-id="${row.id}" data-numero="${row.numero}" data-email="${row.email}" data-administrador="${row.administrador}" data-numero="${row.id}"`;
                    data =`${data} data-comunidad="${row.comunidad[0].nombre}" data-invoice="${row.idinvoice}" data-fecha="${moment(row.creeated).locale('es').format('L')}" data-importe="${row.total_taxes_exc}" data-idrectificativa="${row.id}"`;

                    let fichero = `https://factura.fincatech.es/pdf/${row.nombrefichero}.pdf`

                    var html = '<ul class="nav justify-content-center accionesTabla">';
                    //  Enlace documento pdf
                    html += `<li class="nav-item"><a href="${fichero}" title="Ver PDF generado" target="_blank"><i class="bi bi-filetype-pdf text-success img-fluid icono-accion" style="font-size: 18px;"></i></a></li>`;
                    //  Enlace de ver factura
                    html += `<li class="nav-item"><a href="invoicerectificativa/${row.id}" data-id="${row.id}" title="Ver factura"><i data-feather="eye" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    //  Enviar factura por e-mail
                    html += `<li class="nav-item"><a href="javascript:void(0);" class="btnEnviarFactura" ${data} title="Enviar factura por e-mail"><i data-feather="mail" class="text-success img-fluid icono-accion" style="width:18px;height:18px;"></i></li>`;
                    html += '</ul>';
                    return html;
                }, '&nbsp;', null, 'text-center', '100px');                

                // CoreUI.tableData.addColumn(nombreTabla, null, "", html, '','80px');
                $('#' + nombreTabla).addClass('no-clicable');
                //  Render
                CoreUI.tableData.render(nombreTabla, "InvoiceRectificativa", `invoicerectificativa/list`, false, true, true, null, true, false, false, true, 'GET', false, true);  
            },

            /**
             * Tabla facturas emitidas pendientes de cobro
             */
            TableRemesas: function()
            {
                let nombreTabla = 'listadoRemesas';
                //  Inicializamos el datatable
                CoreUI.tableData.init();

                //  Fecha de la remesa
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                        salida = moment(row.created).format('DD/MM/YYYY');
                    return salida;

                }, 'Fecha de remesa', null, 'text-center', '120px');                      

                //  Nombre fichero
                CoreUI.tableData.addColumn(nombreTabla, "referencia", 'Referencia', null);
                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, "customername", "Administrador", null);


                //  Banco
                CoreUI.tableData.addColumn(nombreTabla, "creditoraccountiban", "Banco de cobro");
                //  Total recibos
                CoreUI.tableData.addColumn(nombreTabla, 'totalrecibos','Nº de recibos', null, 'text-center', '100px');
                //  Total Remesa
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){
                    let salida = Number(row.totalremesa).toFixed(2) + '&euro;';
                    return '<p class="mb-0">'+salida+'</p>';
                }, 'Total Remesa', null, 'text-center','100px');
                                                
                //  Columna de acciones
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                    // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}remesa/${row.id}" class="btnEditarRemesa d-inline-block"><i data-feather="eye" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                    html += `<li class="nav-item"><a href="${baseURL}public/storage/remesas/${row.referencia}.xml" target="_blank" class="btnDescargarRemesa d-inline-block" download><i data-feather="download-cloud" class="text-primary img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>`;
                    return html;
                },'', null, 'text-center','80px');
                // CoreUI.tableData.addColumn(nombreTabla, null, "", html, '','80px');

                //  Render
                CoreUI.tableData.render(nombreTabla, "Remesa", `remesa/list`);                

            },
            
            /**
             * Renderiza la tabla de recibos pertenecientes a una remesa
             */
            TableRecibos: function()
            {
                let remesaId = core.modelId;
                let nombreTabla = 'listadoRecibos';
                //  Inicializamos el datatable
                CoreUI.tableData.init();

                //  Comunidad
                CoreUI.tableData.addColumn(nombreTabla, "customername", "Comunidad", null, '');
                //  Concepto
                CoreUI.tableData.addColumn(nombreTabla, "descripcion", "Concepto", null, '');
                //  IBAN
                CoreUI.tableData.addColumn(nombreTabla, "customeriban", "CC domiciliación", null, '');
                //  Total recibo
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida = '';
                        salida = Number(row.amount).toFixed(2) + '&euro;';
                    return salida;

                }, 'Importe', null, 'text-center');
                //  Estado del recibo
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let texto;
                    let color;
                    let fechaDevolucion = '';
                    
                        //salida = Number(row.amount).toFixed(2) + '&euro;';
                    switch (row.estado)
                    {
                        case 'C':
                            texto = 'Cobrado';
                            color = 'success';
                            break;
                        case 'D':
                            texto = 'Devuelto';
                            color = 'danger';
                            fechaDevolucion = moment(row.datereturned).format('DD/MM/YYYY');
                            break;
                    }

                    salida = `<span class="badge bg-${color}">${texto}</span>`;
                    if(fechaDevolucion !== ''){
                        salida += '<p class="mb-0 d-block"><small>' + fechaDevolucion + '</p>';
                    }

                    return salida;

                }, 'Estado', null, 'text-center');


                //  Columna de acciones
                // CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){
                //     var html = '<ul class="nav justify-content-center accionesTabla">';
                //     // html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                //     html += `<li class="nav-item"><a href="${baseURL}remesa/${row.id}" class="btnEditarRemesa d-inline-block"><i data-feather="search" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                //     html += `<li class="nav-item"><a href="${baseURL}public/storage/remesas/${row.referencia}.xml" target="_blank" class="btnDescargarRemesa d-inline-block" download><i data-feather="download-cloud" class="text-primary img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>`;
                //     return html;
                // },'', null, 'text-center','80px');

                //  Render
                $('#' + nombreTabla).addClass('no-clicable');
                CoreUI.tableData.render(nombreTabla, "Recibos", `remesa/${remesaId}/recibos`);                  
            },

            /**
             * Renderiza la tabla de recibos pertenecientes a una remesa
             */
            TableIngresosCuenta: function()
            {
                let nombreTabla = 'listadoIngresosCuenta';
                
                //  Inicializamos el datatable
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

                //   Fecha de ingreso a cuenta
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let texto;
                    let color;
                    let fechaIngreso = '';
                    fechaIngreso = moment(row.fechaingreso).format('DD/MM/YYYY');

                    return fechaIngreso;

                }, 'Fecha Ingreso', null, 'text-center', '100px');

                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        let salida = row.usuario[0].nombre;
                        return salida;

                }, 'Administrador', null);
                //  Id Liquidación
                //CoreUI.tableData.addColumn(nombreTabla, "descripcion", "Concepto", null, '');
                //  Concepto
                CoreUI.tableData.addColumn(nombreTabla, "concepto", "Concepto", null, '');
                //  Total
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    // console.log('Total: ' + row.total);
                        let total = CoreUI.Utils.formatNumberToCurrency(row.total);
                        let salida = '';
                        salida = `<p class="mb-0 text-right">${total}&euro;</p>`;
                        return salida;

                }, 'Importe', null, 'text-center');

                //  Procesado
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        let salida = '';
                        let color = '';
                        let texto = '';
                        if(row.procesado == 0){
                                texto = 'No';
                                color = 'danger';
                        }else{
                                texto = 'Sí';
                                color = 'success';
                        }

                        salida = `<span class="badge bg-${color}">${texto}</span>`;
                        return salida;

                }, 'Liquidado', null, 'text-center');

                //  Columna de acciones
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        var html = '<ul class="nav justify-content-center accionesTabla">';

                        //  Editar ingreso a cuenta  
                        html += `<li class="nav-item"><a href="${baseURL}ingresoscuenta/${row.id}" class="btnEditarIngreso d-inline-block"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
                        //  Eliminar ingreso a cuenta
                        html += `<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarIngresoCuenta d-inline-block" data-id="${row.id}" data-administrador="${row.usuario[0].nombre}" data-importe="${row.total}" data-fecha="${moment(row.fechaingreso).format('DD/MM/YYYY')}" data-procesado="${row.procesado}" data-concepto="${row.concepto}"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>`;
                        html += '</ul>';

                    return html;
                },'', null, 'text-center','80px');

                //  Render
                //    $('#' + nombreTabla).addClass('no-clicable');
                CoreUI.tableData.render(nombreTabla, "IngresosCuenta", `IngresosCuenta/list`);                  
            },

            /**
             * Renderiza la tabla de recibos pertenecientes a una remesa
             */
            TableLiquidaciones: function()
            {
                let nombreTabla = 'listadoLiquidaciones';
                
                //  Inicializamos el datatable
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

                //   Fecha de Generación
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                    let salida;
                    let texto;
                    let color;
                    let fechaIngreso = '';
                    fechaIngreso = moment(row.created).format('DD/MM/YYYY');

                    return fechaIngreso;

                }, 'Fecha Generación', null, 'text-center', '100px');

                //  Período
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta){

                    let salida;

                    if(!row.datefrom){
                        salida = `Todo hasta`;
                    }else{
                        salida = `Desde ${moment(row.datefrom).format('DD/MM/YYYY')} hasta`;
                    }

                    salida = `${salida} ${moment(row.dateto).format('DD/MM/YYYY')}`;
                    return salida;
                }, 'Período liquidado', null, 'text-center', '200px');

                //  Referencia
                CoreUI.tableData.addColumn(nombreTabla, "referencia", "Referencia", null, null, '270px');

                //  Administrador
                CoreUI.tableData.addColumn(nombreTabla, "administrador", "Administrador", null, '');

                //  Estado
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        let salida = '';
                        let color = '';
                        let texto = '';
                        let fechaPago = '';

                        if(!row.fechapago){
                                texto = 'Pendiente de pago';
                                color = 'danger';
                        }else{
                                texto = 'Pagada';
                                color = 'success';
                                fechaPago = moment(row.fechapago).format('DD/MM/AAAA');
                        }

                        salida = `<span class="badge bg-${color}">${texto}</span>`;
                        if(fechaPago !== ''){
                            salida = `${salida}<p class="mb-0 d-block">${fechaPago}</p>`;
                        }

                        return salida;

                }, 'Estado', null, 'text-center', '100px');

                //  Nº de facturas
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        let salida = `<span class="badge bg-primary">${row.liquidaciondetalle.length}</span>`;
                        return salida;

                }, 'Nº Facturas', null, 'text-center', '100px');

                //  Total
                CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
                {
                        let total = CoreUI.Utils.formatNumberToCurrency(row.total_taxes_exc);
                        let salida = '';
                        salida = `<p class="mb-0 text-right pr-2">${total}&euro;</p>`;
                        return salida;

                }, 'Total', null, 'text-right', '100px');

                //  Columna de acciones
            //    CoreUI.tableData.addColumn(nombreTabla, function(row, type, val, meta)
            //    {
            //         var html = '<ul class="nav justify-content-center accionesTabla">';
            //         //  Editar ingreso a cuenta  
            //         html += `<li class="nav-item"><a href="${baseURL}ingresoscuenta/${row.id}" class="btnEditarIngreso d-inline-block"><i data-feather="edit" class="text-success img-fluid icono-accion" style="width:32px;height:32px;"></i></a></li>`;
            //         //  Eliminar ingreso a cuenta
            //         html += `<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarIngresoCuenta d-inline-block" data-id="${row.id}" data-administrador="${row.usuario[0].nombre}" data-importe="${row.total}" data-fecha="${moment(row.fechaingreso).format('DD/MM/YYYY')}" data-procesado="${row.procesado}" data-concepto="${row.concepto}"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="width:26px;height:26px;"></i></li></ul>`;
            //         html += '</ul>';

            //        return html;
            //    },'', null, 'text-center','80px');

                //  Render
                $('#' + nombreTabla).addClass('no-clicable');
                CoreUI.tableData.render(nombreTabla, "Liquidacion", `liquidaciones/list`);                  
            },

            RenderInfoFacturacion: function()
            {

                //  Nº de comunidades
                $('.numero-comunidades').html(facturacion.Facturacion.Model.InfoFacturacion.numerocomunidades);

                //  Total para facturar
                $('.importe-facturacion').html(facturacion.Facturacion.Model.InfoFacturacion.totalfacturacion);

                //  Total servicios a facturar
                $('.total-servicios .total-1 .importe').html(facturacion.Facturacion.Model.InfoFacturacion.total_cae);
                $('.total-servicios .total-2 .importe').html(facturacion.Facturacion.Model.InfoFacturacion.total_dpd);
                $('.total-servicios .total-3 .importe').html(facturacion.Facturacion.Model.InfoFacturacion.total_doccae);
                $('.total-servicios .total-5 .importe').html(facturacion.Facturacion.Model.InfoFacturacion.total_certificados);

                //  Nº total de facturas devueltas
                let iFacturasDevueltas = facturacion.Facturacion.Model.InfoFacturacion.estadofacturacion.devueltas;
                //  Nº total de facturas cobradas
                let iFacturasCobradas = facturacion.Facturacion.Model.InfoFacturacion.estadofacturacion.cobradas;
                //  Nº total de facturas pendientes de cobro
                let iFacturasPendientes = facturacion.Facturacion.Model.InfoFacturacion.estadofacturacion.pendientes;
                //  Agrupar servicios en factura
                $('#chkAgrupaServicios').prop('checked', facturacion.Facturacion.Model.InfoFacturacion.configuracion.optagrupaservicios);
                //  Envío de facturas a tu comunidad
                $('#chkAPIComunidad').prop('checked', facturacion.Facturacion.Model.InfoFacturacion.configuracion.optenvioapi);

                //  Recuperamos los servicios que tiene activos/contratados el administrador
                facturacion.Facturacion.Model.ContratadoCae = Boolean(Number(facturacion.Facturacion.Model.InfoFacturacion.servicios.cae));
                facturacion.Facturacion.Model.ContratadoDocCae = Boolean(Number(facturacion.Facturacion.Model.InfoFacturacion.servicios.doccae));
                facturacion.Facturacion.Model.ContratadoDpd = Boolean(Number(facturacion.Facturacion.Model.InfoFacturacion.servicios.dpd));
                facturacion.Facturacion.Model.ContratadoCertificadosDigitales = Boolean(Number(facturacion.Facturacion.Model.InfoFacturacion.servicios.certificadosdigitales));

                //  Quitamos toda la información de servicios seleccionados
                facturacion.Facturacion.View.RemoveAllSelectedServices();
                facturacion.Facturacion.View.ShowAvailableServices();

                if(facturacion.Facturacion.Model.ContratadoCae === true){
                    facturacion.Facturacion.View.AddServicioToInfo(1, 'CAE');
                    $('.servicio-cae').show();
                }

                if(facturacion.Facturacion.Model.ContratadoDocCae === true){
                    facturacion.Facturacion.View.AddServicioToInfo(3, 'DOCCAE');
                    $('.servicio-doccae').show();
                }

                if(facturacion.Facturacion.Model.ContratadoDpd === true){
                    facturacion.Facturacion.View.AddServicioToInfo(2, 'DPD');
                    $('.servicio-dpd').show();
                }

                if(facturacion.Facturacion.Model.ContratadoCertificadosDigitales === true){
                    $('.servicio-certificadosdigitales').show();
                    facturacion.Facturacion.View.AddServicioToInfo(5, 'Certificados digitales');
                }

                //  Estado facturación
                let estadoHTML = '';
                let bgEstado = 'success';
                let tituloEstado = '';
                let colorEstado = 'white';

                switch(facturacion.Facturacion.Controller.EstadoFacturacion())
                {
                    case facturacion.Facturacion.Constants.ESTADO_FACTURADO:
                        bgEstado = 'success';
                        tituloEstado = `${iFacturasCobradas} Facturadas`;
                        break;                    
                    case facturacion.Facturacion.Constants.ESTADO_FACTURAS_DEVUELTAS:
                        bgEstado = 'warning';
                        tituloEstado = `${iFacturasDevueltas} Facturas devueltas`;
                        colorEstado = 'dark';
                        break;
                    case facturacion.Facturacion.Constants.ESTADO_PENDIENTE_FACTURACION:
                        bgEstado = 'danger';
                        tituloEstado = `${iFacturasPendientes} Pendiente de cobrar`;
                        break;                                                    
                }

                $('.estado-facturacion').html(`<span class="badge px-3 font-weight-normal text-${colorEstado} bg-${bgEstado}">${tituloEstado}</span>`);

            },

            /**
             * Añade un badge con la información del servicio seleccionado por el usuario
             * @param {*} idServicio 
             * @param {*} nombreServicio 
             */
            AddServicioToInfo: function(idServicio, nombreServicio)
            {
                //  Comprobamos si hay texto o servicios
                if($('.info-servicios .badge').length == 0){
                    $('.info-servicios').html('');
                }

                //  Validamos si el servicio está añadido ya
                if($(`.info-servicios .servicio-${idServicio}`).length <= 0){
                    let htmlServicio = `<span class="badge mr-2 rounded-pill bg-info font-weight-normal text-white servicio-${idServicio}">${nombreServicio}</span>`;
                    //  Añadimos el servicio
                    $('.info-servicios').append(htmlServicio);
                    $('.total-servicios').removeClass('d-none');
                    $(`.total-servicios .total-${idServicio}`).removeClass('d-none');
                }
            },

            /**
             * Elimina un badge de info servicios 
             * @param {*} idServicio 
             */
            RemoveServicioFromInfo: function(idServicio)
            {
                let servicios = 0;

                //  Eliminamos la etiqueta del servicio
                $(`body .info-servicios .servicio-${idServicio}`).remove();

                //  Comprobamos si tiene servicios para si no mostrar el mensaje correspondiente
                $('.chkServicio').each(function(ev){
                    if($(this).is(':checked')){
                        servicios++;
                    }
                });

                //  Si no tiene ningún servicio seleccionado mostrarmos el mensaje de información
                if(servicios <= 0){
                    $('.info-servicios').html('No ha seleccionado ningún servicio');
                    $('.total-servicios').addClass('d-none');
                    for(let i = 1; i<=5; i++){
                        $(`.total-servicios .total-${i}`).removeClass('d-none').addClass('d-none');
                    }
                }
                
            },

            /**
             * Oculta todos los servicios disponibles ara facturación
             */
            RemoveAllSelectedServices: function()
            {
                $('.servicio-doccae').hide();
                $('.servicio-cae').hide();
                $('.servicio-doccae').hide();
                $('.servicio-dpd').hide();
                $('.servicio-certificadosdigitales').hide();
                facturacion.Facturacion.View.RemoveServicioFromInfo(1);
                facturacion.Facturacion.View.RemoveServicioFromInfo(2);
                facturacion.Facturacion.View.RemoveServicioFromInfo(3);
                facturacion.Facturacion.View.RemoveServicioFromInfo(5);                
            },

            /**
             * Muestra/Oculta los servicios disponibles para facturar y en consecuencia el botón de generar facturación
             */
            ShowAvailableServices: function()
            {

                $('.btnGenerarFactura').removeClass('disabled');

                if(facturacion.Facturacion.Model.ContratadoCae == false && facturacion.Facturacion.Model.ContratadoDpd == false && facturacion.Facturacion.Model.ContratadoCertificadosDigitales == false){
                    //  Ocultamos
                    $('.emision--servicios-info').hide();
                    $('.emision--servicios-noinfo .mensaje').text('Este administrador no tiene servicios contratados');
                    $('.emision--servicios-noinfo').show();
                    //  Deshabilitamos el botón de generar facturación
                    $('.btnGenerarFactura').addClass('disabled');
                }else{
                    $('.emision--servicios-info').show();
                    $('.emision--servicios-noinfo').hide();
                }                
            }


        }
    },

    /**
     * Módulo de facturaciones
     */
    Liquidaciones: {

        Init: function(){

            facturacion.Liquidaciones.Events();

        },

        Events: function(){

            //  Rellenamos automáticamente la fecha Hasta
            $('#dateto').val(moment().format('YYYY-MM-DD'));
            facturacion.Liquidaciones.Model.dateTo = moment($('#dateto').val()).format('YYYY-MM-DD');

            //  Cambio de fecha seleccionada
            $('#dateto').on('change',function(ev){
                let res = facturacion.Liquidaciones.Validator.SelectedDates();
                //  Validamos la fecha seleccionada
                if(res !== true){
                    CoreUI.Modal.Error(`<p>Se han encontrado los siguientes errores:</p> ${res}`);
                    facturacion.Liquidaciones.Model.dateTo = '';
                }else{
                    facturacion.Liquidaciones.Model.dateTo = $('#dateto').val();
                    facturacion.Liquidaciones.Controller.LoadInfo();
                }
                facturacion.Liquidaciones.View.RenderInformation();
            });

            //  Cambio de fecha seleccionada
            $('#datefrom').on('change',function(ev){
                let res = facturacion.Liquidaciones.Validator.SelectedDates()
                //  Validamos la fecha seleccionada
                if(res !== true){
                    CoreUI.Modal.Error(`<p>Se han encontrado los siguientes errores:</p> ${res}`);
                    facturacion.Liquidaciones.Model.dateFrom = '';
                }else{
                    facturacion.Liquidaciones.Model.dateFrom = $('#datefrom').val();
                    facturacion.Liquidaciones.Controller.LoadInfo();
                }
                facturacion.Liquidaciones.View.RenderInformation();
            });

            //  Administrador seleccionado
            $('#usuarioId').on('change', function(ev){
                facturacion.Liquidaciones.Model.administrador = $('#usuarioId option:selected').text();
                facturacion.Liquidaciones.Model.idadministrador = $('#usuarioId option:selected').val();
                //  Renderiza la información en base a los parámetros
                facturacion.Liquidaciones.View.RenderInformation();
                //  Cargamos la información según los parámetros seleccionados
                facturacion.Liquidaciones.Controller.LoadInfo();
            });

            //  Generación de liquidación
            $('body').on(core.helper.clickEventType, '.btnGenerarLiquidacion', function(ev){
                facturacion.Liquidaciones.Model.administrador = $('#usuarioId option:selected').text();
                facturacion.Liquidaciones.Model.idadministrador = $('#usuarioId option:selected').val();
                //  Cargamos la información según los parámetros seleccionados
                facturacion.Liquidaciones.Controller.Generate();
            });

        },

        Controller: {

            Generate: function()
            {
                let pInfo = new Promise(async(resolve, reject) => {

                    let data = Object();

                    data.idadministrador = facturacion.Liquidaciones.Model.idadministrador;
                    data.datefrom = facturacion.Liquidaciones.Model.dateFrom;
                    data.dateto = facturacion.Liquidaciones.Model.dateTo;

                    await apiFincatech.post(`liquidaciones/generate`, data).then( (result) =>{

                        //  Recuperamos los datos y asignamos los valores al modelo
                        let res = JSON.parse(result);
                        
                        if(res.data == 'error')
                        {
                            CoreUI.Modal.Error(res.status.error,'Error');
                        }else{
                            CoreUI.Modal.Success(`La liquidación se ha generado correctamente<br><br>${res.data}`, 'Liquidación', function()
                            {
                                facturacion.Liquidaciones.Controller.LoadInfo();
                            });
                        }
                        resolve(true);
                    });
                });

                pInfo.then((result) =>{
                });
            },

            /**
             * Carga la información desde el Webservice
             */
            LoadInfo: function(){

                let pInfo = new Promise(async(resolve, reject) => {

                    let data = Object();

                    data.idadministrador = facturacion.Liquidaciones.Model.idadministrador;
                    data.datefrom = facturacion.Liquidaciones.Model.dateFrom;
                    data.dateto = facturacion.Liquidaciones.Model.dateTo;

                    await apiFincatech.post(`liquidaciones/info`, data).then( (result) =>{

                        //  Recuperamos los datos y asignamos los valores al modelo
                        let res = JSON.parse(result);
                        res = res.data;

                        facturacion.Liquidaciones.Model.numeroComunidades = res.total_comunidades;
                        facturacion.Liquidaciones.Model.totalTaxesExc = res.total_taxes_exc;
                        facturacion.Liquidaciones.Model.totalTaxesInc = res.total_taxes_inc;
                        facturacion.Liquidaciones.Model.totalIngresosCuenta = res.total_ingreso_cuenta;
                        //  Calculamos la diferencia entre el importe total pendiente de facturar - el total de ingresos a cuenta
                        // let totalPendiente = res.total_taxes_inc - res.total_ingreso_cuenta;
                        // facturacion.Liquidaciones.Model.totalPendienteLiquidacion = facturacion.Facturacion.Controller.Utils.FormatearNumero(totalPendiente);
                        facturacion.Liquidaciones.Model.totalPendienteLiquidacion = res.total_liquidacion;

                        resolve(true);
                    });
                });

                pInfo.then((result) =>{
                    //  Terminada la carga, renderizamos los valores
                    facturacion.Liquidaciones.View.RenderInformation();
                });
            }

        },

        Model: {
            //  Número de comunidades afectadas a la liquidación
            numeroComunidades: 0,
            //  Id del administrador
            idadministrador: -1,
            //  Nombre del administrador
            administrador: 'Ninguno',
            //  Estado de liquidaciones
            estado: '-',
            //  Importe total para liquidación
            totalPendienteLiquidacion: 0,
            //  Importe total ingresos a cuenta pendientes de liquidar
            totalIngresosCuenta: 0,
            //  Total impuestos incluidos
            totalTaxesInc: 0,
            //  Total impuestos excluidos
            totalTaxesExc: 0,
            //  Ingresos a cuenta pendientes de liquidar
            pendienteLiquidacion: 0,
            //  Fecha desde
            dateFrom: '',
            //  Fecha hasta
            dateTo: ''

        },

        Validator: {

            /**
             * Valida las fechas seleccionadas para el periodo de liquidación
             * @returns string|boolean Mensaje de error o true si es correcto
             */
            SelectedDates: function()
            {
                let msg = '';
                let today = moment();
                
                //  Si está informada la fecha hasta pero no la fecha desde
                // if($('#dateto').val() != '' && $('#datefrom').val() == ''){
                //     msg = `${msg}<p class="mb-0">- Fecha Desde está vacía o es incorrecta</p>`;
                // }

                //  Validamos que la fecha hasta esté informada
                if($('#dateto').val() == ''){
                    msg = `${msg}<p class="mb-0">- <strong>Fecha Hasta</strong> está vacía o es incorrecta</p>`;
                }

                //  Validamos que la fecha hasta sea válida
                if($('#dateto').val() !== ''){
                    let dateTo = moment($('#dateto').val(), 'YYYY-MM-DD');
                    if (dateTo.isAfter(today)) {
                        msg = `${msg}<p class="mb-0">La fecha hasta debe ser igual o inferior a la fecha de hoy</p>`;
                        $('#dateto').val(moment().format('YYYY-MM-DD'));
                        facturacion.Liquidaciones.Model.dateTo = moment($('#dateto').val()).format('YYYY-MM-DD');
                    }
                }

                if($('#datefrom').val() !== '' && $('#dateto').val() !== ''){

                    let dateFrom = moment($('#datefrom').val(), 'YYYY-MM-DD');
                    let dateTo = moment($('#dateto').val(), 'YYYY-MM-DD');

                    if (dateFrom.isAfter(dateTo)) {
                        msg = `${msg}<p class="mb-0">- <strong>Fecha Desde</strong> debe ser inferior a la <strong>Fecha Hasta</strong></p>`;
                        $('#datefrom').val('');
                    }
                

                }

                return (msg == '' ? true : msg);
            }

        },

        View: {

            RenderInformation: function(){
                //  Administrador
                $('.administrador-seleccionado').html(facturacion.Liquidaciones.Model.administrador);
                //  Nº de comunidades
                $('.numero-comunidades').html(facturacion.Liquidaciones.Model.numeroComunidades);
                //  Periodo seleccionado
                if(facturacion.Liquidaciones.Validator.SelectedDates() === true){
                    let fechaDesde = moment($('#datefrom').val()).format('DD/MM/YYYY');
                    let fechaHasta = moment($('#dateto').val()).format('DD/MM/YYYY');
                    $('.periodo-liquidacion').html(`Desde ${fechaDesde} hasta ${fechaHasta} inclusive`);
                }

                if(facturacion.Liquidaciones.Model.dateFrom == '' && facturacion.Liquidaciones.Model.dateTo !== ''){
                    $('.periodo-liquidacion').html('Todo hasta el día ' + moment($('#dateto').val()).format('DD/MM/YYYY'));
                }

                if(facturacion.Liquidaciones.Model.dateFrom == '' && facturacion.Liquidaciones.Model.dateTo == ''){
                    $('.periodo-liquidacion').html('No ha seleccionado fechas');
                }
                
                //  Importe pendiente de liquidación
                $('.importe-pendiente-liquidacion').html(facturacion.Liquidaciones.Model.totalPendienteLiquidacion);
                //  Importe total de ingresos a cuenta
                $('.importe-ingresos-cuenta').html(facturacion.Liquidaciones.Model.totalIngresosCuenta);
                //  Importe total facturado y pendiente de liquidar
                $('.total_taxes_inc').html( facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacion.Liquidaciones.Model.totalTaxesInc ));
                $('.total_taxes_exc').html( facturacion.Facturacion.Controller.Utils.FormatearNumero(facturacion.Liquidaciones.Model.totalTaxesExc ));


            },

            RenderTable: {

            },

        }

    },

    /**
     * Prefacturación
     */
    Prefacturacion: {

        generarInformePrefacturacion: function(){

            var fechaDesde = $('.fechaDesde').val();
            var fechaHasta = $('.fechaHasta').val();
            var administradorId = $('#usuarioId option:selected').val();
            var nombreAdministrador = $('#usuarioId option:selected').text();

            if(fechaHasta != '' && fechaDesde == '')
            {
                CoreUI.Modal.Error('Proporcione la fecha desde para generar el informe');
                return;
            }

            var datos = Object();
            datos.fechaDesde = fechaDesde;
            datos.fechaHasta = fechaHasta;
            datos.nombreAdministrador = nombreAdministrador;

            //  Mandamos al endpoint la información para generar el informe
            apiFincatech.post(`facturacion/prefacturacion/${administradorId}`, datos).then( (result) =>{
                //  Recuperamos el fichero desde donde se ha generado y lanzamos la descarga
                var data = JSON.parse(result);
                if(data.status.response == 'error')
                {
                    CoreUI.Modal.Error(data.status.error);
                }else{
                    window.open(data.data);
                }
                
            });

        }

    },

    Remesa:{

        Events: function(){
            //  Mostrar modal de devolución de recibos
            $('body').on(core.helper.clickEventType, '.btnProcesarRemesaDevolucion', function(ev)
            {
                // Construimos el modal y lo lanzamos para mostrar la información
                apiFincatech.getView("facturacion", "modal_devolucion_remesa").then((resultHTML)=>{                   
                    CoreUI.Modal.CustomHTML(resultHTML, null, function(){
                        core.Files.init();
                    });
                });      
            })
      
            //  Procesar devolución de remesa de recibos
            $('body').on(core.helper.clickEventType, '.btnUploadXML', function(ev){
                facturacion.Remesa.ProcesarDevolucion();    
            });

        },

        ProcesarDevolucion: function()
        {
            //  Comprobamos que tenga un fichero seleccionado
            let fileInput = document.getElementById('ficheroadjuntar');

            $('.wrapperMensajeErrorCarga').hide();

            if(fileInput.files.length === 0){
                $('.wrapperMensajeErrorCarga .mensaje').html('Debe adjuntar el fichero que desea procesar');
                $('.wrapperMensajeErrorCarga').show();
                return;
            }

            //  Si tiene fichero, construimos el objeto para enviar
            let file = fileInput.files[0];
            let formData = new FormData();
            formData.append('file', file);
            let p = new Promise(async(resolve, reject) =>{
                apiFincatech.post('remesa/devolucion',formData).then((result)=>{
                    // console.log(result);
                    let res = JSON.parse(result);
                    if(res.status.response == 'error')
                    {
                        CoreUI.Modal.Error('Ha ocurrido el siguiente error:<br><br>' + res.status.error);
                    }else{
                        CoreUI.Modal.Success(res.data);
                    }
                    resolve(result);
                })
            });

            p.then((result)=>{

            });
        }

    }

}

$(() =>{
    if(core.model == 'Prefacturacion')
    {
        // facturacion.Init();
    }

    document.addEventListener('coreInitialized', function(event) {

        facturacion.Init();  
        facturacion.Facturacion.Events();   
        if(core.model == 'Prefacturacion' || core.model == 'emision'){
            CoreUI.Controller.InitializeSelectData();
        }
    });

})