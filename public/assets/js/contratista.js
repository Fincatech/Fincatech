
let contratista = {

    idContratista: null,
    idEmpleadoSeleccionado: null,

    Constantes: {

        AsignarEmpleado: `
            <div class="row">

                <div class="col-12 text-center text-uppercase align-self-center">
                    <p class="m-0" style="display: block; font-size: 18px;"> Asignar empleado a la comunidad</p>
                </div>

            </div>

            <div class="form-group row mb-2 justify-content-center wrapperSeleccionarEmpleado">

                <div class="col-12">  

                    <div class="row mt-5">
                        <div class="col-12">
                            <p class="text-left">Empleados disponibles:</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <select class="form-select listEmpleados" aria-label="Default select">
                            </select>
                        </div>
                    </div>
                    
                    <!-- boton asignar -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="javascript:void(0);" class="btn d-block btn-success btnAsignarEmpleado pt-3 pb-3" data-idempleado="" data-idempresa="">Asignar empleado a comunidad</a>
                        </div>
                    </div>

                </div>

            </div>`,
    },

    Init: function()
    {
        //  Cargamos las comunidades que tengan asignado al contratista en sesión
            CoreUI.Sidebar.Comunidades.cargarMenuComunidades();
            contratista.Events();
            core.Files.init();
            contratista.checkRGPD();
    },

    Events: async function()
    {

        comunidadesCore.events();

        //  Creación / Actualización de empleado
        $("body .form-empleado .btnSaveData").off(core.helper.clickEventType).on(core.helper.clickEventType, function(evt)
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
        if( $('body #wrapperListadoDocumentacionCAEEmpresa').length )
        {
            await core.Security.getUserInfo().then(function()
            {
                //  Renderizamos los documentos de la empresa
                contratista.Model.ListarDocumentosEmpresa();
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

        //  Carga de empleados de la comunidad
        $('body').on(core.helper.clickEventType, '.enlaceEmpleadosComunidadContratista', function(ev)
        {
            var idComunidad = $('body').attr('hs-model-id');
            var idEmpresa = core.Security.user;
            empleadoCore.renderTablaEmpleadosEmpresaComunidad(idEmpresa, idComunidad);
        });

        $('body').on(core.helper.clickEventType, '.btnModalAsignarEmpleado', function(ev)
        {
            contratista.mostrarModalAsignarEmpleadoComunidad();
        });     

        /** Asignación de empleado a empresa y comunidad */
        $('body').on(core.helper.clickEventType, '.btnAsignarEmpleado', function(ev)
        {

            var idEmpleado = $('body .listEmpleados option:selected').val();
            var idComunidad = $('body').attr('hs-model-id');
            empleadoCore.asignarEmpleadoComunidad(idComunidad, idEmpleado);
        });

        //  Aceptación RGPD
        $('body').on(core.helper.clickEventType, '.btnAceptarRGPD', function(ev)
        {
            contratista.aceptacionRGPD($(this).attr('data-id'), '1');
        });
        
        //  Rechazo RGPD
        $('body').on(core.helper.clickEventType, '.btnRechazarRGPD', function(ev)
        {
            contratista.aceptacionRGPD($(this).attr('data-id'), '0');
        });

        //  Adjuntar operatoria entre comunidad y empresa externa
        $('body').on(core.helper.clickEventType, '.btnAdjuntarOperatoria', async function()
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
            showCloseButton: true,
            didOpen: function()
            {

                //  Inicializamos el componente de ficheros
                    core.Files.init();
            }});

        });        

        //  Desasignar empleado. Solo contratistas
        if(core.Security.getRole() === 'CONTRATISTA')
        {
            $('body').on(core.helper.clickEventType, '.btnDesasignarEmpleado', function(evt)
            {
                evt.stopImmediatePropagation();
                let idEmpleado = $(this).attr('data-id');
                let idComunidad = $(this).attr('data-idcomunidad');
                let nombreEmpleado = $(this).attr('data-nombre');
                empleadoCore.desasignarEmpleadoComunidad(idComunidad, idEmpleado, core.Modelo.entity.Comunidad[0].nombre, nombreEmpleado);
            });
        }

    },

    checkRGPD: function()
    {

        //  Comprobamos si tiene aceptada la RGPD
        if( $('.mensajeRGPD').length > 0)
        {
            core.Security.getUserInfo().then( (result) =>
            {
                apiFincatech.get(`user/${core.Security.user}/rgpd`).then( result =>
                {
                    if((JSON.parse(result).data['rgpd']) == '1')
                    {
                        $('.mensajeRGPD').hide();
                    }else{
                        $('.mensajeRGPD').show();
                    }
                });
            });       
        }
    },

    //  Actualiza el status relativo a la aceptación de la RGPD de un contratista
    aceptacionRGPD: function( idUsuario, valor )
    {
        var data = Object();
        data.rgpd = valor;
        apiFincatech.put(`usuario/${idUsuario}`, data).then( result =>
        {
            if(valor == '0')
            {
                CoreUI.Modal.Error('Ha rechazado el consentimiento de la aceptación de la RGPD', 'Aceptación RGPD');
            }else{
                CoreUI.Modal.Success('Ha aceptado el consentimiento relativo a la RGPD', 'Aceptación RGPD');
            }
            contratista.checkRGPD();
        });
    },

    mostrarModalAsignarEmpleadoComunidad: function()
    {
        const { value: file } = Swal.fire({
            title: '',
            html: contratista.Constantes.AsignarEmpleado,
            showCancelButton: false,
            showConfirmButton: false,
            width: 800,
            grow: 'row',
            showCloseButton: true,
            didOpen: function(e)
            {
    
                //  Iniciamos la tabla de empresas simple
                //    empresaCore.renderTablaSimple();
                contratista.listarEmpleadosEmpresa();
            }});         
    },

    listarEmpleadosEmpresa: function()
    {
        apiFincatech.get('empresa/' + core.Security.user + '/empleados').then( (result) =>
        {
            var datos = JSON.parse(result);
            var empleados = datos.data['Empleados'];

            for(var x = 0; x < empleados.length; x++)
            {
                $('body .listEmpleados').append(`<option value="${empleados[x].idempleado}">${empleados[x].nombre}</option>`)
            }

        });
    },

    Model: {
        ListarDocumentosEmpresa: function()
        {
            documentalCore.Listado.Cargar('wrapperListadoDocumentacionCAEEmpresa', documentalCore.ENTIDAD_EMPRESA, documentalCore.CAE_EMPRESA, null, core.Security.user, null);
            empresaCore.renderTablaRequerimientosEmpresa(core.Security.user);       
        }
    },

}

$( () =>{
    contratista.Init();    
});