// const { ajaxPrefilter } = require("jquery");

let CoreUI = {

    tituloModulo: null,

    init: function(){

    },

    setTitulo: function(value)
    {
        CoreUI.tituloModulo = value;
    },

    Utils: {

        parse: function(valor, data)
        {
            let inicioPatron = "data[";
            let finalPatron = "]";
            var posInicial = 0;
            
            posInicial = valor.indexOf(inicioPatron);

            while(posInicial >= 0)
            {
                if(posInicial >= 0)
                {
                    //  Encontramos el cierre
                    posFinal = valor.indexOf(finalPatron, posInicial + 1);

                    if(posFinal > 0)
                    {
                        campo = valor.substring(posInicial + inicioPatron.length, posFinal);
                        var find = inicioPatron + campo + finalPatron;
                        var re = new RegExp(find, 'g');
                        //valor = valor.replace(re, data[`${campo}`]);
                        valor = valor.replace(find, data[`${campo}`]);
                       // console.log(valor);
                    }
                }
                posInicial = valor.indexOf(inicioPatron, posInicial + 1);
            }
            //console.log(valor);
            return valor;
        },

        setTituloPantalla: function(entidad, campo, nombre = null)
        {
            //  Lo primero de todo es comprobar si existe la entidad en el modelo
                if(nombre !== null)
                {
                    $('.titulo-modulo').text(nombre);
                    return;
                }

                if(core.Modelo.entity[entidad] !== undefined && core.Modelo.entity[entidad] !== null)
                {
                    $('.titulo-modulo').text(core.Modelo.entity[entidad][0][campo]);
                }
        }

    },

    tableData: {

        _this: null,
        columns: [],
        selectedRowIndex: null,
        selectedRowTableId: null,

        /** Inicializador del componente de tabla enlazada a datos */
        init: function()
        {
            //  Inicialización de idioma
            jQuery.extend( true, jQuery.fn.dataTable.defaults, {
                "language": {
                    "decimal": ",",
                    "thousands": ".",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoPostFix": "",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "loadingRecords": "Cargando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "searching": true,
                    "searchPlaceholder": "",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "aria": {
                        "sortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    //only works for built-in buttons, not for custom buttons
                    "buttons": {
                        "create": "Nuevo",
                        "edit": "Cambiar",
                        "remove": "Borrar",
                        "copy": "Copiar",
                        "csv": "fichero CSV",
                        "excel": "tabla Excel",
                        "pdf": "documento PDF",
                        "print": "Imprimir",
                        "colvis": "Visibilidad columnas",
                        "collection": "Colección",
                        "upload": "Seleccione fichero...."
                    },
                    "select": {
                        "rows": {
                            _: '%d filas seleccionadas',
                            0: 'clic fila para seleccionar',
                            1: 'una fila seleccionada'
                        }
                    }
                }           
            } );        
        },  
        
        /** Extrae el campo que se va a mapear */
        extractFields: function(valor, datos)
        {
            let inicioPatron = "data:";
            let finalPatron = "$";
            var posInicial = 0;
            
            posInicial = valor.indexOf(inicioPatron);

            while(posInicial >= 0)
            {
                valorCampo = 'N/D';

                if(posInicial >= 0)
                {

                    //  Encontramos el cierre
                    posFinal = valor.indexOf(finalPatron, posInicial + inicioPatron.length + 1);

                    if(posFinal > 0)
                    {

                        campo = valor.substring(posInicial + inicioPatron.length, posFinal);
                        // console.log('Campo: ' + campo);

                        //  Comprobamos si es una subentidad buscando el .
                        if(campo.indexOf(".") > 0)
                        {
                            definicion = campo.split(".");
                            if( datos[ definicion[0] ][0] !== undefined)
                            {
                                valorCampo = datos[ definicion[0] ][0]
                                valorCampo = valorCampo[definicion[1]];                            
                            }
                        }else{
                            if( datos[campo] !== undefined)
                            {
                                valorCampo = datos[campo];
                                switch(campo)
                                {
                                    case "created":
                                    valorCampo = moment(valorCampo).locale('es').format('L');
                                    break;
                                    case "estado":
                                        switch(valorCampo)
                                        {
                                            case "H":
                                                valorCampo = '<span class="badge rounded-pill bg-secondary d-block pt-2 pb-2">Histórico</span>';
                                                break;
                                            case "A":
                                                valorCampo = '<span class="badge rounded-pill bg-success d-block pt-2 pb-2">Alta</span>';
                                                break;
                                            case "B":
                                                valorCampo = '<span class="badge rounded-pill bg-danger d-block pt-2 pb-2">Baja</span>';
                                                break;
                                            case "P":
                                                valorCampo = '<span class="badge rounded-pill bg-warning d-block pt-2 pb-2">Pendiente</span>';                                        
                                        }
                                        break;
                                    case "solucionado":
                                        switch(valorCampo)
                                        {
                                            case '0':
                                                valorCampo = '<span class="badge rounded-pill bg-danger d-block pt-2 pb-2">Pendiente</span>';
                                                break;
                                            case '1':
                                                valorCampo = '<span class="badge rounded-pill bg-success d-block pt-2 pb-2">Solucionada</span>';
                                                break;                                        
                                        }
                                        break;
                                    case "activado":
                                        switch(valorCampo)
                                        {
                                            case '0':
                                                valorCampo = '<span class="badge rounded-pill bg-danger d-block pt-2 pb-2">No activo</span>';
                                                break;
                                            case '1':
                                                valorCampo = '<span class="badge rounded-pill bg-success d-block pt-2 pb-2">Activado</span>';
                                                break;                                        
                                        }
                                        break;                                        
                                }
                            }
                        }


                        var find = inicioPatron + campo + finalPatron;
                        // console.log("Valor obtenido: " + valorCampo);
                        valor = valor.replace(find, valorCampo );
                        // console.log("Reemplazo: " + valor);
                    }
                }

                posInicial = valor.indexOf(inicioPatron, posInicial  + inicioPatron.length + 1);

            }

            // console.log(valor);
            return  (valor == '' ? 'N/D': valor) ;
        },

        /** Agrega una columna con click para ampliar información */
        addColumnRow: function(id, data)
        {
            var columna = {};

            if(typeof CoreUI.tableData.columns[id] === 'undefined')
            {
                CoreUI.tableData.columns[id] = [];
            }

            columna = {
                    "className": 'details-control',
                     "orderable": false,
                     "data": data,
                     "defaultContent": '',
                     "render": function () {
                         return '<i class="fa fa-plus-square text-success abrir" aria-hidden="true"></i><i class="fa fa-minus-square text-danger cerrar" aria-hidden="true"></i>';
                     },
                     width:"15px"
            };

            CoreUI.tableData.columns[id].push(columna);
        },

        /** Añade la definición de una columna */
        addColumn: function(id, nombre, titulo, renderHTML, clase, widthColumn )
        {
            if(typeof CoreUI.tableData.columns[id] === 'undefined')
            {
                CoreUI.tableData.columns[id] = [];
            }

            var columna = {};

            if(renderHTML !== undefined && renderHTML !== null)
            {
                columna = {
                    "title": titulo, 
                    "data": null,
                    "width": widthColumn,
                    "className": clase,
                    render: function(data, type, row, meta){
                        html = renderHTML;
                        field = "codigo";
                        html = CoreUI.tableData.extractFields(html, data);
                        feather.replace();
                        return html;
                    }
                };
            }else{
                columna = {"data": nombre, "width": widthColumn, "render":null, "title":titulo};
            }

            CoreUI.tableData.columns[id].push(columna);

        },

        formatComunidad: function(d)
        {
            //  Debemos comprobar el rol para subir fichero
                var canUploadFile = false;
                var needFileUpload = false;
                var fileLink = '';

                if(core.Security.getRole() == 'ADMINFINCAS')
                    canUploadFile = true;

            var salida = `
                <div class="table-responsive">
                    <table border="0" class="table table-bordered table-light">
                        <thead class="">
                            <tr>
                                <th style="background: #dee2e6 !important;">Requerimiento</th>
                                <th class="text-center" style="background: #dee2e6 !important;width:150px;">Estado</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Documento</th>
                            </tr>
                        </thead>`;

                for(x = 0; x < d.documentacioncomunidad.length; x++)
                {

                    //  Nombre del requerimiento
                        salida += `<tr>
                                    <td>${d.documentacioncomunidad[x].requerimiento}</td>`;

                    //  Estado del requerimiento
                        salida += '<td class="text-center" >' + (!d.documentacioncomunidad[x].idficherorequerimiento ? '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2">No adjuntado</span>' : '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2">Disponible para descargar</span>') + '</td>';

                    //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                        ficheroAdjuntado = (!d.documentacioncomunidad[x].idficherorequerimiento ? false : true);
                        if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                        {
                            salida += ` <td class="text-center">
                                            <a href="${baseURL}public/storage/${d.documentacioncomunidad[x].storageficherorequerimiento}" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" title="Ver documento">
                                                <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                            </a>                                            
                                        </td>`;
                        }

                    //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                    //  SUBIR FICHERO SOLO PARA ADMINISTRADOR
                        if(!ficheroAdjuntado && canUploadFile)
                        {  
                                dataset = ` data-idcomunidad="${d.documentacioncomunidad[x].idcomunidad}" data-idempresa="" data-idempleado="" data-idrequerimiento="${d.documentacioncomunidad[x].idrequerimiento}" data-idrelacionrequerimiento="${d.documentacioncomunidad[x].idrelacion}" data-entidad="comunidad" `;
                                salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i></a></td>`;
                        }

                        if(!ficheroAdjuntado && !canUploadFile)
                            salida += '<td>&nbsp;</td>';

                        salida += `</tr>`;
                }

                salida += '</table></div>';
                return salida;
        },

        formatEmpleado: function(d)
        {
            //  Debemos comprobar el rol para subir fichero
                var canUploadFile = false;
                var needFileUpload = false;
                var fileLink = '';

            //  Tipo empleado 
                var value = $(`body #${CoreUI.tableData.selectedRowTableId} tr#${CoreUI.tableData.selectedRowIndex} td:nth-child(2)`).html().indexOf('Comunidad');
                var empleadoComunidad = false;
                    
                if(value>=0)
                    empleadoComunidad = true;

            //  SÓLO EL PERFIL DE CONTRATISTA PUEDE SUBIR FICHEROS
                if(core.Security.getRole() == 'CONTRATISTA' && !empleadoComunidad)
                    canUploadFile = true;

            var salida = `
                <div class="table-responsive">
                    <table border="0" class="table table-bordered table-light">
                        <thead class="">
                            <tr>
                                <th style="background: #dee2e6 !important;">Requerimiento</th>
                                <th class="text-center" style="background: #dee2e6 !important;width:150px;">Estado</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Documento</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Fecha de caducidad</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Observaciones</th>
                            </tr>
                        </thead>`;

                for(x = 0; x < d.documentacionprl.length; x++)
                {

                    //  Nombre del requerimiento
                        salida += `<tr>
                                    <td>${d.documentacionprl[x].requerimiento}</td>`;

                    //  Estado del requerimiento
                    //  Puede tener los siguientes estados:
                    //      P: Pendiente de validación
                    //      N: No adjuntado
                    //      R: Rechazado
                    //      V: Validado (Está OK)
                    //      C: Caducado
                    var estadoRequerimiento = '';
                    var classRequerimiento;
                    switch(d.documentacionprl[x].estado)
                    {
                        case '':
                        case 'N':
                            estadoRequerimiento = 'No adjuntado';
                            classRequerimiento = 'danger';
                            break;
                        case 'R':
                            estadoRequerimiento = 'Rechazado';
                            classRequerimiento = 'danger';
                            break;
                        case 'V':
                            estadoRequerimiento = 'Validado';
                            classRequerimiento = 'success';
                            break;
                        case 'C':
                            estadoRequerimiento = 'Caducado';
                            classRequerimiento = 'warning';
                            break;
                        case 'P':
                            estadoRequerimiento = 'Pendiente de validación';
                            classRequerimiento = 'info';
                            break;
                        default:
                            estadoRequerimiento = 'No adjuntado';
                            classRequerimiento = 'danger';
                            break;

                    }
                        salida += `<td class="text-center" ><span class="badge rounded-pill bg-${classRequerimiento} pl-3 pr-3 pt-2 pb-2">${estadoRequerimiento}</span></td>`;

                    //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                        ficheroAdjuntado = (!d.documentacionprl[x].idficherorequerimiento ? false : true);
                        if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                        {
                            salida += ` <td class="text-center">
                                            <a href="${baseURL}public/storage/${d.documentacionprl[x].storageficherorequerimiento}" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" title="Ver documento">
                                                <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                            </a>                                            
                                        </td>`;
                        }

                    //  Si es un empleado de la comunidad y además pertenece al rol admin de fincas entonces le damos permiso para subir el fichero
                        if( empleadoComunidad && core.Security.getRole() == 'ADMINFINCAS')
                            canUploadFile = true;

                    //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                    //  SUBIR FICHERO SOLO PARA ADMINISTRADOR
                        if(canUploadFile)
                        {  
                                dataset = ` data-idcomunidad="" data-idempresa="" data-idempleado="${d.documentacionprl[x].idempleado}" data-idrequerimiento="${d.documentacionprl[x].idrequerimiento}" data-idrelacionrequerimiento="${d.documentacionprl[x].idrelacion}" data-entidad="empleado" `;
                                salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i></a></td>`;
                        }

                        if(!ficheroAdjuntado && !canUploadFile)
                            salida += '<td>&nbsp;</td>';

                        //  Fecha de caducidad
                            var fechaCaducidad = (d.documentacionprl[x].fechacaducidad == null || d.documentacionprl[x].fechacaducidad == '' ? '-' :  moment(d.documentacionprl[x].fechacaducidad).locale('es').format('L'));
                            salida += `<td class="text-center">${fechaCaducidad}</td>`;

                        //  Observaciones
                            var observaciones = (d.documentacionprl[x].observaciones == null ? '' : d.documentacionprl[x].observaciones);

                            salida += `<td class="text-justify">${observaciones}</td>`;

                        salida += `</tr>`;
                }

                salida += '</table></div>';
                return salida;
        },

        /** Muestra la fila de información ampliada para CAE Empresa */
        formatEmpresa: function (d)
        {
            //  Debemos comprobar el rol para subir fichero
                var canUploadFile = false;
                var needFileUpload = false;
                var fileLink = '';

                if(core.Security.getRole() == 'CONTRATISTA')
                {
                    canUploadFile = true;

                }

            //  Comprobamos el estado del fichero)

            //  Si es un 
            var salida = `
                <div class="table-responsive">
                    <table border="0" class="table table-bordered table-light">
                        <thead class="">
                            <tr>
                                <th style="background: #dee2e6 !important;">Requerimiento</th>
                                <th class="text-center" style="background: #dee2e6 !important;width:150px;">Fecha última actuación</th>
                                <th class="text-center" style="background: #dee2e6 !important;">Estado</th>
                                <th style="background: #dee2e6 !important;">&nbsp;</th>`;
            salida += `        
                            </tr>
                        </thead>`;

            for(x = 0; x < d.documentacioncae.length; x++)
            {

                //  Nombre del requerimiento
                    salida += `
                        <tr>
                            <td>${d.documentacioncae[x].requerimiento}</td>
                            <td class="text-center" >`;

                //  Fecha última actuación
                    if(d.documentacioncae[x].fechaultimaactuacion != '' && d.documentacioncae[x].fechaultimaactuacion != null)
                    {
                        salida += moment( d.documentacioncae[x].fechaultimaactuacion ).locale('es').format('L');
                    }else{
                        salida += 'No se ha realizado ninguna actuación'; 
                    }
                    salida += '</td>';

                //  Estado del requerimiento
                    salida += '<td class="text-center" >' + (!d.documentacioncae[x].idficherorequerimiento ? '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2">No adjuntado</span>' : '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2">Disponible para descargar</span>') + '</td>';

                //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                    ficheroAdjuntado = (!d.documentacioncae[x].idficherorequerimiento ? false : true);
                    if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                    {
                        salida += ` <td class="text-center">
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Ver documento" id="home" data-original-title="Ver documento">
                                            <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                        </a>
                                    </td>`;
                    }

                //  Construimos el enlace de salida para que pueda descargar el fichero adjuntado
                    if(!ficheroAdjuntado && canUploadFile)
                    {  //  SUBIR FICHERO SOLO PARA CONTRATISTA (EMPRESA)
                        dataset = ` data-idcomunidad="${d.documentacioncae[x].idcomunidad}" data-idempresa="${d.id}" data-idempleado="" data-idrequerimiento="${d.documentacioncae[x].idrequerimiento}" data-idrelacionrequerimiento="${d.documentacioncae[x].idrelacion}" data-entidad="empresa" `;
                        salida += `<td class="text-center" ><a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 30px;"></i></a></td>`;
                    }

                    if(!ficheroAdjuntado && !canUploadFile)
                    {
                        salida += '<td>&nbsp;</td>';
                    }

                    salida += `</tr>`;
            }
            salida += '</table></div>';
            return salida;
        },

        format: function ( d, id ) 
        {
            var salida;

            switch(id)
            {
                case 'listadoEmpresa':
                case 'listadoEmpresa':
                case 'listadoEmpresaComunidad':
                    salida = CoreUI.tableData.formatEmpresa(d);
                    break;
                case 'listadoComunidad':
                    salida = CoreUI.tableData.formatComunidad(d);
                    break;
                case 'listadoEmpleadosComunidad':
                    salida = CoreUI.tableData.formatEmpleado(d);
                    break;
            }

            return salida;

        },

        tableEventsClick: function(id, detailRows)
        {
               // On each draw, loop over the `detailRows` array and show any child rows
            window['table' + id].on( 'draw', function () {
                $.each( detailRows, function ( i, id ) {
                    $('body #'+id+' td.details-control').off();
                    $('body #'+id+' td.details-control').trigger( 'click' );
                } );
            } );

         // Add event listener for opening and closing details
            $('body #' + id +' tr td.details-control').off();
            $('body #' + id).on( core.helper.clickEventType, 'tr td.details-control', function (e) {

                //  Obtenemos el id de la fila que ha disparado el evento
                    CoreUI.tableData.selectedRowIndex = $(this).parent().attr('id');

                //  Obtenemos el id de la tabla que está disparando el evento
                    idTabla = ($(this).parent().parent().parent().attr('id'));
                    CoreUI.tableData.selectedRowTableId = idTabla;

                    var tr = $(this).closest('tr');
                    var row = window['table' + idTabla].row( tr );
                    var idx = $.inArray( tr.attr('id'), detailRows );
        
                if ( row.child.isShown() ) {
                    tr.removeClass( 'shown' );
                    row.child.hide();
        
                    // Remove from the 'open' array
                    // detailRows.splice( idx, 1 );
                    tr.removeClass('shown');
                }
                else {
                    tr.addClass( 'shown' );
                    row.child( CoreUI.tableData.format(row.data(), idTabla )).show();
                }

                documentalCore.Events();

            } );

            //  Suscripción al evento del click salvo que la tabla no sea clicable
                if( !$(`body #${id}`).hasClass('no-clicable') )
                {
                    $(`body #${id} tr > td`).off();
                    $(`body #${id}`).on('click', 'tr > td', function (e) 
                    {
                        if($(this).html().indexOf('accionesTabla') > 0)
                        {
                            console.log('Ha clicado en acciones');
                        }else{
                            var data = window['table' + id].row( this ).data();
                            //  Redirigimos a la pantalla correspondiente que está basada en el endpoint
                            //  quitando "list" ya que es el endpoint que se utiliza para el listado ajax
                            window.location.href = baseURL + endpoint.toLowerCase().replace('list', data.id);
                        }

                    });
                }     
        },

        /** Renderiza la tabla */
        render: function(id, entity, endpoint, allColumns, usePagination = true, _search = true, customRender = null)
        {
            // console.log(this.columns.length);
            CoreUI.tableData.init();
            if(allColumns === true)
            {
                //  Nos traemos la definición de la entidad para poder generar las columnas de la tabla
            }

            var detailRows = [];

            var opciones = {
                "serverSide": false,
                "autoWidth": false,
                "select": true,
                "retrieve": true,
                "paging": usePagination,
                "searching": _search,
                ajax: {
                    "url": config.baseURLEndpoint + endpoint,
                    "dataSrc": "data." + entity 
                },
                "columns": CoreUI.tableData.columns[id],  
                "columnDefs": [{
                    "targets": CoreUI.tableData.columns[id].length -1,
                    "className": "text-center"
                }],
                "drawCallback": function(settings){
                    feather.replace();
                },
                "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                    $(nRow).attr('id', iDataIndex);
                }                
            };

            if(customRender != null)
                opciones['render'] = customRender;

            window['table' + id] = $(`#${id}`).DataTable(opciones);

            CoreUI.tableData.tableEventsClick(id, detailRows);

        }        

    },

    Modal: {

        GetHTML: function(id, html, titulo, texto, callback)
        {
            if($('body .'+id).length)
            {
                $('body .'+id).remove();   
            }

            $('body').append(html);

            feather.replace();
            $('body .' + id).modal('show');
        },

        CustomHTML: async function(texto, titulo, callback, modalWidth)
        {
            await Swal.fire({
                html: `${texto}`,
                title: titulo,
                showCancelButton: false,
                showConfirmButton: false,
                width: (modalWidth == '' ? '32rem' : modalWidth)
            });  
        },

        /** Muestra un modal de ok */
        Success: function(texto, titulo, callback)
        {
            Swal.fire({
                text: `${texto}`,
                title: titulo,
                icon: 'success',
                showCancelButton: false
            }).then( (result) => {
                if(result.isConfirmed && callback !== undefined)
                {
                    callback();
                }
            });    
        },

        Info: function(texto, titulo, callback)
        {
            Swal.fire({
                text: `${texto}`,
                title: '',
                icon: 'info',
                showCancelButton: false
            }).then( (result) =>{
                if(result.isConfirmed && typeof callback !== 'undefined')
                {
                    callback();
                }
            }); 
        },

        /** Muestra un modal de error */
        Error: function(texto, titulo, callback)
        {
            Swal.fire({
                text: `${texto}`,
                title: '',
                icon: 'error',
                showCancelButton: false
            }).then( (result) =>{
                if(result.isConfirmed && callback !== undefined)
                {
                    callback();
                }
            });  
        },

    }

}

$(()=>{
    CoreUI.init;
});