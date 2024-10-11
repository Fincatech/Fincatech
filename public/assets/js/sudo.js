
let sudo = {

    init: function(){

        sudo.Mensajes.Init();

        if($('body #listadoMensajes').length)
        {
            sudo.Mensajes.CRUD.GetAll();
        }

        if($('body .stats-sistema').length)
        {
            sudo.Stats();
            //  Cargamos el listado de comunidades pendientes
            comunidadesCore.Render.tablaListadoComunidadesPendientes();
        }

        sudo.Events();

    },

    Events: function()
    {
        $('body').on(core.helper.clickEventType, '.btnActualizacionServicios', function(evt){
            sudo.Controller.MostrarExportacionComunidades();
        });

        /**
         * Proceso de actualización de servicios
         */
        $('body').on(core.helper.clickEventType, '.bntActualizarProcesos', function(evt){
            sudo.Controller.ValidarFicheroActualizacionServicios();
        });

        //  IBAN para facturación
        if($('#ibanliquidaciones').length)
            {
                $('#ibanliquidaciones').mask('SS00 0000 0000 00 0000000000');
                //  Validación de cuando pierde el foco
                $('#ibanliquidaciones').on('blur', function(evt)
                {
                    if($('#ibanliquidaciones').val().length > 0){
                        if(!core.Validator.checkIBAN($('#ibanliquidaciones').val())){
                            evt.stopImmediatePropagation();
                            CoreUI.Modal.Error('El código IBAN no es correcto. Por favor, revíselo.');
                            $('#ibanliquidaciones').val('');
                        }    
                    }
                });
            }        

    },

    Controller: {

        ValidarColumnasFicheroServicios: function()
        {
            return false;
        },

        ValidarFicheroActualizacionServicios: function()
        {

            //  Comprobamos que el fichero sea un fichero de Excel
            if(!core.Files.isExcelFile('ficheroAdjuntarExcel'))
            {
                $('.wrapperMensajeErrorCarga .mensaje').html('El fichero no es válido. Seleccione un fichero de formato Excel (xls o xlsx)');
                $('.wrapperMensajeErrorCarga').show();
                return;
            }else{
                $('.wrapperMensajeErrorCarga').hide();
            }

        //  Validamos que haya seleccionado un administrador
            if( $('#administradorCargaId option:selected').val() == '-1' )
            {
                $('.wrapperMensajeErrorCarga .mensaje').html('Debe seleccionar el administrador al que asignar las comunidades a importar');
                $('.wrapperMensajeErrorCarga').show();
                return;
            }

            var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/  
            if ($("#ficheroAdjuntarExcel").val().toLowerCase().indexOf(".xlsx") > 0) {  
                xlsxflag = true;  
            } 

        //  Si el fichero es correcto, ocultamos los wrappers de información y mostramos el de procesando
            $('.wrapperInformacion').hide();
            $('.wrapperSelectorAdministrador').hide();
            $('.wrapperSelectorFichero').hide();
            $('.wrapperMensajeErrorCarga').hide();

        //  Intentamos hacer la importación
            serviciosCore.Controller.ProcesarUpdateFicheroServicios(xlsxflag);
        },

        MostrarExportacionComunidades: function()
        {
            
            apiFincatech.getView("sudo", "servicios").then((resultHTML)=>{

                CoreUI.Modal.CustomHTML(resultHTML,'Actualización masiva de servicios de comunidades',function(){
                });

            });

        }

    },

    Mensajes: {
    
        Init: function(){
            sudo.Mensajes.Events();
        },

        Events: function(){
            $('body').on(core.helper.clickEventType, '.btnReenviarMensaje', function(e){
                var idMensaje = $(this).attr('data-id');
                apiFincatech.get(`mensaje/${idMensaje}/resend`).then( (result) =>{
                    CoreUI.Modal.Success('El mensaje se ha reenviado correctamente','Reenviar mensaje');
                })
            });

            $('body').on(core.helper.clickEventType, '.btnVerMensajeEnviado', function(evt){
                var idMensaje = $(this).attr('data-id');
                apiFincatech.get(`mensaje/${idMensaje}`).then( (result) =>{
                    let resultado = JSON.parse(result);
                    let body = resultado.data.Mensaje[0]['body'];
                    body = sudo.Mensajes.Helper.htmlEntities(body);
                    body = body.replaceAll('https:/app.fincatech.es/','https://app.fincatech.es/');
                    console.log(body);
                    CoreUI.Modal.CustomHTML(body,'Mensaje Enviado', null, '90%');
                })
            });  
           
        },

        CRUD: {
            /**
             * Listado de todos los mensajes enviados
             */
            GetAll: function(){
                if($('#listadoMensajes').length)
                {
                    if(typeof window['tablelistadoMensajes'] != 'undefined')
                    {
                        CoreUI.tableData.columns['listadoMensajes'] = [];
                    }
                    CoreUI.tableData.init();
        
        
                    //  Razón social
                    CoreUI.tableData.addColumn('listadoMensajes', "subject","Asunto", null, 'text-left');
            
                    //  Email
                    CoreUI.tableData.addColumn('listadoMensajes', "email", "EMAIL", null, 'text-left');
                
                    CoreUI.tableData.addColumn('listadoMensajes', 
                    function(row, type, val, meta)
                    {
                        var timeStamp;
                        var fechaCreacion;
                            timeStamp = moment(row.created, 'YYYY-MM-DD hh:mm').unix();
                            fechaCreacion = moment(row.created).locale('es').format('L');
        
                        return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;

                    },
                    "Fecha envío", null, 'text-center');

                    //  Botón de reenviar mensaje
                                //  Columna de acciones
                        CoreUI.tableData.addColumn('listadoMensajes', function(row, type, val, meta)
                        {
                            var html = `<ul class="nav justify-content-center accionesTabla">
                                            <li class="nav-item mr-2">
                                                <a href="javascript:void(0);" class="btnVerMensajeEnviado d-inline-block btn btn-success" data-id="${row.id}"><i data-feather="eye" class="text-white img-fluid"></i> Ver mensaje</a>
                                            </li>                            
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="btnReenviarMensaje d-inline-block btn btn-primary" data-id="${row.id}"><i data-feather="send" class="text-white img-fluid"></i> Reenviar mensaje</a>
                                            </li>
                                        </ul>`;
                            return html;
                        }, '&nbsp;', null, 'text-center');

                        $('#listadoMensajes').addClass('no-clicable');
                        CoreUI.tableData.render("listadoMensajes", "Mensaje", "mensaje/list", null, true,true,null,null,false,null,true);
                    
                }  
            }
        },

        Helper:{
            htmlEntities: function(rawStr) {
                var textArea = document.createElement('textarea');
                textArea.innerHTML = rawStr;
                return textArea.value;
            }
        }

    },
    
    Stats: function(){
        apiFincatech.get(`stats/list`).then( result =>
        {
                let data = JSON.parse(result);
                $('.stat-empresas').text(data.data['empresas']);
                $('.stat-comunidades').text(data.data['comunidades']);
                $('.stat-administradores').text(data.data['administradores']);
                $('.stat-emails').text(data.data['emailscertificados']);
                $('.stat-emails-enviados').text(data.data['emailsenviados']);
                //  Totales servicios contratados
                $('.stat-totalcae').text(data.data['servicioscomunidades']['totalcae']);
                $('.stat-totaldpd').text(data.data['servicioscomunidades']['totaldpd']);
                $('.stat-totaldoccae').text(data.data['servicioscomunidades']['totaldoccae']);
                $('.stat-totalcertificados').text(data.data['servicioscomunidades']['totalcertificadosdigitales']);
        });
    }

};


$(()=>{
    sudo.init();
});