let TecnicoCAE = {

    ListadoComunidades: 'comunidades',
    ListadoAdministradores: 'administradores',
    TipoListado: 'comunidades',

    Init: function()
    {

        if($('#listadoDocumentosPendientesCAE').length)
        {
            TecnicoCAE.Render.ListadoPendienteCAE();
        }

        TecnicoCAE.Events();

    },

    Events: function(){

        /**
         * Botón selección tipo de listado
         */
        $('body').on(core.helper.clickEventType, '.btnSeleccionPendiente', function(evt)
        {
            //  Desmarcamos los botones de selección
            $('.btnSeleccionPendiente').removeClass('active');
            //  Marcamos el botón de selección activo
            $(this).addClass('active');
            //  Recargamos el listado según la selección del usuario
            TecnicoCAE.Controller.CargarListado( $(this).attr('data-tipo') );
        });
    },

    Controller:{

        /**
         * 
         * @param {*} tipo 
         */
        CargarListado: function(tipo)
        {
            switch(tipo){
                case 'administradores':
                    $('.tipo-listado').html('Pendiente Administradores');
                    break;
                case 'comunidades':
                    $('.tipo-listado').html('Pendiente Comunidades');
                    break;
            }
            TecnicoCAE.Render.ListadoPendienteCAE(tipo);
        }

    },

    Model: {

    },

    Render:{

        ListadoPendienteCAE: function(tipo = TecnicoCAE.ListadoComunidades)
        {
            if($('#listadoDocumentosPendientesCAE').length)
            {

                //  Inicializamos la tabla
                    CoreUI.tableData.init();
                    CoreUI.tableData.columns = [];

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

                    $('#listadoDocumentosPendientesCAE').addClass('no-clicable');
                    CoreUI.tableData.render("listadoDocumentosPendientesCAE", "requerimiento", `documental/requerimientos/cae/comunidades/${tipo}`, false, true, true, null, true, true, 'nombre', false,'GET', true, true ).then ( () =>{
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