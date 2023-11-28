// const { default: Swal } = require("sweetalert2");

let CertificadoDigital = {

    Init: async function()
    {
        if($('body #listadoCertificadoDigitalPendiente').length > 0)
        {
            CertificadoDigital.Render.CertificadosPendientes();
        }

        CertificadoDigital.Events();
    },

    Events: function()
    {
        $('body').on(core.helper.clickEventType, '.enlaceCertificadoDigital', function(e)
        {
            //  Cargamos las empresas asociadas a la comunidad en pantall
                documentalCore.Comunidad.renderTablaDocumentacionComunidadCertificadoDigital(core.modelId);
        });  
        
        /** Carga de modal con representantes legales asociados al administrador en sesión */
        $('body').on(core.helper.clickEventType, '.btnSolicitarCertificadoComunidad', function(e){
            //  Selección de representante legal mediante modal
            CertificadoDigital.Solicitud.SeleccionarRepresentanteLegal();
        });
        /**
         * Confirmación de representante legal para solicitar el certificado digital de la comunidad
         */
        $('body').on(core.helper.clickEventType,'.btnConfirmarRepresentanteLegal', function(e){           
            let representantelegalId = $('body #representanteLegal option:selected').val();
            CertificadoDigital.Solicitud.CertificadoIndividual(core.modelId, representantelegalId);
        });

        $('body').on(core.helper.clickEventType, '.btnProcesarSolicitudesCertificadoDigital', function(e){
            CertificadoDigital.Solicitud.CertificadoMultiple();
        });

        $('body').on(core.helper.clickEventType, '.btnSelectAllComunity', function(e){
            CertificadoDigital.GUI.SelectComunityStatus(true);
        });

        $('body').on(core.helper.clickEventType, '.btnDeselectAll', function(e){
            CertificadoDigital.GUI.SelectComunityStatus(false);
        });

        //  Solicitud múltiple de certificados desde modal
        $('body').on(core.helper.clickEventType, '.btnProcesarSolicitudCertificados', function(e){
            $('.swal2-container .mensajeError').text('');
            CertificadoDigital.Model.RequestMultipleCertificate();
        });

    },
    
    GUI: {

        /**
         * 
         */
        CargarComunidadesSeleccionadasIntoModal: function()
        {
            CertificadoDigital.Model.LoadDataFromSelectedRows();
            $('body .comunidadesCertificadoSolicitado').text(CertificadoDigital.Model.comunityNames.join(', '));
        },

        /**
         * 
         * @param {*} status 
         */
        SelectComunityStatus: function(status)
        {
            $('.chkSelectorComunidad').each(function(evt){
                $(this).prop('checked', status);
            });
        }
    },

    Helper:{

        readURL: function(input, dest) 
        {
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

                    switch(dest){
                        case 'imgfileFrontDocument':
                            CertificadoDigital.Model.frontDocumentBase64 = e.target.result;
                            CertificadoDigital.Model.frontDocument = filename;                            
                            break;
                        case 'imgfileRearDocument':
                            CertificadoDigital.Model.rearDocumentBase64 = e.target.result;
                            CertificadoDigital.Model.rearDocument = filename;
                            break;
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    },

    Model:{
        frontDocument: null,
        frontDocumentBase64: null,
        rearDocument: null,
        rearDocumentBase64: null,
        comunityIds: Array(),
        comunityNames: Array(),
        solicitudIds: Array(),

        /**
         * Carga en el modelo las solicitudes seleccionadas con la información de comunidad asociada
         */
        LoadDataFromSelectedRows: function(){

            CertificadoDigital.Model.solicitudIds = Array();
            CertificadoDigital.Model.comunityIds = Array();
            CertificadoDigital.Model.comunityNames = Array();

            $('.chkSelectorComunidad').each(function(evt){
                if($(this).is(':checked')){
                    let rowId = $(this).attr('data-idrow');
                    let nombreComunidad = window['tablelistadoCertificadoDigitalPendiente'].row(rowId).data().nombre;
                    CertificadoDigital.Model.solicitudIds.push( $(this).attr('data-idsolicitud') );
                    CertificadoDigital.Model.comunityIds.push( $(this).attr('data-idcomunidad') );
                    CertificadoDigital.Model.comunityNames.push( nombreComunidad );
                }
            });
            CertificadoDigital.Model.comunityNames.sort();
        },

        RequestMultipleCertificate: async function(){
            if(!CertificadoDigital.Model.frontDocument || !CertificadoDigital.Model.rearDocument)
            {
                $('.swal2-container .mensajeError').text('Debe adjuntar el documento de identidad escaneado tanto su parte frontal como su parte trasera');
            }else{
                //  Procesamos y enviamos al endpoint la solicitud
                var data = Object();
                data = {
                    solicitudIds: CertificadoDigital.Model.solicitudIds.join(','),
                    comunityIds: CertificadoDigital.Model.comunityIds.join(','),
                    userId: core.Security.user,
                    documentoFrontalBase64: CertificadoDigital.Model.frontDocumentBase64,
                    documentoFrontalNombre: CertificadoDigital.Model.frontDocument,
                    documentoTraseroBase64: CertificadoDigital.Model.rearDocumentBase64,
                    documentoTraseroNombre: CertificadoDigital.Model.rearDocument                    
                };  
                await apiFincatech.post('certificadodigital/comunidad/createrequest',data).then( (response) =>{
                    Swal.close();
                    var responseData = JSON.parse(response);
                    if(responseData.status['response'] == "ok"){
                        
                        CoreUI.Modal.Success('La solicitud se ha realizado correctamente.<br><br>Una vez que el Operador de la Autoridad de Registro haya validado las solicitudes, recibirá en su e-mail las instrucciones para descargar el certificado digital.');
                        CertificadoDigital.Render.CertificadosPendientes();
                    }else{
                        let mensajeError = `No se ha podido completar la solicitud por los siguientes motivos:<br><br>`;
                        mensajeError = `<p class="text-left">${mensajeError}</p>`;
                        CoreUI.Modal.Error(mensajeError,'Solicitud de certificado digital');
                    }
                });
            }
        }
    }

    ,

    Solicitud:{

        /**
         * Solicita el certificado digital para una comunidad
         * @param {*} comunidadId 
         */
        CertificadoIndividual: async function(comunidadId, representanteLegalId){

            //  Si la validación ha sido correcta, enviamos la información al WS
            await apiFincatech.get(`comunidad/${comunidadId}/solicitarcertificado/${representanteLegalId}`).then(async (data)=>
            {
                result = JSON.parse(data);
                if(result.data == 'error')
                {
                    CoreUI.Modal.Error(result.status.error,'Solicitud certificado digital');
                }else{
                    //TODO: Cambiar mensaje por el que puso Cristóbal en el e-mail
                    CoreUI.Modal.Success('Se ha solicitado correctamente el certificado digital para la comunidad y está a la espera de ser validado y aprobado por un Operador de Autoridad de Registro.<br><br>Recibirá un e-mail con el resultado de la validación.','Solicitar certificado digital');
                }
            });
        },

        CertificadoMultiple: async function(){
            if(CertificadoDigital.Validate.HasSelectedComunity())
            {
                //  Debemos mostrar el modal de adjuntar el DNI por ambas caras para poder procesar la solicitud de certificado
                apiFincatech.getView('modals','modal_documento_identificativo').then( (result)=>{
                    CoreUI.Modal.CustomHTML(result, null, null,'90%');
                }).then(()=>{
                    CertificadoDigital.GUI.CargarComunidadesSeleccionadasIntoModal();
                });
            }else{
                CoreUI.Modal.Error('Debe seleccionar al menos una comunidad para poder solicitar y emitir el certificado digital correspondiente', 'Error');
            }
        },

        /**
         * Muestra un modal con todos los representantes legales disponibles para el administrador
         */
        SeleccionarRepresentanteLegal: async function()
        {

            let datos = Object();

            datos.idcomunidad = core.modelId;
            datos.nombreComunidad = core.Modelo.entity.Comunidad[0].nombre;
            datos.codigoComunidad = core.Modelo.entity.Comunidad[0].codigo;

            apiFincatech.get(`administrador/${core.Security.user}/representantelegal/list`).then((result) =>
            {
                datos.representanteslegales = JSON.parse(result).data['representantelegal'];
                //  console.log(datos.representanteslegales);
                //  Comprobamos que tenga al menos 1 representante legal dado de alta en el sistema, en caso contrario avisamos.
                if(datos.representanteslegales.length == 0)
                {
                    CoreUI.Modal.Error('Actualmente no tiene representantes legales asignados.<br>Por favor, contacte con Fincatech para más información.','Certificado digital de comunidad');
                }else{

                    apiFincatech.getView("modals", "certificadodigital/modal_representante_legal", JSON.stringify(datos)).then((resultHTML)=>{
                        CoreUI.Modal.CustomHTML(resultHTML, 'SOLICITUD APROBACIÓN CERTIFICADO DIGITAL',null, '64em');
                    }).then(()=>{
                        //  Inicializamos el combo de representantes legales
                        // core.Forms.initializeSelectData();
                        // console.log('inicializacion select');
                        $('body #representanteLegal').select2({
                            theme: 'bootstrap4',
                            placeholder: "Seleccione una opción",
                          });                        
                    });

                }


            });

        }

    },

    Render:{

        renderTablaComunidadCertificadoDigital: function(comunidadId)
        {
            if($('#listadoDocumentacionCertificadoDigital').length)
            {
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Título
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', "titulo","Nombre del empleado", null, 'text-left');
    
                //  Fichero
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', 'ficheroscomunes[0]', "DOCUMENTO", null, 'text-center', null, function(data, type, row, meta)
                    {
                        var salida = `
                                      <a href="javascript:void(0);" class="btnAdjuntarDocumentoEmpleadoRGPD" data-idrow="${meta.row}" data-tipo="rgpdempleado" data-idrequerimiento="${row.id}">
                                        <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i>
                                      </a>
                                      `;
                        return salida;
                    });
    
                //  Fecha de creación
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', function(row, type, val, meta){

                        var descarga = '';
                        var fechaSubida = row.updated;

                        //  Descarga de fichero
                        if(row.ficheroscomunes.length > 0)
                        {
                            descarga = `<a href="${baseURL}public/storage/${row.ficheroscomunes[0].nombrestorage}" download="${row.ficheroscomunes[0].nombre}" class="d-block" data-toggle="tooltip" data-placement="bottom" title="Ver documento" data-original-title="Ver documento" target="_blank">
                                            <i class="bi bi-file-earmark-arrow-down text-success" style="font-size: 24px;"></i>
                                        </a>`;
                        }
                        
                        // if(!row.ficheroscomunes.length || !row.updated)
                        if(!row.ficheroscomunes.length)
                        {
                            //  Renderizar estado no adjuntado
                                return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                        }else{

                            //  Puede que haya dado de alta el requerimiento y no tener fecha de actualización
                                if(!row.updated )
                                {
                                    fecha = row.created;
                                }else{
                                    fecha = row.updated;
                                }

                            //  Renderizamos el icono de descarga junto con la fecha
                                fechaSubida = `<span class="small">Subido el ${moment(fecha).locale('es').format('L')}</span>`;
                                return `<p class="mb-0 text-center">${descarga} ${fechaSubida}</p>`;
                        }

                    },"DOCUMENTO ADJUNTADO", null, 'text-center');      

                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarDocumentoRGPD d-inline-block" data-id="data:id$" data-tipo="rgpdempleado" data-nombre="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoDocumentacionCertificadoDigital', null, "", html);
    
                    $('#listadoDocumentacionCertificadoDigital').addClass('no-clicable');
                  //  CoreUI.tableData.render("listadoDocumentacionCertificadoDigital", "Requerimiento", `rgpd/documentacion/rgpdempleado/1/list`, false, false, false);
            }
        },

        CertificadosPendientes: function(){
            if($('#listadoCertificadoDigitalPendiente').length)
            {
    
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Selector checkbox
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        //  Si el certificado no está aprobado entonces no dejamos seleccionar la comunidad
                        if(row.aprobado == '0' || row.solicitadouanataca == '1' || row.solicitudcertificado == '1'){
                            return '';
                        }

                        let chk = 
                            `<div class="form-check">
                                <input class="form-check-input chkSelectorComunidad mx-auto" type="checkbox" data-idrow="${meta.row}" data-idsolicitud="${row.idsolicitud}" data-idcomunidad="${row.id}" value="${row.idsolicitud}" id="chkSelectorComunidad${row.id}">
                            </div>`;
                        return chk;

                    });

                //  Código de comunidad
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        return row.codigo;
                    },"Cód.", null, 'text-left');

                //  Nombre de la comunidad
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        return row.nombre;
                    },"Comunidad", null, 'text-left');                

                //  Técnico que ha realizado la aprobación
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        return row.tecnicoaprobacion;
                    },"Técnico Operador de Registro", null, 'text-left');  
                
                //  Fecha de solicitud
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     return moment(row.fechasolicitud).locale('es').format('L');
                    // },"Fecha de solicitud", null, 'text-center');                

                //  Solicitud aprobada
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        let output = '';
                        if(row.aprobado == '1')
                        {
                            output = '<span class="badge rounded-pill text-uppercase bg-success d-block pt-2 pb-2 pl-5 pr-5">Aprobada</span>';
                        }else{
                            output = '<span class="badge rounded-pill text-uppercase bg-warning text-dark d-block pt-2 pb-2 pl-5 pr-5">Pendiente de aprobación</span>';
                        }
                        return output;
                    },'Solicitud', null, 'text-center','10%');

                //  Fecha de aprobación
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        if(row.aprobado == '1')
                        {
                            return moment(row.fechaaprobacion).locale('es').format('L');
                        }else{
                            return '';
                        }
                    },"Fecha aprobación", null, 'text-center');                       

                //  Certificado emitido
                    CoreUI.tableData.addColumn('listadoCertificadoDigitalPendiente', function(row, type, val, meta){
                        let output = '';
                        if(row.solicitadouanataca == '1')
                        {
                            output = '<span class="badge rounded-pill text-uppercase bg-success d-block pt-2 pb-2 pl-5 pr-5">Emitido</span>';
                        }else{
                            output = '<span class="badge rounded-pill text-uppercase bg-warning text-dark d-block pt-2 pb-2 pl-5 pr-5">Pendiente emisión</span>';
                        }
                        return output;                        
                    },'Estado certificado', null, 'text-center','10%');
                //  Acción individual
                    // CoreUI.tableData.addColumn('listadoCertificadosPendientesSolicitud', function(row, type, val, meta){
                    //     let boton = `<a href="javascript:void(0);" data-idsolicitud="${row.id}" class="badge rounded-pill bg-success d-block pt-2 pb-2 text-white btnSolicitarCertificado">Solicitar certificado</a>`;
                    //     return boton;
                    // },'', null, 'text-center');

                    $('#listadoCertificadoDigitalPendiente').addClass('no-clicable');
                    CoreUI.tableData.render("listadoCertificadoDigitalPendiente", "comunidad", `certificadodigital/administrador/solicitudes/list`, null, false, true);
            }
        },

    },

    Validate:{
        /**
         * Valida si el usuario ha seleccionado al menos una comunidad para emitir el certificado digital correspondiente
         * @returns 
         */
        HasSelectedComunity: function(){
            let result = false;
            $('.chkSelectorComunidad').each(function(evt){
                if($(this).is(':checked')){
                    result = true;
                }
            });
            return result;
        }
    }

}

$(()=>{
    CertificadoDigital.Init();
})