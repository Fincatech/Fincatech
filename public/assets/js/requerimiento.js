
let requerimientoCore = {

    NotasInformativas: Object(),
    notainformativa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoRequerimiento').length)
        {
            requerimientoCore.renderTabla();
        }else{
            core.Files.init();
            core.Files.Fichero.entidadId = core.modelId;       
         }

        if( $('#listadoDocumentacionContratosCesion').length)
            requerimientoCore.renderTablaDocumentacionContratosCesion();

        if( $('#listadoContratosCesion'))
            requerimientoCore.renderTablaContratosCesion(core.modelId);

        if( $('#listadoDocumentacionCamarasSeguridad'))
            requerimientoCore.renderTablaDocumentacionCamarasSeguridad();

        if( $('#listadoCamarasSeguridad'))
            requerimientoCore.renderTablaCamarasSeguridad(core.modelId);

    },

    events: async function()
    {   


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

            //  Comunidad asociada
                CoreUI.tableData.addColumn('listadoRequerimiento', "comunidad[0].nombre","Comunidad asociada", null, 'text-left');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a>'
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Fichero", html, 'text-center', '120px');

            //  Estado activado
                var html = 'data:activado$';
                CoreUI.tableData.addColumn('listadoRequerimiento', null, "Activo", html, 'text-center', '100px');

                // $('#listadoRequerimiento').addClass('no-clicable');
                CoreUI.tableData.render("listadoRequerimiento", "Requerimiento", "requerimiento/list");
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

            //  Nombre
                // CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', "nombre","NOMBRE", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down pr-2" style="font-size:24px;"></i> data:nombre$</a>'
                CoreUI.tableData.addColumn('listadoDocumentacionContratosCesion', null, "Fichero", html, 'text-left', '120px');

                $('#listadoDocumentacionContratosCesion').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionContratosCesion", "Requerimiento", "rgpd/requerimiento/3/list", false, false, false);
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
            //     CoreUI.tableData.addColumn('listadoDocumentacionCamarasSeguridad', "nombre","NOMBRE", null, 'text-justify');

            //  Fichero asociado
                var html = '<a href="' + config.baseURL + 'public/storage/data:ficheroscomunes.nombrestorage$" target="_blank"><i class="bi bi-cloud-arrow-down mr-2" style="font-size:24px;"></i>data:nombre$</a>'
                CoreUI.tableData.addColumn('listadoDocumentacionCamarasSeguridad', null, "Fichero", html, 'text-left', '120px');

                $('#listadoDocumentacionCamarasSeguridad').addClass('no-clicable');
                CoreUI.tableData.render("listadoDocumentacionCamarasSeguridad", "Requerimiento", "rgpd/requerimiento/2/list", false, false, false);
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

            // CoreUI.tableData.addColumnRow('listadoCamarasSeguridad', 'documentacionprl');

            //  Título
            CoreUI.tableData.addColumn('listadoContratosCesion', "titulo","Título", null, 'text-left');

            //  Observaciones
            CoreUI.tableData.addColumn('listadoContratosCesion', "descripcion", "Comentarios", null, 'text-justify');

            //  Fichero
                CoreUI.tableData.addColumn('listadoContratosCesion', 'ficheroscomunes[0]', "DOCUMENTO", null, 'text-center', null, function(data, type, row, meta)
                {
                    console.log(row);

                    var salida = `<a href="${baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" class="mr-2" data-toggle="tooltip" data-placement="bottom" title="Ver documento" data-original-title="Ver documento" target="_blank">
                                    <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                  </a>
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
                CoreUI.tableData.render("listadoContratosCesion", "ContratosCesion", `rgpd/documentacion/contratoscesion/${idComunidad}/list`, false, false, false);
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

            // CoreUI.tableData.addColumnRow('listadoCamarasSeguridad', 'documentacionprl');

            //  Título
            CoreUI.tableData.addColumn('listadoCamarasSeguridad', "titulo","Título", null, 'text-left');

            //  Observaciones
            CoreUI.tableData.addColumn('listadoCamarasSeguridad', "descripcion", "Comentarios", null, 'text-justify');

            //  Fichero
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', 'ficheroscomunes[0]', "DOCUMENTO", null, 'text-center', null, function(data, type, row, meta)
                {
                    console.log(row);

                    var salida = `<a href="${baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" class="mr-2" data-toggle="tooltip" data-placement="bottom" title="Ver documento" data-original-title="Ver documento" target="_blank">
                                    <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                  </a>
                                  <a href="javascript:void(0);" class="btnAdjuntarDocumentoRGPD" data-tipo="camarasseguridad" data-idrequerimiento="${row.id}">
                                    <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i>
                                  </a>
                                  `;
                    return salida;
                });

            // Fecha de creación
                var html = 'data:created$';
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', null, "Fecha de subida", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    //html += `<li class="nav-item"><a href="${baseURL}empleado/data:id$" class="btnEditarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarDocumentoRGPD d-inline-block" data-id="data:id$" data-tipo="camarasseguridad" data-nombre="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoCamarasSeguridad', null, "", html);

                $('#listadoCamarasSeguridad').addClass('no-clicable');
                CoreUI.tableData.render("listadoCamarasSeguridad", "CamarasSeguridad", `rgpd/documentacion/camarasseguridad/${idComunidad}/list`, false, false, false );
        }
    },

}

$(()=>{
    requerimientoCore.init();
});