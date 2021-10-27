let rgpdCore = {

    Init: function()
    {
        rgpdCore.Events();
    },

    Events: function()
    {
        $('body').on(core.helper.clickEventType, '.enlaceRGPD', function(e)
        {
            $('.loading').show();
            //  Documentación Cámaras de seguridad
                documentalCore.RGPD.cargarDocumentacionCamarasSeguridad().then( 
            //  Cámaras de seguridad de la comunidad
                    // requerimientoCore.renderTablaCamarasSeguridad(core.modelId)
                ).then( 
            //  Tabla documentación básica
                    // documentalCore.Comunidad.renderTablaDocumentacionComunidad(core.modelId)
                ).then( 
            //  Consultas al DPD
                    // dpdCore.renderTabla()
                ).then( 
            //  Informes de valoración y seguimiento
                    // informeValoracionSeguimientoCore.renderTabla()
                ).then( 
            //  Notas informativas
                    //  notasInformativasCore.renderTabla()
                ).then( 
            //  Contratos de cesión de datos a terceros
                    // requerimientoCore.renderTablaDocumentacionContratosCesion()
                ).then(
                    //  requerimientoCore.renderTablaContratosCesion(core.modelId)
                ).then ( 
                     documentalCore.renderTablaDocumentacionBasica()
                );
            $('.loading').hide();
        });

        //  Documentación básica
        $('body').on(core.helper.clickEventType, '.enlaceRGPDDocumentacionBasica', function(evt)
        {
            documentalCore.RGPD.cargarDocumentacionCamarasSeguridad();
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
        });        
    }

}

$( () =>
{
    rgpdCore.Init();
});