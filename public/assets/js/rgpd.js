let rgpdCore = {

    Constantes: {

        CargaDocumentoEmpleadoRGPD: `
        <div class="row">
             <div class="col-12 text-center text-uppercase align-self-center">
               <p class="m-0" style="display: block; font-size: 18px;"> Carga de documento</p>
             </div>
         </div>
         <div class="row mb-2 wrapperInformacion">
           <div class="col-12">
               <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el fichero que desea adjuntar</p>
               <p class="mt-3 text-justify" style="font-size: 14px;">2. Presione el botón <strong>Adjuntar documento</strong></p>
           </div>
         </div>
         <div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">
           <div class="col-12">  
               <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
                   <div class="col-2 align-self-center h-100 text-center">
                       <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
                   </div>
                   <div class="col-10 pl-0 align-self-center">
                       <input accept=".pdf, .doc, .docx" class="form-control form-control-sm ficheroAdjuntar border-0" hs-fichero-entity="Documental" id="ficheroadjuntar" name="ficheroadjuntar" type="file">
                   </div>       
               </div>
               <span class="pb-3 d-block text-center pt-2" style="font-size: 13px;">Sólo se permiten ficheros con extensión pdf, doc o docx</span>    
               
              <div class="row form-group wrapperTituloDocumento text-left">
                <div class="col-12">
                  <label>Nombre del empleado</label>
                  <input type="text" class="form-control w-100 tituloDocumentoRGPD mt-2" id="tituloDocumentoRGPD" maxlength="40" name="tituloDocumentoRGPD">
                </div>
              </div>
              <div class="row form-group wrapperObservaciones text-left mt-2 d-none">
                <div class="col-12">
                  <label>Descripción</label>
                  <textarea class="form-control w-100 observacionesDocumentoRGPD shadow-inset border-0 mt-2" id="observacionesDocumentoRGPD" rows="3" name="observacionesDocumentoRGPD"></textarea>
                </div>
              </div>          
               <!-- Mensaje de error --> 
               <div class="wrapperMensajeErrorCarga row text-light p-3" style="display: none; font-size: 14px;">
                   <div class="col-12 bg-danger p-3 rounded shadow-neumorphic">
                     <p class="mensaje"></p>
                   </div>
               </div>          
     
               <!-- Botón de adjuntar documento -->
               <div class="row mt-3">
                 <div class="col-12">
                   <a href="javascript:void(0);" class="btn d-block btn-success bntUploadDocumentoEmpleadoRGPD pt-3 pb-3">Adjuntar documento</a>
                 </div>
               </div>
           </div>
         </div>
         `,

    },

    Init: function()
    {
        rgpdCore.Events();

        //  Inicialización de la tabla de RGPD de empleados del administrador en sesión
        if($('body #listadoRGPDEmpleadosAdministracion').length > 0)
        {
            rgpdCore.Render.renderTablaRGPDEmpleadosAdministrador();
        }

    },

    Events: function()
    {

        $('body').on(core.helper.clickEventType, '.enlaceRGPD', function(e)
        {
            $('.loading').show();
            $('.tituloEmpresasComunidad').text('Empresas externas');
            //  Documentación Cámaras de seguridad
                documentalCore.RGPD.cargarDocumentacionCamarasSeguridad().then ( 
                     documentalCore.renderTablaDocumentacionBasica()
                );
            
                rgpdCore.comprobarContratoAdministracionComunidad(core.modelId);    
                $('.loading').hide();
        });

        //  Documentación básica
        $('body').on(core.helper.clickEventType, '.enlaceRGPDDocumentacionBasica', function(evt)
        {
            documentalCore.RGPD.cargarDocumentacionCamarasSeguridad();
            rgpdCore.comprobarContratoAdministracionComunidad(core.modelId);
        });

        //  Notas informativas
        $('body').on(core.helper.clickEventType, '.enlaceRGPDNotasInformativas', function(evt)
        {
            notasInformativasCore.renderTabla();
        });

        //  Informe de evaluación y seguimiento
        $('body').on(core.helper.clickEventType, '.enlaceRGPDInformeEvaluacionSeguimiento', function(evt)
        {
            informeValoracionSeguimientoCore.renderTabla();
        });

        //  Consultas al DPD
        $('body').on(core.helper.clickEventType, '.enlaceRGPDConsultasDPD', function(evt)
        {
            dpdCore.renderTabla();
        });

        //  Contratos de cesión de datos a terceros
        $('body').on(core.helper.clickEventType, '.enlaceRGPDContratosCesionTerceros', function(evt)
        {
            requerimientoCore.renderTablaDocumentacionContratosCesion().then( () =>{
                requerimientoCore.renderTablaContratosCesion(core.modelId);
            });
        });

        //  Cámaras de seguridad
        $('body').on(core.helper.clickEventType, '.enlaceRGPDCamarasSeguridad', function(evt)
        {
            requerimientoCore.renderTablaCamarasSeguridad(core.modelId);
            //  Comprobamos si la comunidad tiene cámara de seguridad
            $('#chkTieneCamarasSeguridad').prop('checked', ( core.Modelo.entity.Comunidad[0].camarasseguridad == '1' ? 'checked' : '') );
        });
        

        
    },

    comprobarContratoAdministracionComunidad: function(_idComunidad)
    {
        if(!_idComunidad || typeof _idComunidad === 'undefined' || _idComunidad == '')
        {
            return;
        }
        apiFincatech.get(`rgpd/comunidad/${_idComunidad}/administrador/${core.Security.user}/contratoadministracion`).then( result => {
            var datos = JSON.parse(result);
            if(!datos.data)
            {

            }else{
                datos = datos.data;
                console.log(datos);

                $('.btnAdjuntarContrato').attr('data-idrelacionrequerimiento', datos.id);
                $('.tablaContratoAdministracion .estado').html( CoreUI.Utils.renderLabelByEstado(datos.estado) );
                
                if(datos.estado == 'Verificado' || datos.estado == 'Adjuntado')
                {
                    var fechaSubida = moment(datos.created).locale('es').format('L');
                    var htmlSubida = `
                        <p class="mb-0 text-center"><a href="${baseURL}public/storage/${datos.nombrestorage}" class="text-success" download="${datos.nombre}"><i class="bi bi-file-earmark-arrow-down" style="font-size: 24px;"></i></a> </p>Subido el ${fechaSubida}`
                    $('.tablaContratoAdministracion .fechaSubida').html(htmlSubida);
                    
                }
            }
        });        

    },

    Render:{

        renderTablaRGPDEmpleadosAdministrador: function()
        {
            if($('#listadoRGPDEmpleadosAdministracion').length)
            {
                //  Cargamos el listado de comunidades
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];
    
                //  Título
                    CoreUI.tableData.addColumn('listadoRGPDEmpleadosAdministracion', "titulo","Nombre del empleado", null, 'text-left');
    
                //  Fichero
                    CoreUI.tableData.addColumn('listadoRGPDEmpleadosAdministracion', 'ficheroscomunes[0]', "DOCUMENTO", null, 'text-center', '150px', function(data, type, row, meta)
                    {
                        var salida = `
                                      <a href="javascript:void(0);" class="btnAdjuntarDocumentoEmpleadoRGPD" data-idrow="${meta.row}" data-tipo="rgpdempleado" data-idrequerimiento="${row.id}">
                                        <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i>
                                      </a>
                                      `;
                        return salida;
                    });
    
                //  Fecha de creación
                    CoreUI.tableData.addColumn('listadoRGPDEmpleadosAdministracion', function(row, type, val, meta){

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

                    },"DOCUMENTO ADJUNTADO", null, 'text-center', '150px');      

                //  Columna de acciones
                    var html = '<ul class="nav justify-content-center accionesTabla">';
                        html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarDocumentoRGPD d-inline-block" data-id="data:id$" data-tipo="rgpdempleado" data-nombre="data:titulo$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                    CoreUI.tableData.addColumn('listadoRGPDEmpleadosAdministracion', null, '', html, '', '50px');
    
                    $('#listadoRGPDEmpleadosAdministracion').addClass('no-clicable');
                    CoreUI.tableData.render("listadoRGPDEmpleadosAdministracion", "Rgpdempleado", `rgpd/documentacion/rgpdempleado/1/list`, false, false, false);
            }
        }

    }


}

$( () =>
{
    rgpdCore.Init();
});