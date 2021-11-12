// const moment = require("moment");

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

    events: function()
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
            console.log(window['tablelistadoEmpresaComunidad'].row( $(this).attr('id')).data());
            var idEmpresa = window['tablelistadoEmpresaComunidad'].row( $(this).attr('id')).data().idusuario;
            var nombre = window['tablelistadoEmpresaComunidad'].row( $(this).attr('id')).data().razonsocial;

                $('.tituloEmpresasComunidad').text(nombre);
            //  Cargamos el listado de empleados para la comunidad y empresa seleccionada
                empresaCore.renderTablaRequerimientosEmpresa( idEmpresa ).then( ()=>{
                    $('.wrapperDocumentacionBasica').hide();
                    $('.wrapperEmpleadosEmpresaComunidad').show();
                });

                empleadoCore.renderTablaEmpleadosEmpresaComunidad(idEmpresa, core.modelId);

        })

        $('body').on(core.helper.clickEventType, '.btnCerrarEmpleadosComunidad', function(e)
        {
            $('.wrapperEmpleadosEmpresaComunidad').hide();
            $('.wrapperEmpresasComunidad').show();
            $('.wrapperDocumentacionBasica').show();
            $('.tituloEmpresasComunidad').text('Empresas externas');

        });

        $('body').on(core.helper.clickEventType, '.enlaceCae', function(e)
        {
            //  Cargamos las empresas asociadas a la comunidad en pantalla
                documentalCore.Comunidad.renderTablaDocumentacionComunidadCAE(core.modelId);                
                empresaCore.renderTablaEmpresasComunidad(core.modelId);
                $('.tituloEmpresasComunidad').text('Empresas externas');
        });

        //  Búsqueda de empresa 
        $('body').on(core.helper.clickEventType, '.btnBuscarEmpresaCAE', function(ev)
        {

            $('.wrapperBusquedaEmpresa .mensaje').hide();
            $('.wrapperInfoEmpresa').hide();

            //  Validamos que haya proporcionado un e-mail
            if( !core.helper.validarEmail($('#searchEmpresa').val()))
            {
                $('.wrapperBusquedaEmpresa .mensaje').addClass('text-danger');
                $('.wrapperBusquedaEmpresa .mensaje').text('El e-mail proporcionado no es válido');
                $('.wrapperBusquedaEmpresa .mensaje').show();
                return;
            }

            //  Buscamos el e-mail en la tabla de empresas
                var resultadoBusqueda = empresaCore.buscarEmailEmpresa( $('#searchEmpresa').val() );
                if( resultadoBusqueda === false )
                {
                    //CoreUI.Modal.Error('No existe ninguna empresa asociada al e-mail proporcionado', 'Empresas');
                    if( !$('.wrapperBusquedaEmpresa .mensaje').hasClass('text-danger'))
                    {
                        $('.wrapperBusquedaEmpresa .mensaje').addClass('text-danger');
                    }
                    $('.wrapperBusquedaEmpresa .mensaje').text('No existe ninguna empresa asociada al e-mail proporcionado');
                    $('.wrapperBusquedaEmpresa .mensaje').show();
                    $('.bntCrearNuevaEmpresaCAE').show();
                }else{
                    $('.bntCrearNuevaEmpresaCAE').hide();

                    //  Cargamos los datos
                    var infoEmpresa = window['tablelistadoSimpleEmpresas'].row(resultadoBusqueda).data();
                    $('.wrapperInfoEmpresa .nombreEmpresa').text(infoEmpresa.razonsocial);
                    $('.wrapperInfoEmpresa .cifEmpresa').text(infoEmpresa.cif);
                    $('.wrapperInfoEmpresa .emailEmpresa').text(infoEmpresa.email);
                    $('.wrapperInfoEmpresa .btnConfirmarEmpresaCAE').attr('data-id', infoEmpresa.id);
                    $('.wrapperInfoEmpresa .btnConfirmarEmpresaCAE').attr('data-nombre', infoEmpresa.razonsocial);
                    //  Mostramos la información
                    $('.wrapperInfoEmpresa').show();
                   
                }
            //  Si existe mostramos los datos

            //  Si no existe, mostramos un alert informando de la situación
        });

    },

    buscarEmailEmpresa: function(emailEmpresa)
    {
        var resultado = false;

        var nFilas = $('#listadoSimpleEmpresas tbody tr').length;
        if(nFilas > 0)
        {
            for(var x = 0; x < nFilas; x++)
            {
                var infoEmpresa = window['tablelistadoSimpleEmpresas'].row(x).data();
                if(infoEmpresa.email == emailEmpresa)
                {
                    resultado = x;
                }
            }
        }
        return resultado;

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

    renderTablaEmpleados: async function(idempresa, tabla = 'listadoEmpleadosEmpresa')
    {
    
        var tablaDestino = `#${tabla}`;

        if( $(tablaDestino).length)
        {
            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');
            CoreUI.tableData.init();

            //  Nombre y apellidos
            CoreUI.tableData.addColumn( tabla , "nombre","Nombre", null, 'text-left');

            //  CIF
            CoreUI.tableData.addColumn( tabla , "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Empresa
                CoreUI.tableData.addColumn( tabla , "razonsocial", "Empresa", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn( tabla , "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn( tabla , "email", "EMAIL", null, 'text-left');

            //  Teléfono
                CoreUI.tableData.addColumn( tabla , "telefono", "TELEFONO", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn( tabla , "fechaalta", "Fecha alta", null, 'text-left');

            //  Fecha de baja
                CoreUI.tableData.addColumn( tabla , "fechabaja", "Fecha baja", null, 'text-left');

            // Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn( tabla , null, "Estado", html);

            CoreUI.tableData.render( tabla , "Empleados", `empresa/${idempresa}/empleados`, false, false, false);
        }

    },

    /**
     * Carga el listado de empleados que tiene un contratista
     * @param {*} idempresa 
     * @param {*} tabla 
     */
    renderTablaEmpleadosContratista: async function(idempresa, tabla = 'listadoEmpleadosEmpresa')
    {
    
        var tablaDestino = `#${tabla}`;

        if( $(tablaDestino).length)
        {
            // //  Fecha de creación
            //     var html = 'data:created$';
            //     CoreUI.tableData.addColumn(null, "Fecha", html, 'text-center');
                CoreUI.tableData.init();

                CoreUI.tableData.addColumn( tabla, function(row, type, val, meta){
                    var salida = `<a href="/contratista/empleado?id=${row.idempleado}"><i class="bi bi-pencil-square"></i> ${row.nombre}</a>`
                    return salida;
                },'Nombre del empleado', null, 'text-left', '30%');

            // //  Nombre y apellidos
            //     CoreUI.tableData.addColumn( tabla , "nombre","Nombre", null, 'text-left');

            //  CIF
                CoreUI.tableData.addColumn( tabla , "numerodocumento", "DNI/NIE", null, 'text-left');

            //  Puesto
                CoreUI.tableData.addColumn( tabla , "puesto", "Puesto", null, 'text-left');

            //  Email
                CoreUI.tableData.addColumn( tabla , "email", "EMAIL", null, 'text-left');

            //  Teléfono
                CoreUI.tableData.addColumn( tabla , "telefono", "TELEFONO", null, 'text-left');

            //  Fecha de alta
                CoreUI.tableData.addColumn( tabla , "fechaalta", "Fecha alta", null, 'text-left');

            //  Fecha de baja
                CoreUI.tableData.addColumn( tabla , "fechabaja", "Fecha baja", null, 'text-left');

            //  Estado
                var html = 'data:estado$';
                CoreUI.tableData.addColumn( tabla , null, "Estado", html);

            //  Eliminar
                CoreUI.tableData.addColumn( tabla, function(row, type, val, meta)
                {
                    var html = `<a href="javascript:void(0);" class="btnEliminarEmpleado d-inline-block" data-id="${row.idempleado}" data-nombre="${row.nombre}"><i data-feather="trash-2" class="text-danger img-fluid"></i></a>`;
                    return html;
                }, '&nbsp;',null, 'text-center', '20px');

            $(tablaDestino).addClass('no-clicable');
            CoreUI.tableData.render( tabla , "Empleados", `empresa/${idempresa}/empleados`, false, false, false);
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

    renderTablaRequerimientosEmpresa: async function(idEmpresa)
    {
        if($('#listadoDocumentacionEmpresa').length)
        {

                CoreUI.tableData.init();
                CoreUI.tableData.columns = [];

            //  Nombre del documento
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', "requerimiento","Requerimiento", null, 'text-left');

            //  Fecha última actuación
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
                {
                    if(row.fechaultimaactuacion == '' || row.fechaultimaactuacion == 'null' || !row.fechaultimaactuacion)
                    {
                        return 'No se ha realizado ninguna actuación';
                    }else{
                        return moment(row.fechaultimaactuacion).locale('es').format('L')
                    }
                }, "Fecha última actuación", null, 'text-justify');

            //  Estado
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
                {
                    return (!row.idficherorequerimiento ? '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2">No adjuntado</span>' : '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2">Disponible para descargar</span>') + '</td>';
                }, "Fichero", null, 'text-left');

            //  Columna de acciones
                CoreUI.tableData.addColumn('listadoDocumentacionEmpresa', function(row, type, val, meta)
                {
                    var canUploadFile = false;
                    var salida = '';

                    if(core.Security.getRole() == 'CONTRATISTA')
                        canUploadFile = true;
    
                //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                    ficheroAdjuntado = (!row.idficherorequerimiento ? false : true);

                    if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                    {
                        var enlaceDescarga = baseURL + '/public/storage/' + row.storageficherorequerimiento;
                        salida += ` <td class="text-center">
                                        <a href="${enlaceDescarga}" target="_blank" title="Ver documento">
                                            <i class="bi bi-cloud-arrow-down text-primary mr-1" style="font-size: 30px;"></i>
                                        </a>
                                    </td>`;
                    }


                    var _idempresa = (row.idempresa == null ? idEmpresa : row.idempresa);

                //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                    if(canUploadFile)
                    // if(!ficheroAdjuntado && canUploadFile)
                    {  //  SUBIR FICHERO SOLO PARA CONTRATISTA (EMPRESA)
                        dataset = ` data-idcomunidad="${row.idcomunidad}" data-idempresa="${_idempresa}" data-idempleado="" data-idrequerimiento="${row.idrequerimiento}" data-idrelacionrequerimiento="${row.idrelacion}" data-entidad="empresa" `;
                        salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 30px;"></i></a></td>`;
                    }

                    if(!ficheroAdjuntado && !canUploadFile)
                    {
                        salida += '<td>&nbsp;</td>';
                    }
                    return salida;

                }, '&nbsp;', null, 'text-center');

                $('#listadoDocumentacionEmpresa').addClass('no-clicable');

                //  Si ya está inicializada destruimos para volver a generarla¿?
                //  FIXME: Cambiar url ajax simplemente
                if(typeof window['tablelistadoDocumentacionEmpresa'] !== 'undefined' )
                {
                    window['tablelistadoDocumentacionEmpresa'].destroy();
                }


                CoreUI.tableData.render("listadoDocumentacionEmpresa", "documentacioncae", `empresa/${idEmpresa}/documentacion`, null, false, false);

        }  
    },

}

$(()=>{
    empresaCore.init();
});