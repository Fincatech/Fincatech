
let sudo = {

    init: function(){
        if($('body #listadoMensajes').length)
        {
            sudo.Mensajes.CRUD.GetAll();
            sudo.Mensajes.Init();
        }

        if($('body .stats-sistema').length)
        {
            sudo.Stats();
            //  Cargamos el listado de comunidades pendientes
            comunidadesCore.Render.tablaListadoComunidadesPendientes();
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
        });
    }

};


$(()=>{
    sudo.init();
});