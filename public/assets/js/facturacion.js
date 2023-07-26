let facturacion = {

    Init: function(){
        console.log('Facturacion core');
        core.Forms.initializeSelectData();
        facturacion.Events();
    },

    Events: function(){
        $('body').on(core.helper.clickEventType, '.btnGenerarInformePrefacturacion', function(){
            facturacion.Prefacturacion.generarInformePrefacturacion();
        });
    },

    Prefacturacion: {

        generarInformePrefacturacion: function(){

            var fechaDesde = $('.fechaDesde').val();
            var fechaHasta = $('.fechaHasta').val();
            var administradorId = $('#usuarioId option:selected').val();
            var nombreAdministrador = $('#usuarioId option:selected').text();

            if(fechaHasta != '' && fechaDesde == '')
            {
                CoreUI.Modal.Error('Proporcione la fecha desde para generar el informe');
                return;
            }

            var datos = Object();
            datos.fechaDesde = fechaDesde;
            datos.fechaHasta = fechaHasta;
            datos.nombreAdministrador = nombreAdministrador;

            //  Mandamos al endpoint la información para generar el informe
            apiFincatech.post(`facturacion/prefacturacion/${administradorId}`, datos).then( (result) =>{
                //  Recuperamos el fichero desde donde se ha generado y lanzamos la descarga
                var data = JSON.parse(result);
                console.log(data);
                window.open(data.data);
                
            });

        }

    }

}

$(() =>{
    if(core.model == 'Prefacturacion')
    {
        facturacion.Init();
    }
})