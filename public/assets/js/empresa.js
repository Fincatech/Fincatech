
let empresaCore = {

    Empresa: Object(),
    empresa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoEmpresa').length)
        {
            empresaCore.renderTabla();
        }else{
     
        }

        //  Comprobamos si hay que renderizar el listado de empleados por empresa
        empresaCore.renderTablaEmpleados(core.modelId);

        //  Título del módulo
            if($('.titulo-modulo').length && core.model == 'Empresa')
                CoreUI.setTitulo('razonsocial');

    },

    events: async function()
    {   
        $('body').on(core.helper.clickEventType, '.btnEliminarEmpresa', (evt)=>{
            evt.stopImmediatePropagation();
            empresaCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.bntCrearNuevaEmpresaCAE', function(evt)
        {
            empresaCore.mostrarModalNuevaEmpresaCAE();
        });

        $('body').on(core.helper.clickEventType, '#listadoEmpresaComunidad tr', function(e)
        {
            e.stopImmediatePropagation();
            $('.wrapperEmpresasComunidad').hide();
            
            //  Cargamos el listado de empleados para la comunidad y empresa seleccionada
                $('.wrapperEmpleadosEmpresaComunidad').css('opacity','1');
        })

        $('body').on(core.helper.clickEventType, '.btnCerrarEmpleadosComunidad', function(e)
        {
            $('.wrapperEmpleadosEmpresaComunidad').css('opacity','0');
            $('.wrapperEmpresasComunidad').show();
        });

    },

    /**
     * Muestra el modal de crear nueva empresa desde el CAE
     */
    mostrarModalNuevaEmpresaCAE: async function()
    {

        apiFincatech.getView("empresa", "form").then((resultHTML)=>{

                Swal.fire({
                    text: "",
                    html: resultHTML,
                    grow:'false',
                    width: '120rem',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="bi bi-save mr-2"></i> Guardar',
                    cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        actions: 'text-center p-3 shadow-inset rounded-pill border-light border-2 bg-white',
                        confirmButton: 'btn btnSaveData btn-success shadow d-block pb-2 pt-2 confirmButtonModal',
                        cancelButton: ' btn btn-danger btnCancelSave shadow d-block pb-2 pt-2 mr-3 cancelButtonModal',
                        popup: 'bg-transparent'
                    },                    
                    didOpen: function(e)
                    {

                        //  Cargamos los combos 
                            core.Forms.getSelectData( 'Empresatipo', 'Empresatipo.nombre', 'Empresatipo.id', '', '', 'tipoEmpresaComunidad', true ).then(()=>{
                                $('body .formEmpresaComunidad #tipoEmpresaComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                            });  

                            core.Forms.getSelectData( 'Provincia', 'Provincia.Nombre', 'Provincia.Id', '', '', 'provinciaEmpresaComunidad', true ).then(()=>{
                                $('body .formEmpresaComunidad #provinciaEmpresaComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                            }); 

                            $('.swal2-container .btnSaveData').removeClass('btnSaveData');


                    },
                    preConfirm: function(e)
                    {
                        if(core.Forms.Validate('formEmpresaComunidad'))
                        {
                            return true;
                        }else{
                            //  Mostramos el mensaje de error correspondiente
                            Swal.showValidationMessage('Debe completar todos los campos marcados como obligatorios');
                            return false;
                        }               
                    }                 
                  }).then((result) => {
                    if (result.isConfirmed) 
                    {
                            core.Forms.mapFormDataToSave('formEmpresaComunidad');
                            core.Modelo.Insert('empresa', core.Forms.data, false).then( ()=>{
                                //  Recargamos el listado de empresas de la comunidad
                                    window['tablelistadoEmpresaComunidad'].ajax.reload();
                            }).catch(function(e){
                                return false;
                            });
                    }
                }); 
            }); 
    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("empresa", id, nombre, "listadoEmpresa");
        Swal.fire({
            title:`¿Desea eliminar la empresa:<br>${nombre}?`,
            text: "Se va a eliminar la empresa y toda la información asociada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                //  Llamamos al endpoint de eliminar
                apiFincatech.delete("empresa", id).then((result) =>{
                    Swal.fire(
                        'Empresas eliminada correctamente',
                        '',
                        'success'
                      );
                      $('#listadoEmpresa').DataTable().ajax.reload();
                });
            }
        });
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
    
        if($('#listadoEmpresa').length)
        {
            CoreUI.tableData.init();

            CoreUI.tableData.addColumnRow('listadoEmpresa', 'documentacioncae');

            //  Razón social
            CoreUI.tableData.addColumn('listadoEmpresa', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpresa', "cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresa', "email", "EMAIL", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresa', "telefono", "TELEFONO", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresa', "personacontacto", "Persona de contacto", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn('listadoEmpresa', "localidad", "Localidad", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresa', "empresatipo[0].nombre", "Tipo", null, 'text-left');

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}empresa/data:id$" class="btnEditarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoEmpresa', null, "", html);

                $('#listadoEmpresa').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpresa", "Empresa", "empresa/list");
        }
    },

    renderTablaSimple: async function()
    {
        if($('#listadoSimpleEmpresas').length)
        {
            if(typeof window['tablelistadoSimpleEmpresas'] != 'undefined')
            {
                // window['tablelistadoSimpleEmpresas'].destroy();
                CoreUI.tableData.columns['listadoSimpleEmpresas'] = [];

            }
            CoreUI.tableData.init();


            //  Razón social
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "email", "EMAIL", null, 'text-left');

            //  Tipo de empresa
            CoreUI.tableData.addColumn('listadoSimpleEmpresas', "empresatipo[0].nombre", "Tipo", null, 'text-left');


            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item">
                                <a href="javascript:void(0);" class="btnConfirmarEmpresaCAE d-inline-block btn btn-sm btn-success" data-id="data:id$" data-nombre="data:razonsocial$">ASIGNAR</a>
                            </li>
                            </ul>`;
                CoreUI.tableData.addColumn('listadoSimpleEmpresas', null, "", html);

                $('#listadoSimpleEmpresas').addClass('no-clicable');
                CoreUI.tableData.render("listadoSimpleEmpresas", "Empresa", "empresa/list");
        }  
    },

    renderTablaEmpleados: async function(idempresa)
    {
    
        if($('#listadoEmpleadosEmpresa').length)
        {
            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');
            CoreUI.tableData.init();

            //  Nombre y apellidos
            CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "nombre","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Empresa
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "email", "EMAIL", null, 'text-left');

            //  Teléfono
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "telefono", "TELEFONO", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "fechaalta", "Fecha alta", null, 'text-left');

            //  Fecha de baja
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', "fechabaja", "Fecha baja", null, 'text-left');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoEmpleadosEmpresa', null, "Estado", html);

            //  Fecha de alta
                // var html = 'data:created$';
                // CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');

            CoreUI.tableData.render("listadoEmpleadosEmpresa", "Empleados", `empresa/${idempresa}/empleados`, false, false, false);
        }

    },

    renderTablaEmpresasComunidad: async function(idcomunidad)
    {
        if(idcomunidad == '' || typeof idcomunidad === 'undefined' || idcomunidad == null )
            return;

        if($('#listadoEmpresaComunidad').length)
        {

            CoreUI.tableData.init();

            //  Añadimos la columna de desplegar
            // CoreUI.tableData.addColumnRow('listadoEmpresaComunidad', 'empresatipo');
            // CoreUI.tableData.addColumnRow('listadoEmpresaComunidad', 'documentacioncae');

            //  Razón social
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "email", "EMAIL", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "telefono", "TELEFONO", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "personacontacto", "Persona de contacto", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "localidad", "Localidad", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn('listadoEmpresaComunidad', "tipoempresa", "Tipo", null, 'text-left');


            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpresaComunidad d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoEmpresaComunidad', null, "", html);

                $('#listadoEmpresaComunidad').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpresaComunidad", "empresascomunidad", `comunidad/${idcomunidad}/empresas`, null, false, false);
        }  
    },

}

$(()=>{
    empresaCore.init();
});