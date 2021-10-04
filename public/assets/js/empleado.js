
let empleadoCore = {

    Empresa: Object(),
    empresa: Object(),

    init: async function()
    {

        this.events();

        if($('#listadoEmpleado').length)
        {
            empleadoCore.renderTabla();
        }else{
            

            // core.Files.init();
            // core.Files.Fichero.entidadId = core.modelId;        
        }

        empleadoCore.renderTablaEmpresasEmpleado(core.modelId);

        //  Título del módulo
        if($('.titulo-modulo').length && core.model == 'Empleado')
            CoreUI.setTitulo('nombre');
    },

    events: async function()
    {   

        $('body').on(core.helper.clickEventType, '.btnEliminarEmpleado', (evt)=>{
            evt.stopImmediatePropagation();
            empleadoCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnNuevoEmpleadoComunidad', function(evt)
        {
            //  Llamamos al modal de nuevo empleado
                empleadoCore.mostrarModalNuevoEmpleado();
        });

        //  Bindeamos el botón de guardar nuevo empleado ¿?

    },

    mostrarModalNuevoEmpleado: async function()
    {
            apiFincatech.getView("empleado", "form").then((resultHTML)=>{

                // result = CoreUI.Utils.parse(resultHTML, core.modelId);
                //  resultHTML;
                Swal.fire({
                    text: "",
                    html: resultHTML,
                    grow:'row',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="bi bi-save mr-2"></i> Guardar',
                    cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btnSaveData btn-success shadow d-block pb-2 pt-2',
                        cancelButton: 'btn btn-danger btnCancelSave shadow d-block pb-2 pt-2 mr-3'
                    },                    
                    didOpen: function(e)
                    {
                        $('.formEmpleadoComunidad #idcomunidad').val(core.modelId);
                        $('.formEmpleadoComunidad .nombreComunidad').text(core.Modelo.entity.Comunidad[0].nombre);

                        //  Cargamos los combos 
                            core.Forms.getSelectData( 'Tipopuestoempleado', 'Tipopuestoempleado.nombre', 'Tipopuestoempleado.id', '', '', 'puestoEmpleadoComunidad', true ).then(()=>{
                                $('body .formEmpleadoComunidad #puestoEmpleadoComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                            });  

                            core.Forms.getSelectData( 'Provincia', 'Provincia.Nombre', 'Provincia.Id', '', '', 'provinciaEmpleadoComunidad', true ).then(()=>{
                                $('body .formEmpleadoComunidad #provinciaEmpleadoComunidad').select2({
                                    dropdownParent: $('.swal2-container'),
                                    theme: 'bootstrap4', 
                                });
                            }); 


                    }                    
                  }).then((result) => {
                    if (result.isConfirmed) {
                        core.Forms.mapDataToSave('formEmpleadoComunidad');
                        core.Modelo.Insert('Empleado', core.Forms.data, false).then( ()=>{
                            //  Recargamos el listado de empleados de la comunidad
                                window['listadoEmpleadosComunidad'].ajax.reload();
                        });
                        //  Llamamos al endpoint de eliminar
                        // apiFincatech.delete("empleado", id).then((result) =>{
                        //     Swal.fire(
                        //         'Empleado eliminado correctamente',
                        //         '',
                        //         'success'
                        //       );
                        //       $('#listadoEmpleado').DataTable().ajax.reload();
                        // });
                    }
                }); 
            });    
           

    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("empleado", id, nombre, "listadoEmpleado");
        Swal.fire({
            title:`¿Desea eliminar el empleado:<br>${nombre}?`,
            text: "Se va a eliminar el empleado y toda la información asociada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                //  Llamamos al endpoint de eliminar
                apiFincatech.delete("empleado", id).then((result) =>{
                    Swal.fire(
                        'Empleado eliminado correctamente',
                        '',
                        'success'
                      );
                      $('#listadoEmpleado').DataTable().ajax.reload();
                });
            }
        });
    },

    /**
     * Carga los datos del listado
     */
    renderTabla: async function()
    {
        if($('#listadoEmpleado').length)
        {
            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');

            //  Nombre y apellidos
            CoreUI.tableData.addColumn('listadoEmpleado', "nombre","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn('listadoEmpleado', "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Dirección
            CoreUI.tableData.addColumn('listadoEmpleado', "direccion", "Dirección", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn('listadoEmpleado', "email", "EMAIL", null, 'text-left');

            //  Teléfono
            CoreUI.tableData.addColumn('listadoEmpleado', "telefono", "TELEFONO", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn('listadoEmpleado', "localidad", "Localidad", null, 'text-left');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoEmpleado', null, "Estado", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}empleado/data:id$" class="btnEditarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn('listadoEmpleado', null, "", html);

                // $('#listadoEmpleado').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpleado", "Empleado", "empleado/list");
        }
    },

    renderTablaEmpresasEmpleado: async function(idempleado)
    {
        if($('#listadoEmpresasEmpleado').length)
        {
            CoreUI.tableData.init();

            //  Empresa
                CoreUI.tableData.addColumn('listadoEmpresasEmpleado', "razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn('listadoEmpresasEmpleado',"puesto", "Puesto", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn('listadoEmpresasEmpleado',"fechaalta", "Fecha alta", null, 'text-center', '80px');

            //  Fecha de baja
                CoreUI.tableData.addColumn('listadoEmpresasEmpleado',"fechabaja", "Fecha baja", null, 'text-center', '80px');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoEmpresasEmpleado',null, "Estado", html, 'text-center', '90px');

                CoreUI.tableData.render("listadoEmpresasEmpleado", "Empresasempleado", `empleado/${idempleado}/empresas`, false, false, false);
        }
    },

    renderTablaEmpleadosComunidad: async function(idcomunidad)
    {
        if($('#listadoEmpleadosComunidad').length)
        {

            CoreUI.tableData.init();

            //  Empresa
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "puesto", "Puesto", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "fechaalta", "Fecha alta", null, 'text-center', '80px');

            //  Fecha de baja
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "fechabaja", "Fecha baja", null, 'text-center', '80px');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', null, "Estado", html, 'text-center', '90px');

            CoreUI.tableData.render("listadoEmpleadosComunidad", "Empresasempleado", `empleado/1/empresas`, false, false, false);
        }    
    
    }

}

$(()=>{
    empleadoCore.init();
});