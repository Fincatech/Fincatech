
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
            // core.Files.init();
            // core.Files.Fichero.entidadId = core.modelId;        
        }

    },

    events: async function()
    {   
        $('body').on(core.helper.clickEventType, '.btnEliminarEmpresa', (evt)=>{
            evt.stopImmediatePropagation();
            empresaCore.eliminar( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });
    },

    /** Elimina una comunidad previa confirmación */
    eliminar: function(id, nombre)
    {
        core.Modelo.Delete("empresa", id, nombre, "listadoEmpresa");
        Swal.fire({
            title:`¿Desea eliminar la empressa:<br>${nombreComunidad}?`,
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

            //  Cargamos el listado de comunidades
            CoreUI.tableData.init();

            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');

            //  Razón social
            CoreUI.tableData.addColumn("razonsocial","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn("cif", "CIF", null, 'text-justify');

            //  Email
            CoreUI.tableData.addColumn("email", "EMAIL", null, 'text-left');

            //  Email
            CoreUI.tableData.addColumn("telefono", "TELEFONO", null, 'text-left');

            //  Persona de contacto
            CoreUI.tableData.addColumn("personacontacto", "Persona de contacto", null, 'text-left');

            //  Localidad
            CoreUI.tableData.addColumn("localidad", "Localidad", null, 'text-left');

            //  Columna de acciones
                var html = '<ul class="nav justify-content-center accionesTabla">';
                    html += `<li class="nav-item"><a href="${baseURL}empresa/data:id$" class="btnEditarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="edit" class="text-success img-fluid"></i></a></li>`;
                    html += '<li class="nav-item"><a href="javascript:void(0);" class="btnEliminarEmpresa d-inline-block" data-id="data:id$" data-nombre="data:razonsocial$"><i data-feather="trash-2" class="text-danger img-fluid"></i></li></ul>';
                CoreUI.tableData.addColumn(null, "", html);

                // $('#listadoEmpresa').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpresa", "Empresa", "empresa/list");
        }
    }


}

$(()=>{
    empresaCore.init();
});