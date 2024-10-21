let administradorCore = {

    administradores: Object(),
    administrador: Object(),

    init: async function()
    {
        
        //  Bindeamos los eventos de los diferentes botones de administradores
        administradorCore.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "administrador")
        {
            //  Recuperamos el listado de administradores
            await administradorCore.listadoDashboard();
        }else if( core.model.toLowerCase() == "administrador" && core.actionModel != "list"){
            core.model = "Usuario";
            comunidadesCore.renderTablaComunidadesAdministrador(core.modelId);
            //  Título del módulo
                if($('.titulo-modulo').length && core.model == 'Administrador')
                    CoreUI.setTitulo('nombre');
            //  Renderizamos la tabla de representantes legales asociados al administrador
            administradorCore.RepresentanteLegal.Controller.RenderTablaRepresentantesLegales();
        }

        //  Inicializamos los eventos del representante legal
        administradorCore.RepresentanteLegal.Events();

    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerAdministrador', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.verModalAdministrador( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarAdministrador', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        //  Formulario Representante legal
        $('body .form-representante-legal').off(core.helper.clickEventType).on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.RepresentanteLegal.Controller.Guardar();
        });    

        //  Eliminar representante legal
        $('body').on(core.helper.clickEventType, '.btnEliminarRepresentanteLegal', (evt)=>{
            evt.stopImmediatePropagation();
            administradorCore.RepresentanteLegal.Controller.Eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });        

        //  Selector de imagen documento frontal dni
        $('body').on('change','#fileFrontDocument',function(){
            administradorCore.Helper.readURL(this, 'imgfileFrontDocument');
        });  

        //  Selector de imagen documento trasero dni
        $('body').on('change','#fileRearDocument',function(){
            administradorCore.Helper.readURL(this, 'imgfileRearDocument');
        }); 

        //  Exportación de comunidades con servicios contratados por administrador
        $('body').on(core.helper.clickEventType, '.btnExportarComunidades', function(evt){
            administradorCore.ExportComunidadesToExcel($(this).attr('data-id'), $(this).attr('data-nombre'));
        });

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("administrador", id, nombre, "listadoAdministrador");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalAdministrador: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        administradorCore.getAdministrador(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "administradores/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, administradorCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, administradorCore.comunidad.nombre);
            });

        });
    },

    /**
     * Carga los datos del listado de administradores en la tabla listadoAdministradores
     */
    renderTabla: async function(reducida = false, nombreTabla = 'listadoAdministrador')
    {

        if( $(`#${nombreTabla}`).length)
        {
            //  Cargamos el listado de administradores
                CoreUI.tableData.init();

            //  ID
                CoreUI.tableData.addColumn(nombreTabla, "codigo", "CÓD");

            //  Nombre
                CoreUI.tableData.addColumn(nombreTabla, "nombre", "NOMBRE");

                CoreUI.tableData.addColumn(nombreTabla, "cif", "CIF");

            //  Email
                var html = '<a href="mailto:data:email$" class="pl-1 pr-1">data:email$</a>';
                CoreUI.tableData.addColumn(nombreTabla, null, "EMAIL", html);

            //  Teléfono
                CoreUI.tableData.addColumn(nombreTabla, "telefono", "TELEFONO");

            //  Comunidades
                CoreUI.tableData.addColumn(nombreTabla, "numerocomunidades", "Comunidades activas",null, 'text-center');

            //  Visitado
                CoreUI.tableData.addColumn(nombreTabla, 
                function(row, type, val, meta)
                {
                    var visitado;
                    if(row.visitado == true)
                    {
                        visitado = '<span class="badge badge-pill bg-success text-white">Visitado</span>';
                    }else{
                        visitado = '<span class="badge badge-pill bg-danger text-white">No visitado</span>';
                    }

                    return visitado;
                },
                "Visitado", null, 'text-center');


                //  Fecha de creación
                    CoreUI.tableData.addColumn(nombreTabla, 
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
                    CoreUI.tableData.addColumn(nombreTabla, null, "Estado", html, 'text-center');
                                    
                //  Fecha de alta
                    var html = 'data:created$';
                    CoreUI.tableData.addColumn(nombreTabla, null, "Fecha de alta", html, 'text-center');
                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center">';
                    html += `<li class="nav-item"><a href="${baseURL}administrador/data:id$" class="btnEditarAdministrador d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid icono-accion" style="height:26px; width:26px;"></i></a></li>`;
                    if(core.Security.getRole() == 'SUDO'){
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarAdministrador d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid icono-accion" style="height:26px; width:26px;"></i></a></li>';
                    }
                    html += '</ul>';
                    CoreUI.tableData.addColumn(nombreTabla, null, "", html, 'text-center');

                //  Columna de exportar comunidades solo para el sudo
                    if(core.Security.getRole() == 'SUDO')
                    {
                        html = '<a href="javascript:void(0);" class="btnExportarComunidades btn btn-primary d-inline-block" data-id="data:id$" data-nombre="data:nombre$">Exportar comunidades</a>';
                        CoreUI.tableData.addColumn(nombreTabla, null, "", html, 'text-center');                
                    }
                CoreUI.tableData.render(nombreTabla, "Administrador", "administrador/list");
        }
    },

    /** Recupera el listado de administradores en el dashboard */
    listadoDashboard: async function()
    {
        // await administradorCore.getAll().then(async (data)=>{
            this.renderTabla();
        // });
  
    },

    getAdministrador: async function(comunidadId)
    {
        await apiFincatech.get('administrador/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            administradorCore.administrador = responseData.Usuario[0];
            administradorCore.administrador['password'] = '';
            console.log(administradorCore.comunidad);
            return administradorCore.comunidad;
        });
    },

    /** Recupera los datos de las administradores desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('administrador/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            administradorCore.administradores = responseData.Usuario;
            // administradorCore.administradores.total = administradorCore.administradores.length;
        });

    },

    /**
     * Exporta las comunidades a un fichero excel que se mostrará para descargar
     * @param {*} administradorId 
     */
    ExportComunidadesToExcel: function(administradorId, nombreAdministrador)
    {
        Swal.fire({
            title: 'Exportación de comunidades',
            html: `<p>Clique el botón <strong>descargar</strong> para poder descargar un fichero Excel con la información de todas las comunidades asociadas a este administrador así como los servicios contratados.</p>
            <h6 class="p-2 bg-primary shadow-neumorphic mb-3 mt-3 br-10 text-white font-weight-bold text-uppercase"></i> Instrucciones tras la descarga</h6>
            <p class="text-left">Las columnas con el nombre <strong>ID interno (No cambiar)</strong> no puede cambiarse su valor o el proceso de importación no se ejecutará correctamente.</p>
            <p class="text-left">Las columnas que hacen referencia al <strong>estado de contratación</strong> solo admiten 2 valores: 0 ó 1 de donde 0 indica que no está contratado y 1 que está contratado el servicio especificado.</p>
            <p class="text-left">Las columnas que hacen referencia al <strong>precio</strong> tanto de comunidad como de Fincatech no deben contener símbolos de Euro, sólo valores numéricos.</p>`,
            grow:'false',
            width: '60rem',
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="bi bi-save mr-2"></i> Descargar',
            cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                actions: 'text-center p-3 shadow-inset rounded-pill border-light border-2 bg-white',
                confirmButton: 'btn btnSaveData btn-success rounded-pill shadow d-block pb-2 pt-2 cofirmButtonModal',
                cancelButton: ' btn btn-danger btnCancelSave rounded-pill shadow d-block pb-2 pt-2 mr-3 cacelButtonModal',
                popup: 'bdg-transparent'
            }            
          }).then((result) => {
            if (result.isConfirmed) 
            {
                //  Llamamos al endpoint de exportar
                apiFincatech.get(`administrador/${administradorId}/comunidades/excelexport`).then(( result )=>{
                    let res = JSON.parse(result);
                    if(res.data == 'Error')
                    {
                        CoreUI.Modal.Error(res.status.error,'Exportación de comunidades');
                    }else{
                        // Decodificar el Base64
                        let binaryData = atob(res.data);

                        // Crear un array de bytes
                        let bytes = new Uint8Array(binaryData.length);
                        for (let i = 0; i < binaryData.length; i++) {
                            bytes[i] = binaryData.charCodeAt(i);
                        }

                        // Crear un Blob
                        let blob = new Blob([bytes], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                        // Crear un enlace de descarga
                        let a = document.createElement("a");
                        a.href = window.URL.createObjectURL(blob);

                        // Establecer el nombre del archivo
                        nombreAdministrador = nombreAdministrador.replace(/ /g, "_");
                        a.download = `exportacion_comunidades_${nombreAdministrador}.xlsx`;

                        // Simular clic en el enlace para descargar el archivo
                        a.click();
                    }
                });
            }
        }); 
    },

    // Componente Representante Legal
    RepresentanteLegal:{

        Events: function(){

            if( $('.form-representante-legal') && core.actionModel === 'get')
            {
                //  Bindeamos los eventos correspondientes
                administradorCore.Helper.changeUploadStatus('status-frontal',true);
                administradorCore.Helper.changeUploadStatus('status-anverso',true);  
                // nos suscribimos al evento 'hsFormDataLoaded'
                if(core.model == 'Representantelegal'){
                    $('body').on('hsFormDataLoaded', function(event, formularioId) {
                        //  Código a ejecutar cuando se dispara el evento
                            $('.fecha-frontal').html(moment(core.Modelo.entity.Representantelegal[0].documentofrontal.created).locale('es').format('DD/MM/YYYY HH:mm'));
                            $('.fecha-anverso').html(moment(core.Modelo.entity.Representantelegal[0].documentotrasera.created).locale('es').format('DD/MM/YYYY HH:mm'));
                        //  Cargamos las comunidades para las cuales ha solicitado el certificado digital y es representante legal
                    });
                }
  
            }

        },

        Controller:{

            Guardar: function(){

                //  Validamos los datos principales del formulario
                if(!core.Forms.Validate('form-representante-legal'))
                {
                    CoreUI.Modal.Error('Corrija los campos obligatorios para poder guardar la información');
                    return;
                }

                //  Validamos que haya adjuntado imágenes siempre que sea un nuevo representante legal
                if(!administradorCore.RepresentanteLegal.Validator.ValidateImages()){
                    CoreUI.Modal.Error('Debe adjuntar las imágenes del documento de identidad');
                    return;
                }

                //  Determinamos si es una inserción o una actualización
                switch(core.actionModel)
                {
                    case 'add':
                        administradorCore.RepresentanteLegal.Controller.Insert();
                        break;
                    case 'get':
                        administradorCore.RepresentanteLegal.Controller.Update();
                        break;
                }
            },

            Insert: function(){

                //  Mapeamos el formulario con los datos
                    core.Forms.mapDataToSave();

                //  Inyectamos la imagen frontal y trasera del documento de identidad
                    administradorCore.Helper.setDocumentImages();

                //  Enviamos al WS la información para crear el representante legal
                    core.Forms.Save();

                //  Establecemos el ID del administrador por si actualiza el usuario
                    $('#administradorid').val( $('#hadministradorid').val() );
            },

            Listado: function()
            {
                let resultado1 =  administradorCore.RepresentanteLegal.Controller.GetAll(1).then( (resultado)  =>{
                    console.log('resultado: ', JSON.parse(resultado));
                });
                console.log('resultado1: ', resultado1);

            },

            GetAll: async function(administradorId)
            {
                const listado = apiFincatech.get(`administrador/${administradorId}/representantelegal/list`);
               // console.log(JSON.parse(listado));
                return listado;
            },

            Update: function(){

            },

            /**
             * Elimina el representante legal seleccionado
             * @param {*} representanteLegalId 
             * @param {*} representanteNombre 
             */
            Eliminar: function(representanteLegalId, representanteNombre){
                core.Modelo.Delete("representantelegal", representanteLegalId, representanteNombre, "listadoAdministradorRepresentantesLegales");
            },

            //  TODO: 
            RenderTablaComunidadesRepresentadas: function()
            {

            },

            RenderTablaRepresentantesLegales: function()
            {
                if($('#listadoAdministradorRepresentantesLegales').length)
                {
                        
                    //  Cargamos el listado de administradores
                        CoreUI.tableData.init();
        
                    //  Nombre
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', 
                        function(row, type, val, meta)
                        {
                            return `${row.nombre} ${row.apellido} ${row.apellido2}`;
                        },
                        "Nombre y apellidos", null, 'text-left');                    
        
                    //  CIF / NIF
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', "documento", "CIF");
        
                    //  Email
                        var html = '<a href="mailto:data:email$" class="pl-1 pr-1">data:email$</a>';
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', null, "EMAIL", html);
        
                    //  Teléfono
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', "telefono", "TELEFONO");
        
                    //  Fecha de alta
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', 
                        function(row, type, val, meta)
                        {
                            var fechaCreacion;

                            //timeStamp = moment(row.lastlogin, 'YYYY-MM-DD hh:mm').unix();
                            fechaCreacion = moment(row.created).locale('es').format('L');
        
                            return `${fechaCreacion}`;
                        },
                        "Fecha de alta", null, 'text-center');
        
                    //  Estado
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', 
                        function(row, type, val, meta)
                        {
                            return `${row.estado}`;
                        },
                        "Estado", null, 'text-center');
        
                    //  Columna de acciones
                        CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', 
                        function(row, type, val, meta)
                        {
                            var html = '<ul class="nav justify-content-center">';
                                html += `<li class="nav-item"><a href="${baseURL}representantelegal/${row.id}" class="btnEditarRepresentanteLegal d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid mr-2" style="height:26px; width:26px;"></i></a></li>`;
                                if(core.Security.getRole() == 'SUDO'){
                                    html += `<li class="nav-item">
                                                <a href="javascript:void(0);" class="btnEliminarRepresentanteLegal d-inline-block" data-id="${row.id}" data-nombre="${row.nombre} ${row.apellido} ${row.apellido2}"><i data-feather="trash-2" class="text-danger img-fluid" style="height:26px; width:26px;"></i>
                                            </li>`;
                                }
                                html += '</ul>';
                            return html;
                        },
                        "", null, 'text-center');

                        // var html = '<ul class="nav justify-content-center">';
                        //     html += `<li class="nav-item"><a href="${baseURL}representantelegal/data:id$" class="btnEditarRepresentanteLegal d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid mr-2" style="height:26px; width:26px;"></i></a></li>`;
                        //     if(core.Security.getRole() == 'SUDO'){
                        //         html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarRepresentanteLegal d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid" style="height:26px; width:26px;"></i></li></ul>';
                        //     }
                        // CoreUI.tableData.addColumn('listadoAdministradorRepresentantesLegales', null, "", html, 'text-center');
                        $('#listadoAdministradorRepresentantesLegales').addClass('no-clicable');
                        CoreUI.tableData.render("listadoAdministradorRepresentantesLegales", "representantelegal", `administrador/${core.modelId}/representantelegal/list`);
                }
            }

        },

        Model:{
            frontDocumentBase64: null,
            frontDocument: null,
            rearDocumentBase64: null,
            rearDocument: null
        },

        Entity:{

            ImagenDocumentoFrontal: null,
            ImagenDocumentoTrasera: null,

        },

        Validator: {
            /**
             * Valida que las imagenes hayan sido adjuntadas
             * @returns bool
             */
            ValidateImages: function()
            {
                if(core.actionModel === 'add')
                {
                    return (CertificadoDigital.Model.frontDocumentBase64 === null || CertificadoDigital.Model.rearDocumentBase64 === null) ? false : true;
                }
                //  Si es una edición entonces devolvemos true por defecto
                //  ya que al darlo de alta se habrá tenido que subir la imágen de forma obligatoria
                return true;
            }
        }

    },

    Helper:{

        /**
         * Cambia el estado de subida del documento de identificación
         * @param {*} dest 
         * @param {*} uploaded 
         */
        changeUploadStatus: function(dest, uploaded)
        {
            if(uploaded)
            {
                $(`.${dest}`).html(`<span class="badge rounded-pill text-uppercase bg-success d-block pt-2 pb-2 pl-5 pr-5 mx-3">Subido</span>`);
            }else{
                $(`.${dest}`).html(`<span class="badge rounded-pill text-uppercase bg-danger d-block pt-2 pb-2 pl-5 pr-5 mx-3">Pendiente</span>`);
            }
        },

        readURL: function(input, dest) 
        {
            console.log('1');
            if (input.files && input.files[0]) 
            {
                var reader = new FileReader();
                reader.onload = function (e) {
                    console.log(input);
                    $(`body .${dest}`).attr('src', e.target.result);
                    var fullPath = document.getElementById(input.id).value;

                    if (fullPath) {
                        var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                        var filename = fullPath.substring(startIndex);
                        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                            filename = filename.substring(1);
                        }
                    }     
                    console.log('traza');
                    console.log(fullPath.filesize / 1024);
                    //  Validamos que no exceda de 3mb el documento
                    if( (fullPath.filesize / 1024) > 3)
                    {
                        CoreUI.Modal.Error('El archivo no puede superar los 3Mb de tamaño. Seleccione otro, por favor');
                        return;
                    }

                    switch(dest){
                        case 'imgfileFrontDocument':
                            CertificadoDigital.Model.frontDocumentBase64 = e.target.result;
                            CertificadoDigital.Model.frontDocument = filename;  
                            //  Cambiamos el estado de la subida
                            administradorCore.Helper.changeUploadStatus('status-frontal',true);
                            break;
                        case 'imgfileRearDocument':
                            CertificadoDigital.Model.rearDocumentBase64 = e.target.result;
                            CertificadoDigital.Model.rearDocument = filename;
                            //  Cambiamos el estado de la subida
                            administradorCore.Helper.changeUploadStatus('status-anverso',true);
                            break;
                    }
                }
                console.log('traza1');

                reader.readAsDataURL(input.files[0]);
            }
        },

        setDocumentImages: function()
        {
            core.Forms.data['frontImageBase64'] = CertificadoDigital.Model.frontDocumentBase64;
            core.Forms.data['frontImageName'] = CertificadoDigital.Model.frontDocument;
            core.Forms.data['rearImageBase64'] = CertificadoDigital.Model.rearDocumentBase64;
            core.Forms.data['rearImageName'] = CertificadoDigital.Model.rearDocument;
        }

    },    

}

$(()=>{

    document.addEventListener('coreInitialized', function(event) {
        administradorCore.init();
    });

});