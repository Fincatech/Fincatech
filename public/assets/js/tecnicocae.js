let TecnicoCAE = {

    Init: function(){
        if($('#listadoDocumentosPendientesCAE').length)
        {
            TecnicoCAE.Render.ListadoPendienteCAE();
        }
    },

    Events: function(){

    },

    Controller:{

    },

    Model: {

    },

    Render:{

        ListadoPendienteCAE: function()
        {
            if($('#listadoDocumentosPendientesCAE').length)
            {

                //  Inicializamos la tabla
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

                //

                //  Administrador
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'nombre', 'Administrador', null, 'text-justify', null);

                //  Teléfono
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'telefono', 'Teléfono', null, 'text-justify', null);

                //  E-mail
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'email', 'E-mail', null, 'text-justify', null);

                //  Comunidad
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'comunidad', 'Comunidad', null, 'text-justify', null);

                //  CIF Comunidad
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'cifcomunidad', 'CIF', null, 'text-left', null);

                //  Dirección Comunidad
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'direccioncomunidad', 'Dirección', null, 'text-left', null);

                //  Código postal comunidad
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'codpostalcomunidad', 'C.P.', null, 'text-center', null);

                //  Localidad / Provincia
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'localidadcomunidad', 'Localidad', null, 'text-left', null);
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'provinciacomunidad', 'Provincia', null, 'text-left', null);

                //  Requerimientos pendientes
                    CoreUI.tableData.addColumn('listadoDocumentosPendientesCAE', 'requerimientos', 'Requerimientos Pendientes', null, 'text-left', null);

                //  Estado del requerimiento
                    // CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', 
                    //     function(row, type, val, meta)
                    //     {
                    //         if(row.idficherorequerimiento == null)
                    //         {
                    //             return '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                    //         }else{
                    //             //  Comprobamos el estado del requerimiento
                    //             if(core.Security.getRole() == 'CONTRATISTA')
                    //             {
                                    
                    //                 //  Comprobamos si el contratista ha descargado el documento previamente
                    //                 if(documentalCore.Helper.ComprobarDescargaRequerimientoPorEmpresa(core.Security.user, row.idficherorequerimiento, row.descargas))
                    //                 {
                    //                     return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Descargado</span>';
                    //                 }else{
                    //                     return '<span class="badge rounded-pill bg-warning pl-3 pr-3 pt-2 pb-2 d-block">Pendiente descarga</span>';
                    //                 }
                    //             }else{
                    //                 return '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Subido</span>';
                    //             }
                    //         }
                    //     },
                    // "Estado", null, 'text-center', '10%');

                //  Fichero asociado
                    // CoreUI.tableData.addColumn('listadoDocumentacionComunidadCae', 
                    //     function(row, type, val, meta)
                    //     {
                    //         var ficheroAdjuntado = false;
                    //         var htmlSalida = '<div class="row mb-0 mx-auto" style="width:fit-content;">';
                    //         var estado = '';

                    //         //  Enlace de descarga
                    //         if(row.idficherorequerimiento != null)
                    //         {
                    //             ficheroAdjuntado = true;
                    //             //  Tiene fichero ya subido
                    //             htmlSalida += `<div class="col text-center align-self-center p-0"><a href="${config.baseURL}public/storage/${row.storageficherorequerimiento}" class="btnDescargarFichero" data-idfichero="${row.idficherorequerimiento}" target="_blank" download="${row.nombreficherorequerimiento}" ><i class="bi bi-cloud-arrow-down" style="font-size:24px;"></i></a></div>`;
                    //         }

                    //         //  Validamos que solo el admin de fincas o el sudo pueda subir el fichero
                    //         // if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') && row.idficherorequerimiento == null)
                    //         if((core.Security.getRole() == 'SUDO' || core.Security.getRole() == 'ADMINFINCAS') || core.Security.getRole() == 'TECNICOCAE')
                    //         {
                    //             htmlSalida += `<div class="col text-center align-self-center p-0"><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento ml-2" data-toggle="tooltip" data-idcomunidad="${row.idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a></div>`;
                    //         }

                    //         //  Historial
                    //         if(row.historico === true){
                    //             htmlSalida += `
                    //             <div class="col text-center align-self-center p-0">
                    //                 <a href="javascript:void(0)" class="btnVerHistorial ml-2" data-toggle="tooltip" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="comunidadrequerimiento" title="Ver Histórico">
                    //                     <i class="bi bi-clock-history text-danger" style="font-size: 18px;"></i>
                    //                 </a>
                    //             </div>`;
                    //         }

                    //         return htmlSalida + `</div>`; // row.requerimiento;

                    //     }, 
                    // "Fichero", null, 'text-center', '20%');

                    $('#listadoDocumentosPendientesCAE').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentosPendientesCAE", "requerimiento", `documental/requerimientos/cae/comunidades`, false, true, true, null, true, true, 'nombre', false,'GET', true, true ).then ( () =>{
                        //window['tablelistadoDocumentosPendientesCAE'].table(0).columns(0).data = 'oscar';
                        //window['tablelistadoDocumentosPendientesCAE'].table(0).columns(1).visible(false);                     
                    });
                    
                    $('#listadoDocumentosPendientesCAE').show();

            }   
        }

    }

}

$(()=>{
    TecnicoCAE.Init();
});