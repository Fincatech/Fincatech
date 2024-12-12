let usuarioCore = {

    usuarios: Object(),
    usuario: Object(),

    init: async function()
    {

        //  Bindeamos los eventos de los diferentes botones de administradores
        usuarioCore.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "usuario")
        {
            //  Recuperamos el listado de administradores
            await usuarioCore.listadoDashboard();
        }

        //  Título del módulo
            if($('.titulo-modulo').length && (core.model == 'Usuario' || core.model == 'Autorizado'))
                CoreUI.setTitulo('nombre');    

        //  Bindeamos los eventos de autorizado
        usuarioCore.Autorizado.init();

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerUsuario', (evt)=>{
            evt.stopImmediatePropagation();
            usuarioCore.verModalAdministrador( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarUsuario', (evt)=>{
            evt.stopImmediatePropagation();
            usuarioCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },



    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("usuario", id, nombre, "listadoUsuario");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalAdministrador: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        usuarioCore.getAdministrador(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "administradores/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, usuarioCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, usuarioCore.comunidad.nombre);
                // Swal.fire({
                //     title:`${usuarioCore.comunidad.nombre}`,
                //     html: result,
                //     customClass: 'modal-lg'
                // })
            });

        });
    },

    /**
     * Carga los datos del listado de administradores en la tabla listadoUsuarioes
     */
    renderTabla: async function()
    {
        if($('#listadoUsuario').length)
        {
            //  Cargamos el listado de administradores
            CoreUI.tableData.init();

            //  Nombre
            CoreUI.tableData.addColumn('listadoUsuario', "nombre", "NOMBRE");

            //  Rol
                var html = 'data:rol.nombre$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "ROL", html);

            CoreUI.tableData.addColumn('listadoUsuario', "cif", "CIF");

            //  Email
                var html = '<a href="mailto:data:email$" class="pl-1 pr-1">data:email$</a>';
                CoreUI.tableData.addColumn('listadoUsuario', null, "EMAIL", html);

            //  Teléfono
                CoreUI.tableData.addColumn('listadoUsuario', "telefono", "TELEFONO");

            //  Fecha de último acceso
                CoreUI.tableData.addColumn('listadoUsuario', 
                function(row, type, val, meta)
                {
                    var timeStamp;
                    var fechaCreacion;

                    if(!row.lastlogin || row.lastlogin == '0000-00-00 00:00:00')
                    {
                        timeStamp = '';
                        fechaCreacion = '<span class="badge badge-pill bg-danger text-white">Nunca</span>';
                    }else{
                        timeStamp = moment(row.lastlogin, 'YYYY-MM-DD hh:mm').unix();
                        // fechaCreacion = moment(row.lastlogin).locale('es').format('L');
                        fechaCreacion = moment(row.lastlogin).format('DD/MM/YYYY');
                    }

                    return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
                },
                "Último acceso", null, 'text-center');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "Estado", html, 'text-center');

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoUsuario', null, "Fecha de alta", html, 'text-center');

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center">';
                    html += `<li class="nav-item"><a href="${baseURL}usuario/data:id$" class="btnEditarUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="height:26px; width:26px;"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="height:26px; width:26px;"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoUsuario', null, "", html);

            $('#listadoUsuario').addClass('no-clicable');
            CoreUI.tableData.render("listadoUsuario", "Usuario", "usuario/list");
        }

    },

    /** Recupera el listado de administradores en el dashboard */
    listadoDashboard: async function()
    {
        await usuarioCore.getAll().then(async (data)=>{
               this.renderTabla();
        });
  
    },

    getAdministrador: async function(comunidadId)
    {
        await apiFincatech.get('usuario/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            usuarioCore.comunidad = responseData.Comunidad[0];
            //console.log(usuarioCore.comunidad);
            return usuarioCore.comunidad;
        });
    },

    /** Recupera los datos de las administradores desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('usuario/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            usuarioCore.usuarios = responseData.Usuario;
            // usuarioCore.administradores.total = usuarioCore.administradores.length;
        });

    },

    /** Gestión de usuarios autorizados */
    Autorizado: {

        /**
         * Inicializa el componente de Autorizado
         */
        init: function()
        {
            usuarioCore.Autorizado.events();

            //  Comprobamos si se está cargando el listado
            if(core.actionModel == "list" && core.model.toLowerCase() == "autorizado")
            {
                //  Recuperamos el listado de administradores
                    usuarioCore.Autorizado.listadoAutorizados();
            }   
            
        //  Comprobamos si se está cargando el listado
            if( (core.actionModel == "add" || core.actionModel === 'get') && core.model.toLowerCase() == "autorizado")
            {
                //  Recuperamos el listado de comunidades del administrador principal
                    usuarioCore.Autorizado.comunidadesAsignadas();
            }            
        },

        events: function()
        {

                //  Cambio nombre de usuario
                $('body .form-autorizado #nombre').on('keyup', function(e)
                {
                    CoreUI.Utils.setTituloPantalla(null, null, `${$(this).val()}`);
                });

                // @Override del botón de guardar
                $("body .form-autorizado .btnSaveData").off(core.helper.clickEventType).on(core.helper.clickEventType, function(evt)
                {
                    evt.stopImmediatePropagation();
                    evt.preventDefault();
                    usuarioCore.Autorizado.Model.Save();
                });

                $('body').on(core.helper.clickEventType, '.btnEliminarUsuarioAutorizado', function(evt)
                {
                    //  Eliminamos el usuario autorizado
                    evt.stopImmediatePropagation();
                    usuarioCore.Autorizado.Model.delete( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );                    
                });

                switch(core.actionModel)
                {
                    case 'add':
                    case 'get':
                        $('body').on('change', '.form-autorizado .chkComunidadAutorizada', function(ev)
                        {
                            window['tablelistadoComunidadesAutorizado'].row($(this).attr('data-row')).data().asignada = ($(this).is(':checked'));
                        });

                        break;
                    
                }

                if(core.actionModel === 'add')
                {
                    $('.form-autorizado #password').addClass('form-required');
                }

        },

        /**
         * Renderiza el listado de usuarios autorizados para un administrador
         */
        listadoAutorizados: function()
        {

            if( $('#listadoUsuariosAutorizados').length)
            {
                var listado = 'listadoUsuariosAutorizados';

                //  Cargamos el listado de administradores
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
                //  Nombre
                    CoreUI.tableData.addColumn( listado, "nombre", "NOMBRE");

                    CoreUI.tableData.addColumn(listado, "cif", "CIF");

                //  Email
                    var html = '<a href="mailto:data:email$" class="pl-1 pr-1">data:email$</a>';
                    CoreUI.tableData.addColumn(listado, null, "EMAIL", html);

                //  Teléfono
                    CoreUI.tableData.addColumn(listado, "telefono", "TELEFONO");

                //  Fecha de último acceso
                    CoreUI.tableData.addColumn(listado, 
                    function(row, type, val, meta)
                    {
                        var timeStamp;
                        var fechaCreacion;

                        if(!row.lastlogin || row.lastlogin == '0000-00-00 00:00:00')
                        {
                            timeStamp = '';
                            fechaCreacion = '<span class="badge badge-pill bg-danger text-white">Nunca</span>';
                        }else{
                            timeStamp = moment(row.lastlogin, 'YYYY-MM-DD hh:mm').unix();
                            fechaCreacion = moment(row.lastlogin).locale('es').format('L');
                        }

                        return `<span style="display:none;">${timeStamp}</span>${fechaCreacion}`;
                    },
                    "Último acceso", null, 'text-center');

                //  Fecha de alta
                    var html = 'data:created$';
                    CoreUI.tableData.addColumn(listado, null, "Fecha de alta", html, 'text-center');

                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center">';
                        html += `<li class="nav-item"><a href="${baseURL}autorizado/data:id$" class="btnEditarUsuario d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="height:26px; width:26px;"></i></a></li>`;
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarUsuarioAutorizado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="height:26px; width:26px;"></i></li></ul>';
                    CoreUI.tableData.addColumn(listado, null, "", html);

                $('#listadoUsuario').addClass('no-clicable');
                CoreUI.tableData.render(listado, "Usuario", "autorizado/list");  

            }

        },

        comunidadesAsignadas: function()
        {
            if( $('#listadoComunidadesAutorizado').length)
            {
                var listado = 'listadoComunidadesAutorizado';

                //  Cargamos el listado de administradores
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                //  Asignada
                    CoreUI.tableData.addColumn(listado, 
                    function(row, type, val, meta)
                    {
                        var asignada = '';
                        var rowId = meta.row;
                        if(row.asignada === true)
                        {
                            asignada = 'checked';
                        }

                        var el = `
                            <input id="asignada_${row.id}" name="asignada_${row.id}" type="checkbox" class="form-check-label chkComunidadAutorizada" ${asignada} data-row=${rowId}>
                            <label class="form-check-label ml-2" for="asignada_${row.id}">
                        `;

                        return el;
                    },
                    "", null, 'text-center');

                //  Código
                    CoreUI.tableData.addColumn( listado, "codigo", "CÓDIGO");

                //  Nombre
                    CoreUI.tableData.addColumn( listado, "nombre", "NOMBRE");

                //  Fecha de alta
                    var html = 'data:created$';
                    CoreUI.tableData.addColumn(listado, null, "Fecha de alta", html, 'text-center');

                // //  Columna de acciones
                //     var html = '<ul class="nav justify-content-center">';
                //         html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$" class="btnEditarComunidad d-inline-block mr-2" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid" style="height:26px; width:26px;"></i></a></li>`;
                //         html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid" style="height:26px; width:26px;"></i></li></ul>';
                //     CoreUI.tableData.addColumn(listado, null, "", html);

                $(`#${listado}`).addClass('no-clicable');

                var ep = core.actionModel === 'get' ? `autorizado/${core.modelId}/comunidad/list` : 'autorizado/comunidad/list';
                CoreUI.tableData.render(listado, "Comunidad", ep, null, false);  

            }  
        },

        Model:{

            /**
             * Recupera la asignación de comunidades para un usuario autorizado desde el listado de comunidades
             * @returns 
             */
            GetComunidadesAsignadas: function()
            {
                let oComunidades = Array();

                //  Recorremos las filas del listado
                $seleccionComunidades = window['tablelistadoComunidadesAutorizado'].rows().data();
                for(let iSelCom = 0; iSelCom < $seleccionComunidades.length; iSelCom++)
                {
                    let oTMP = Object();
                    oTMP.idcomunidad = window['tablelistadoComunidadesAutorizado'].row(iSelCom).data().id;
                    oTMP.asignada = window['tablelistadoComunidadesAutorizado'].row(iSelCom).data().asignada;
                    oComunidades.push(oTMP);
                }
                return oComunidades;
            },

            Validate: function(){

                //  Validamos los campos obligatorios
                let resultadoValidacion = false;
                core.Forms.Validate();

                if( $('.form-autorizado #email').val() !== '')
                {
                    if(!core.Validator.Email($('.form-autorizado #email').val()))
                    {
                        core.Forms.SetError('El formato del e-mail no es correcto');
                    }
                }

                if($('.form-autorizado #password').val() !== '')
                {

                    if( $('.form-autorizado #password').val() !== $('.form-autorizado #passwordConfirme').val() )
                    {
                        core.Forms.SetError('Las contraseñas no coinciden');
                    }

                }


                if(core.Forms.GetErrorMessage() === '')
                    resultadoValidacion = true;

                return resultadoValidacion;
            },

            /** Save model data */
            Save: function()
            {
                //  Validamos que la información sea correcta
                if(usuarioCore.Autorizado.Model.Validate()){
                    //  Guardamos según la acción
                    core.Forms.prepareFormDataBeforeSend('form-autorizado');
                    //  Recuperamos la asignación de las comunidades
                    core.Forms.data['comunidadesasignadas'] = usuarioCore.Autorizado.Model.GetComunidadesAsignadas();
                    core.Forms.Save(true);
                }else{
                    core.Forms.ShowErrorMessage();
                }
            },

            add: function()
            {

            },

            update: function()
            {

            },

            delete: function(id, nombre)
            {
                core.Modelo.Delete("autorizado", id, nombre, "listadoUsuariosAutorizados");
            },

        }

    }

}

document.addEventListener('coreInitialized', function(event) {
    console.log('coreInitialized Autorizado');
    // comunidadesCore.renderTablaComunidadesAdministrador(core.modelId);
    // usuarioCore.init(); 
      
});
   
document.addEventListener('modelLoaded', function(event) {
    console.log('init modelLoaded usuario');
    if(core.actionModel == 'get' && core.model.toLowerCase() == "autorizado"){
        usuarioCore.init(); 
        // let titulo = `${core.Modelo.entity['Autorizado'][0]['codigo']} - ${core.Modelo.entity['Comunidad'][0]['nombre']}`;
        // CoreUI.Utils.setTituloPantalla(null, null, titulo);
    }    
});

$(()=>{
    usuarioCore.init(); 
});