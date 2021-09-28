
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

        if($('.titulo-modulo').length)
        {
            CoreUI.Utils.setTituloPantalla('Empleado','nombre');
        }
    },

    events: async function()
    {   

        // $('body').on(core.helper.clickEventType, '.btnSaveData', (evt)=>{
        //     evt.stopImmediatePropagation();
        //     // empleadoCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        // });

        $('body').on(core.helper.clickEventType, '.btnEliminarEmpleado', (evt)=>{
            evt.stopImmediatePropagation();
            empleadoCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
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
            CoreUI.tableData.addColumn("nombre","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn("numerodocumento", "DNI/NIE", null, 'text-left');

            //  Dirección
            CoreUI.tableData.addColumn("direccion", "Dirección", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn("email", "EMAIL", null, 'text-left');

            //  Teléfono
            CoreUI.tableData.addColumn("telefono", "TELEFONO", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn("localidad", "Localidad", null, 'text-left');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html);

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}empleado/data:id$" class="btnEditarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpleado d-inline-block" data-id="data:id$" data-nombre="data:nombre$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

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
                CoreUI.tableData.addColumn("razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn("puesto", "Puesto", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn("fechaalta", "Fecha alta", null, 'text-center', '80px');

            //  Fecha de baja
                CoreUI.tableData.addColumn("fechabaja", "Fecha baja", null, 'text-center', '80px');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn(null, "Estado", html, 'text-center', '90px');

            CoreUI.tableData.render("listadoEmpresasEmpleado", "Empresasempleado", `empleado/${idempleado}/empresas`, false, false, false);
        }
    },


}

$(()=>{
    empleadoCore.init();
});