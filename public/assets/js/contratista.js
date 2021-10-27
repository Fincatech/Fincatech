
let contratista = {

    idContratista: null,

    Init: function()
    {
        
        //  Cargamos las comunidades que tengan asignado al contratista en sesión
            CoreUI.Sidebar.Comunidades.cargarMenuComunidades();
            contratista.Events();
            core.Files.init();
    },

    Events: async function()
    {

        //  Creación / Actualización de empleado
        $("body .btnSaveData").off(core.helper.clickEventType).on(core.helper.clickEventType, function(evt)
        {
            evt.stopImmediatePropagation();

            //  Validamos el formulario
            if(core.Forms.Validate('form-empleado'))
            {
                core.Forms.prepareFormDataBeforeSend('form-empleado');
                empleadoCore.Model.save();
            }else{
                CoreUI.Modal.Error('Debe rellenar la información de todos los campos marcados como obligatorios','Error Alta Empleado');
            }

        });

        //  Documentación CAE de la empresa
        if( $('body #listadoDocumentacionEmpresa').length )
        {
            await core.Security.getUserInfo().then(function()
            {
                //  Renderizamos los documentos de la empresa
                    empresaCore.renderTablaRequerimientosEmpresa(core.Security.user);

            });
        }

        //  Empleados de la empresa
        if( $('body #listadoEmpleadosContratista').length )
        {
            await core.Security.getUserInfo().then( function()
            {
                 empresaCore.renderTablaEmpleadosContratista(core.Security.user, 'listadoEmpleadosContratista');
            });
        }

        //  Formulario de empleado
        if( $('body #form-empleado').length )
        {

            core.Security.getUserInfo().then( async( result ) =>
            {

                $('body #idempresa').val(core.Security.user);

                var actionForm =  $('body #form-empleado').attr('data-action');
                    
                $('body').attr('hs-action', actionForm );
                $('body').attr('hs-model', 'empleado');
                
                core.model = 'empleado';

                //  Inicializamos los select
                    core.Forms.initializeSelectData();

                if(actionForm == 'get')
                {
    
                    $('body').attr('hs-model-id', $('body #form-empleado').attr('data-id'));

                    empleadoCore.Model.get( $('body #form-empleado').attr('data-id'), function()
                    {
                        core.Forms.data = empleadoCore.Model.Empleado;
                        core.modelId = empleadoCore.Model.id;
                        $( 'body' ).attr('hs-model-id', empleadoCore.Model.id );
                        core.Forms.mapDataFromModel('form-empleado', empleadoCore.Model.Empleado);
                    });

                }else{
                    $('#fechaalta').val( moment().format('YYYY-MM-DD') );
                }

            });


        }

        //  Documentación del empleado al hacer click sobre la tabla
        if( $('body #listadoEmpleadosContratista').length )
        {

            $('body').on(core.helper.clickEventType, '#listadoEmpleadosContratista tr', function(evt)
            {

                evt.stopImmediatePropagation();
                var idEmpleado = window['tablelistadoEmpleadosContratista'].row( $(this).attr('id')).data().idempleado;
                core.Files.Fichero.entidadId = idEmpleado;
                $('body .empleadoRequerimientosInfo').text('Requerimientos de ' + window['tablelistadoEmpleadosContratista'].row( $(this).attr('id')).data().nombre );
                $('body .empleadoRequerimientosInfo').show();
                $('body .wrapperDocumentacionEmpleado .mensajeInformacion').hide();
                $('body #listadoDocumentacionEmpleado').removeClass('d-none');

                empleadoCore.renderTablaDocumentacionEmpleado(idEmpleado, 'listadoDocumentacionEmpleado');

                $('body .btnAdjuntarFicheroDocumento').off();
                $('body').on(core.helper.clickEventType, '.btnAdjuntarFicheroDocumento', async function()
                {
        
                    documentalCore.idcomunidad = $(this).attr('data-idcomunidad');
                    documentalCore.idempresa = $(this).attr('data-idempresa');
                    documentalCore.idempleado = $(this).attr('data-idempleado');
                    documentalCore.idrequerimiento = $(this).attr('data-idrequerimiento');
                    documentalCore.idrelacionrequerimiento = $(this).attr('data-idrelacionrequerimiento');
                    documentalCore.entidad = $(this).attr('data-entidad');
        
                    const { value: file } = await Swal.fire({
                    title: '',
                    html: Constantes.CargaDocumento,
                    showCancelButton: false,
                    showConfirmButton: false,
                    // grow: 'row',
                    showCloseButton: true,
                    didOpen: function()
                    {
        
                        //  Inicializamos el componente de ficheros
                            core.Files.init();
                    }});
        
                });

            });

        }

    },



}

$( () =>{
    contratista.Init();    
});