
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
            //  Comprobamos si tiene spa asignado y si está editando o añadiendo
            if(core.actionModel == 'get')
            {
                //  Comprobamos si tiene spa asignado
                    if(core.Modelo.entity.Comunidad[0].idspa == '-1' || core.Modelo.entity.Comunidad[0].idspa == null)
                    {
                        //  Avisamos de que debe asignar el spa
                            CoreUI.Modal.Error('Para dar de alta un empleado debe asignar primero un SPA a la comunidad', 'Comunidad sin SPA asignado', function()
                            {
                                Swal.close();
                            });
                    }else{
                        //  Llamamos al modal de nuevo empleado
                            empleadoCore.mostrarModalNuevoEmpleado();                        
                    }

            }else{
                CoreUI.Modal.Info('Para dar de alta un empleado en esta comunidad, primero debe guardarla', 'Alta de nuevo empleado');
            }
        });

    },

    mostrarModalNuevoEmpleado: async function()
    {
            apiFincatech.getView("empleado", "form").then((resultHTML)=>{

                // result = CoreUI.Utils.parse(resultHTML, core.modelId);
                //  resultHTML;
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

                            $('.swal2-container .btnSaveData').removeClass('btnSaveData');


                    },
                    preConfirm: function(e)
                    {
                        if(core.Forms.Validate('formEmpleadoComunidad'))
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
                            core.Forms.mapDataToSave('formEmpleadoComunidad');
                            core.Modelo.Insert('empleado', core.Forms.data, false).then( ()=>{
                                //  Recargamos el listado de empleados de la comunidad
                                    window['listadoEmpleadosComunidad'].ajax.reload();
                            });
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

            CoreUI.tableData.addColumnRow('listadoEmpleado', 'documentacionprl');

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
        if($('#listadoEmpleadosComunidad').length &&
            typeof idcomunidad !== 'undefined' &&
            idcomunidad != '' && idcomunidad != null)
        {

                CoreUI.tableData.init();

                CoreUI.tableData.addColumnRow('listadoEmpleadosComunidad', 'documentacionprl');

            //  Tipo
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', function(row, type, val, meta){

                    var icono = '<i class="bi bi-shop pr-2"></i>';
                    var clase = '';
                    if(row.tipoempleado == 'Comunidad')
                    {
                        icono = '<i class="bi bi-building pr-2"></i>';
                        clase = 'text-info';

                    }

                    return `<span class="text-uppercase ${clase}">${icono} <span style="font-size: 12px;">${row.tipoempleado}</span></span>`;

                } , "Contratación", null, 'text-center', '100px');

            //  Empresa
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "razonsocial", "Empresa / Comunidad", null, 'text-left');

            //  Nombre
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "nombre", "Nombre y apellidos", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "email", "Correo electrónico", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "fechaalta", "Fecha alta", null, 'text-center', '80px');

            //  Fecha de baja
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', "fechabaja", "Fecha baja", null, 'text-center', '80px');

            //  Documentación completada
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', function(row, type, val, meta){

                    //  Contamos el número de documentos frente al número de documentos adjuntados
                    var iDoc = 0;
                    var iDocAdjuntos = 0;

                    for(i = 0; i < row.documentacionprl.length; i++)
                    {
                        iDoc++;
                        if(row.documentacionprl[i].idficherorequerimiento != null)
                            iDocAdjuntos++;
                    }

                    var icono = '<i class="bi bi-x-circle text-danger" style="font-size:24px;"></i>';

                    if(iDocAdjuntos == iDoc)
                        icono = '<i class="bi bi-check2-square text-success" style="font-size:24px;"></i>';


                    return `<p class="m-0 text-center">${icono}</p>`;

                } , "DOC. ADJUNTADA", null, 'text-center');
            //  Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', null, "Estado", html, 'text-center', '80px');

            //  Acciones
                CoreUI.tableData.addColumn('listadoEmpleadosComunidad', function(row, type, val, meta)
                {
                    var salida =  '';

                    if(row.tipoempleado == 'Comunidad')
                    {
                        salida = `<ul class="nav justify-content-center accionesTabla">
                                        <li class="nav-item">
                                            <a href="${baseURL}empleado/${row.idempleado}" class="btnEditarEmpleado d-inline-block" data-id="${row.idempleado}" data-nombre="${row.nombre}"><i data-feather="edit" class="text-success img-fluid mr-2"></i></a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="btnEliminarEmpleado d-inline-block" data-id="${row.idempleado}" data-nombre="${row.nombre}"><i data-feather="trash-2" class="text-danger img-fluid"></i></a>
                                        </li>
                                </ul>`;
                    }

                    return salida;

                } , "&nbsp;", null, 'text-center', '70px');

                $('#listadoEmpleadosComunidad').addClass('no-clicable');
                CoreUI.tableData.render("listadoEmpleadosComunidad", "Empleado", `comunidad/${idcomunidad}/empleados`, false, false, false);
        }    
    
    }

}

$(()=>{
    empleadoCore.init();
});