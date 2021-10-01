let comunidadesCore = {

    comunidades: Object(),
    comunidad: Object(),

    init: async function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        this.events();

        //  Comprobamos si se está cargando el listado
        if(core.actionModel == "list" && core.model.toLowerCase() == "comunidad")
        {

            //  Recuperamos el listado de comunidades
            await comunidadesCore.listadoDashboard();
            await comunidadesCore.renderMenuLateral();
        }

    },

    // Gestión de eventos
    events: function()
    {

        /** Override del método de guardar para poder enganchar los servicios */
        if(core.model.toLowerCase() == "comunidad")
        {
            $('body').on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
                evt.stopImmediatePropagation();
                comunidadesCore.guardarComunidad();
            });    
        }

        $('body').on(core.helper.clickEventType, '.btnVerComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.verModalComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    guardarComunidad: function()
    {   
        //  Mapeamos los datos iniciales de la comunidad
            core.Forms.mapDataToSave();

        //  Mapeamos los datos de los servicios contratados
            serviciosCore.mapServiciosContratados();

        //  Guardamos los datos ya mapeados correctamente
            core.Forms.Save( true );
    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("comunidad", id, nombre, "listadoComunidades");
    },

    /** TODO: Muestra un modal con la info de la comunidad */
    verModalComunidad: async function(idComunidad)
    {
        //  Llamamos al endpoint de la comunidad para recuperar la información mediante el registro
        var infoComunidad;

        comunidadesCore.getComunidad(idComunidad).then((result)=>{

            //console.log(result);
            infoComunidad = result;
            // Construimos el modal y lo lanzamos para mostrar la información
            apiFincatech.getView("modals", "comunidades/modal_info").then((resultHTML)=>{

                result = CoreUI.Utils.parse(resultHTML, comunidadesCore.comunidad);
                console.log(result);
                CoreUI.Modal.GetHTML('modalInfoComunidad', result, comunidadesCore.comunidad.nombre);
            });

        });
    },

    renderMenuLateral: async function()
    {
        $('.navComunidades').append('<li class="sidebar-header">Comunidades</li>');
        comunidadesCore.comunidades.forEach( function(valor, indice, array){
            var html = `<li class="sidebar-item">
                            <a class="sidebar-link" href="/fincatech/comunidad/${valor['id']}">
                                <div class="row">
                                    <div class="col-2">
                                        <img src="/fincatech/public/assets/img/icon_edificio.png" class="img-responsive feather">
                                    </div>
                                    <div class="col-10 pr-0">
                                        <span class="align-middle">${valor['codigo']} - ${valor['nombre']}</span>
                                    </div>
                                </div>
                            </a>
                        </li>`;
            $('.navComunidades').append(html);
            
        });
        feather.replace();
    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTabla: async function()
    {
        if($('#listadoComunidad').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn("codigo","COD");
            //  Nombre
            CoreUI.tableData.addColumn("nombre", "NOMBRE");

            //  Administrador
            CoreUI.tableData.addColumn("usuario[0].nombre", "Administrador");            
                // var html = `<a href="${baseURL}administrador/data:usuarioId$" class="pl-1 pr-1">data:usuario.nombre$</a>`;
                // CoreUI.tableData.addColumn(null, "ADMINISTRADOR", html);

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn(null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn("telefono", "TELEFONO");

            //  Documentos pendientes de subir
            CoreUI.tableData.addColumn("nombre", "doc pend. de subir");

            //  Pendientes de verificar
            CoreUI.tableData.addColumn("nombre", "doc pend. de verificar");

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha alta", html);

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnVerComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="eye" class="text-info img-fluid"></i></a></li>';
                    html += `<li class="nav-item"><a href="${baseURL}comunidad/data:id$" class="btnEditarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarComunidad d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

            CoreUI.tableData.render("listadoComunidad", "Comunidad", "comunidad/list");
        }

    },

    /**
     * Carga los datos del listado de comunidades en la tabla listadoComunidades
     */
    renderTablaComunidadesAdministrador: async function(id)
    {
        if($('#listadoComunidadesAdministrador').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();
            //  Código
            CoreUI.tableData.addColumn("codigo","COD");
            //  Nombre
            CoreUI.tableData.addColumn("nombre", "NOMBRE");

            //  Email
                var html = '<a href="mailto:data:emailcontacto$" class="pl-1 pr-1">data:emailcontacto$</a>';
                CoreUI.tableData.addColumn(null, "EMAIL", html);

            //  Teléfono
            CoreUI.tableData.addColumn("telefono", "TELEFONO");

            //  Documentos pendientes de subir
            CoreUI.tableData.addColumn("nombre", "doc pend. de subir");

            //  Pendientes de verificar
            CoreUI.tableData.addColumn("nombre", "doc pend. de verificar");

            //  Fecha de alta
                var html = 'data:created$';
                CoreUI.tableData.addColumn(null, "Fecha alta", html);

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html);

            CoreUI.tableData.render("listadoComunidadesAdministrador", "ComunidadesAdministrador", `administrador/${id}/comunidades`);
        }

    },

    /** Recupera el listado de comunidades en el dashboard */
    listadoDashboard: async function()
    {
        await comunidadesCore.getAll().then(async (data)=>{
               $('.statscomunidades .total').html(comunidadesCore.comunidades.total);
               this.renderTabla();
        });
  
    },

    getComunidad: async function(comunidadId)
    {
        await apiFincatech.get('comunidad/' + comunidadId).then( (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidad = responseData.Comunidad[0];
            console.log(comunidadesCore.comunidad);
            return comunidadesCore.comunidad;
        });
    },

    /** Recupera los datos de las comunidades desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('comunidad/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            comunidadesCore.comunidades = responseData.Comunidad;
            comunidadesCore.comunidades.total = comunidadesCore.comunidades.length;
        });

    },

    Import: {

        /**
         * TODO: Valida que el fichero que se está intentando cargar tenga los campos como en la plantilla
         */
        validacionPlantillaComunidades: function()
        {
            return true;
        },


        guardarComunidadDesdePlantilla(jsonExcel)
        {
            /*
                "codpostal": "29680",
                "presidente": "Presidente nombre",
                "telefono": "123456789",
                "emailcontacto": "ejemplo@fincatech.es",
                "cif": "1R",
                "cae.contratado": "0",
                "cae.pvp": "0",
                "cae.preciocomuidad": "0",
                "Rgpd.contratado": "1",
                "Rgpd.pvp": "30",
                "Rgpd.preciocomunidad": "120",
                "prl.contratado": "0",
                "prl.pvp": "0",
                "prl.preciocomunidad": "0",
                "instalaciones.contratado": "0",
                "instalaciones.pvp": "0",
                "instalaciones.preciocomunidad": "0",
                "certificadosdigitales.contratado": "0",
                "Certificadosdigitales.pvp": "0",
                "certificadosdigitales.preciocomunidad": "0"
            }
            */

            var idAdministrador = $('#administradorCargaId option:selected').val();
            var porcentaje = 0;
            for(x = 0; x < jsonExcel.length; x++)
            {


                var Comunidad = {};
                    Comunidad.comunidadservicioscontratados = [];

                var infoServicio = new Object();

                Comunidad.codigo = jsonExcel[x].codigo;
                Comunidad.cif = jsonExcel[x].cif;
                Comunidad.codpostal = jsonExcel[x].codpostal;
                Comunidad.direccion = jsonExcel[x].direccion;
                Comunidad.nombre = jsonExcel[x].nombre;
                Comunidad.localidad = jsonExcel[x].localidad;
                Comunidad.presidente = jsonExcel[x].presidente;
                Comunidad.telefono = jsonExcel[x].telefono;
                Comunidad.emailcontacto = (jsonExcel[x].emailcontacto === undefined ? '' : jsonExcel[x].emailcontacto );
                Comunidad.ibancomunidad = jsonExcel[x].ibancomunidad;
                Comunidad.usercreate = idAdministrador;
                Comunidad.usuarioId = idAdministrador;
                Comunidad.estado = 'A';

                //  Servicios contratados
                /* Orden de los servicios: 

                    1: CAE
                    2: RGPD
                    3: PRL
                    4: Instalaciones
                    5: Certificados digitales

                */
                // Comunidad.comunidadservicioscontratados[0].idcomunidad = jsonExcel[x].localidad;
                infoServicio.idservicio = 1;
                infoServicio.contratado = (jsonExcel[x]['cae.contratado'] === undefined ? 0 : jsonExcel[x]['cae.contratado'] );
                infoServicio.precio = (jsonExcel[x]['cae.pvp'] === undefined ? 0 : jsonExcel[x]['cae.pvp']);
                infoServicio.preciocomunidad = (jsonExcel[x]['cae.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['cae.preciocomunidad']);

                Comunidad.comunidadservicioscontratados.push(infoServicio);

                var infoServicio = new Object();
                infoServicio.idservicio = 2;
                infoServicio.contratado = (jsonExcel[x]['Rgpd.contratado'] === undefined ? 0 :jsonExcel[x]['Rgpd.contratado'] );
                infoServicio.precio = ( jsonExcel[x]['Rgpd.pvp'] === undefined ? 0 : jsonExcel[x]['Rgpd.pvp'] );
                infoServicio.preciocomunidad = ( jsonExcel[x]['Rgpd.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['Rgpd.preciocomunidad'] );
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                var infoServicio = new Object();
                infoServicio.idservicio = 3;
                infoServicio.contratado = (jsonExcel[x]['prl.contratado'] === undefined ? 0 : jsonExcel[x]['prl.contratado']);
                infoServicio.precio = (jsonExcel[x]['prl.pvp'] === undefined ? 0 : jsonExcel[x]['prl.pvp']);
                infoServicio.preciocomunidad = (jsonExcel[x]['prl.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['prl.preciocomunidad']);
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                var infoServicio = new Object();
                infoServicio.idservicio = 4;
                infoServicio.contratado = (jsonExcel[x]['instalaciones.contratado'] === undefined ? 0 : jsonExcel[x]['instalaciones.contratado']);
                infoServicio.precio = (jsonExcel[x]['instalaciones.pvp'] === undefined ? 0 : jsonExcel[x]['instalaciones.pvp']);
                infoServicio.preciocomunidad = (jsonExcel[x]['instalaciones.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['instalaciones.preciocomunidad']);
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                var infoServicio = new Object();
                infoServicio.idservicio = 5;
                infoServicio.contratado = ( jsonExcel[x]['certificadosdigitales.contratado'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.contratado'] );
                infoServicio.precio = ( jsonExcel[x]['certificadosdigitales.pvp'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.pvp'] );
                infoServicio.preciocomunidad = ( jsonExcel[x]['certificadosdigitales.preciocomunidad'] === undefined ? 0 : jsonExcel[x]['certificadosdigitales.preciocomunidad'] ); 
                Comunidad.comunidadservicioscontratados.push(infoServicio);

                console.log(Comunidad);

                //  Generamos la comunidad en base de datos
                core.Modelo.Insert('comunidad', Comunidad, false);
                porcentaje = (( x * 100 ) / jsonExcel.length).toFixed(2);
                $('.wrapperProgresoCarga .progress-bar-striped').attr('aria-valuenow',`${porcentaje}%`);
                $('.wrapperProgresoCarga .progress-bar-striped').css('width',`${porcentaje}%`);
                $('.wrapperProgresoCarga .progress-bar-striped').html(`${porcentaje}%`);  

                $('.wrapperProgresoCarga .progresoCarga').html(`(${x} de ` + (jsonExcel.length) + ')');

            }

            //  Obtenemos el ID del administrador seleccionado
            // FIXME: Arreglar el texto para plural y singular
                $('.wrapperProgresoCarga .progresoCarga').html('(' + jsonExcel.length + ' comunidad(es) importada(s))');
                $('.btnCerrarProceso').show();
                $('.wrapperProgresoCarga .progress-bar-striped').attr('aria-valuenow',`100%`);
                $('.wrapperProgresoCarga .progress-bar-striped').css('width',`100%`);
                $('.wrapperProgresoCarga .progress-bar-striped').html(`100%`);                  
        },
    
        leerPlantillaComunidades: function(xlsxflag)
        {

            /*Checks whether the browser supports HTML5*/  
            if (typeof (FileReader) != "undefined") 
            {  
    
                var readerExcel = new FileReader();  
    
                readerExcel.onload = function (e) {  
    
                    $('.wrapperProgresoCarga').show();

                    var data = e.target.result;  
                    /*Converts the excel data in to object*/  
                    if (xlsxflag) {  
                        var workbook = XLSX.read(data, { type: 'binary' });  
                    }  
                    else {  
                        var workbook = XLS.read(data, { type: 'binary' });  
                    }  
                    /*Gets all the sheetnames of excel in to a variable*/  
                    var sheet_name_list = workbook.SheetNames;  
    
                    var cnt = 0; /*This is used for restricting the script to consider only first sheet of excel*/  
                    sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/  
                        /*Convert the cell value to Json*/  
                        if (xlsxflag) {  
                            var exceljson = XLSX.utils.sheet_to_json(workbook.Sheets[y]);  
                        }  
                        else {  
                            var exceljson = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y]);  
                        }  

                        $('.wrapperProgresoCarga .progress-bar').css('width','0%');
                        $('.wrapperProgresoCarga .progress-bar-striped').html('0%');

                        if (exceljson.length > 0 && cnt == 0) 
                        {  
                            $('.wrapperProgresoCarga .progresoCarga').html(`(0 de ` + (exceljson.length) + ')');
                            comunidadesCore.Import.guardarComunidadDesdePlantilla(exceljson);
                        }  
                    });
                }  
    
                if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/  
                    readerExcel.readAsArrayBuffer($("#ficheroAdjuntarExcel")[0].files[0]);  
                }  
                else {  
                    readerExcel.readAsBinaryString($("#ficheroAdjuntarExcel")[0].files[0]);  
                }  
            }else {  
                alert("Sorry! Your browser does not support HTML5!");  
            }  
        },
    
        /**
         * Importa comunidades desde un fichero excel
         */
        importarComunidades: function()
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

            //  Si es un fichero de Excel debemos validar los campos que trae el fichero
            //  si no tiene el formato de la plantilla entonces avisamos al usuario que ese fichero no es válido, que se descargue la plantilla
            //  y volvemos a mostrar el modal de carga automática de comunidades solo si es sudo
                if(!comunidadesCore.Import.validacionPlantillaComunidades())
                {
                    $('.wrapperMensajeErrorCarga .mensaje').html('El fichero no es válido. Utilice la plantilla de Fincatech.');
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
                comunidadesCore.Import.leerPlantillaComunidades( xlsxflag );
    
        }

    },


}

$(()=>{
    comunidadesCore.init();
});