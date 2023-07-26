
let requerimientoCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoRequerimiento').length)
        {
            await requerimientoCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;       
         }

    },

    events: function()
    {   

        $('body').on(core.helper.clickEventType, '.btnEliminarRequerimiento', (evt)=>{
            evt.stopImmediatePropagation();
            requerimientoCore.Model.Delete( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });        

    },

    Model: {
        Delete: function(id, nombre)
        {
            core.Modelo.Delete('requerimiento', id, nombre, 'listadoRequerimiento', '¿Desea eliminar el requerimiento seleccionado?');
        }
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoRequerimiento').length)
        {

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            //  Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Fecha", html, 'text-center', '80px');

            //  Nombre
                CoreUI.tableData.addColumn('listadoRequerimiento', "nombre","NOMBRE", null, 'text-justify');

            //  Tipo
                CoreUI.tableData.addColumn('listadoRequerimiento', "requerimientotipo[0].nombre", "TIPO", null,'text-left');

            //  Sujeto a revisión
                CoreUI.tableData.addColumn('listadoRequerimiento',function(row, type, val, meta)
                {
                    var sujetoRevision = (row.sujetorevision == '0' || row.sujetorevision == 'null' ? 'No' : 'Sí');
                    return `<p class="text-center m-0">${sujetoRevision}</p>`;
                }, "Sujeto revisión", null, 'text-center');

            //  Requiere descarga previa
                CoreUI.tableData.addColumn('listadoRequerimiento',function(row, type, val, meta)
                {
                    var descargaPrevia = (row.requieredescarga == '0' || row.requieredescarga == 'null' ? 'No' : 'Sí');
                    return `<p class="text-center m-0">${descargaPrevia}</p>`;
                }, "Requiere descarga previa", null, 'text-center');

            //  Tiene caducidad
                CoreUI.tableData.addColumn('listadoRequerimiento',function(row, type, val, meta)
                {
                    var caduca = (row.caduca == '0' || row.caduca == 'null' ? 'No' : 'Sí');
                    return `<p class="text-center m-0">${caduca}</p>`;
                }, "Caduca", null, 'text-center');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Fichero", html, 'text-center', '120px');

            //  Estado activado
                var html = 'data:activado$';
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Activo", html, 'text-center', '100px');

            //  Columna de acciones
            CoreUI.tableData.addColumn('listadoRequerimiento',function(row, type, val, meta)
            {
                var html = `
                    <ul class="nav justify-content-center">
                        <li class="nav-item">
                            <a href="${config.baseURL}requerimiento/${row.id}" class="d-inline-block">
                                <i data-feather="edit-2" class="text-success img-fluid text-success mr-2" style="width:26px;height:26px;"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0);" class="btnEliminarRequerimiento d-inline-block" data-id="${row.id}" data-nombre="${row.nombre}"><i data-feather="trash-2" class="text-danger img-fluid" style="width:26px;height:26px;"></i></a>
                        </li>
                    </ul>`;
                return html;
            }, "&nbsp;", null, 'text-center', '40px');

                // CoreUI.tableData.addColumn('listadoRequerimiento', null, "", html);

                $('#listadoRequerimiento').addClass('no-clicable');
                await CoreUI.tableData.render("listadoRequerimiento", "Requerimiento", "requerimiento/list");
        }
    },

    /**
     * Renderiza la tabla de documentos para descargar relativos a contratos cesión
     */
    renderTablaDocumentacionContratosCesion: async function()
    {
        if($('#listadoDocumentacionContratosCesion').length)
        {

            //  Cargamos el listado de documentos de contratos de cesión
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];
            //  Nombre
                // CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', "nombre","NOMBRE", null, 'text-justify');

            //  Fichero asociado
                var html = `
                        <a href="${config.baseURL}public/storage/data:ficheroscomunes.nombrestorage$" target="_blank">
                            <p class="m-0 d-inline-flex">
                                <i class="bi bi-cloud-arrow-down pr-2" style="font-size:26px;"></i> 
                                <span class="align-self-center">data:nombre$</span>
                            </p>
                        </a>`;
                CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', null, "Fichero", html, 'text-left', '120px');

                $('#listadoDocumentacionContratosCesion').addClass('no-clicable');
                await CoreUI.tableData.render("listadoDocumentacionContratosCesion", "Requerimiento", "rgpd/requerimiento/3/list", false, false, false);
        }
    },

    /**
     * Renderiza la tabla de documentos para descargar relativos a cámaras de seguridad
     */
    renderTablaDocumentacionCamarasSeguridad: async function()
    {
        if($('#listadoDocumentacionCamarasSeguridad').length)
        {

            //  Cargamos el listado de documentos de contratos de cesión
                CoreUI.tableData.init();

            // //  Nombre
            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down mr-2" style="font-size:24px;"></i>data:nombre$</a>'
                CoreUI.tableData.addColumn('listadoDocumentacionCamarasSeguridad', null, "Fichero", html, 'text-left', '120px');

                $('#listadoDocumentacionCamarasSeguridad').addClass('no-clicable');
                await CoreUI.tableData.render("listadoDocumentacionCamarasSeguridad", "Requerimiento", "rgpd/requerimiento/2/list", false, false, false);
        }
    },

    /**
     * Renderiza la tabla de contratos de cesión de datos según el id de la comunidad
     * @param {*} idComunidad 
     */
    renderTablaContratosCesion: async function(idComunidad)
    {
        if($('#listadoContratosCesion').length)
        {
            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];
            // CoreUI.tableData.addColumnRow('listadoCamarasSeguridad', 'documentacionprl');

            //  Título
            CoreUI.tableData.addColumn('listadoContratosCesion', "titulo","Título", null, 'text-left');

            //  Observaciones
            CoreUI.tableData.addColumn('listadoContratosCesion', "descripcion", "Comentarios", null, 'text-justify');

            //  Fichero
                CoreUI.tableData.addColumn('listadoContratosCesion', 'ficheroscomunes[0]', "DOCUMENTO", null, 'text-center', null, function(data, type, row, meta)
                {
                    var descarga = '';;

                    //  Descarga de fichero
                    if(row.ficheroscomunes.length > 0)
                    {
                        descarga = `<a href="${baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" class="mr-2" data-toggle="tooltip" data-placement="bottom" title="Ver documento" data-original-title="Ver documento" target="_blank">
                                        <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                    </a>`;
                    }
                    var salida = `${descarga}
                                  <a href="javascript:void(0);" class="btnAdjuntarDocumentoRGPD" data-tipo="contratoscesion" data-idrequerimiento="${row.id}">
                                    <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i>
                                  </a>
                                  `;
                    return salida;
                });

            // Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoContratosCesion', null, "Fecha de subida", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    //html += `<li class="nav-item"><a href="${baseURL}empleado/data:id$" class="btnEditarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarDocumentoRGPD d-inline-block" data-id="data:id$" data-tipo="contratoscesion" data-nombre="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoContratosCesion', null, "", html);

                $('#listadoCamarasSeguridad').addClass('no-clicable');
                await CoreUI.tableData.render("listadoContratosCesion", "ContratosCesion", `rgpd/documentacion/contratoscesion/${idComunidad}/list`, false, false, false);
        }
    },

    /**
     * Renderiza la tabla de cámaras de seguridad según el id de la comunidad
     * @param {*} idComunidad 
     */
    renderTablaCamarasSeguridad: async function(idComunidad)
    {
        if($('#listadoCamarasSeguridad').length)
        {
            //  Cargamos el listado de comunidades
                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

            //  Título
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', "titulo","Título", null, 'text-left');

            //  Observaciones
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', "descripcion", "Comentarios", null, 'text-justify');

            //  SUBIDA DOCUMENTO DE CÁMARA DE SEGURIDAD
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', 'ficheroscomunes[0]', "SUBIR DOCUMENTO", null, 'text-center', null, function(data, type, row, meta)
                {
                    var salida = '';

                    salida += `<a href="javascript:void(0);" class="btnAdjuntarDocumentoRGPD" data-tipo="camarasseguridad" data-idrequerimiento="${row.id}">
                                    <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i>
                                  </a>`;
                    return salida;
                });

            //  DESCARGA Y ESTADO DEL DOCUMENTO DE CÁMARA DE SEGURIDAD
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', 'ficheroscomunes[0]', "DOCUMENTO SUBIDO", null, 'text-center', null, function(data, type, row, meta)
                {
                    var salida = '';
                    if(row.ficheroscomunes.length > 0)
                    {
                        salida = `
                        <a href="${baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" class="mr-2" data-toggle="tooltip" data-placement="bottom" title="Ver documento" data-original-title="Ver documento" target="_blank">
                            <i class="bi bi-file-earmark-arrow-down text-success" style="font-size: 26px;"></i>
                        </a>`;
                    }else{
                        salida += `<span class="badge bg-danger">Pendiente subir</span>`;
                    }

                    return salida;
                });

                // var html = 'data:updated$';
                //CoreUI.tableData.addColumn('listadoCamarasSeguridad', null, "Fecha de subida", html);
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', 'ficheroscomunes[0]', "Fecha de subida", null, 'text-center', null, function(data, type, row, meta)
                {

                    var salida = '';
                    if(!row.updated)
                    {
                        salida = `
                        <span class="m-0">-</span>`;
                    }else{
                        salida += `<span>${moment(row.updated).locale('es').format('DD/MM/YYYY HH:mm')}</span>`;
                    }

                    return salida;

                });


            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    //html += `<li class="nav-item"><a href="${baseURL}empleado/data:id$" class="btnEditarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarDocumentoRGPD d-inline-block" data-id="data:id$" data-tipo="camarasseguridad" data-nombre="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', null, "", html);

                $('#listadoCamarasSeguridad').addClass('no-clicable');
                CoreUI.tableData.render("listadoCamarasSeguridad", "CamarasSeguridad", `rgpd/documentacion/camarasseguridad/${idComunidad}/list`, false, false, false );
        }
        return true;
    },


    renderEtiquetaEstado: function( value )
    {
        /** Estados posibles
         * p: Pendiente de verificacion
         * v: Verificado
         * r: Rechazado
         * c: Caducado
         * n: Pendiente de subir
         */
        var clase;
        var estado;

        switch (value.toLowerCase()){
            case 'pv':
                estado = 'Pendiente de verificación';
                clase = 'info';
                break;
            case 've':
                estado = 'Verificado';
                clase = 'success';
                break;
            case 're':
                estado = 'Rechazado';
                clase = 'danger';
                break;
            case 'ca':
                estado = 'Caducado';
                clase = 'danger';
                break;
            case 'ps':
                estado = 'Pendiente de subir';
                clase = 'warning';
                break;
            case 'na':
                estado = 'No adjuntado';
                clase = 'danger';
                break;
            default:
                estado = 'Pendiente';
                clase = 'danger';
                break;
        }

        return `<span class="badge rounded-pill bg-${clase} pl-3 pr-3 pt-2 pb-2 d-block">${estado}</span>`

    }

}

$(()=>{
    requerimientoCore.init();
});