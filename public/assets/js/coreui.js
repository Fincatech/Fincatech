let CoreUI = {

    tituloModulo: null,

    init: function(){

    },

    setTitulo: function(value)
    {
        CoreUI.tituloModulo = value;
    },

    Controller:{

        /**
         * Carga automáticamente los combos de la pantalla según el modelo y columnas a mostrar
         */
        InitializeSelectData: async function()
        {
            var promesaSelect;
            var numeroCombos = $("body .form-data .select-data").length;
            var iCombo = 0;
      
            promesaSelect = new Promise(async function(resolve, reject){
      
            //  Validamos que hay algún select dentro del form
              if($("body .form-data .select-data").length)
              {
                //  Iteramos sobre todos los select del formulario
                $("body .form-data .select-data").each(function()
                {
                    var insertarOpcionSeleccionar = false;
      
                    var entidadCombo = $(this).attr("hs-list-entity");
                    var keyField = $(this).attr("hs-list-field");
                    var keyValue = $(this).attr("hs-list-value");
                    var elDOM = $(this).prop("id");
      
                    var keyOriginField = $(this).attr("hs-field");
                    var keyOriginValue = $(this).attr("hs-value");
      
                    if($(this).attr('hs-seleccionar') !== undefined)
                    {
                      insertarOpcionSeleccionar = ($(this).attr('hs-seleccionar') == 'false' ? false : true);              
                    }
      
                    var promesaSelectActual = new Promise(async function(resolve2, reject2){

                        CoreUI.Controller.GetSelectData( entidadCombo, keyField, keyValue, null, null, elDOM, insertarOpcionSeleccionar ).then((result)=>{
                            iCombo++;
                            if(iCombo >= numeroCombos){
                                resolve(true);
                            }
                        });
      
                    });
      
                });
      
              }else{
                resolve(true);
              }
              
            });
      
            promesaSelect.then(function(){
                core.Forms.getModelo();
                return promesaSelect;
            }); 
        },

        /**
         * Obtiene los datos del WS
         */
        GetSelectData: async function(entidad, keyField, keyValue, keyOriginField, keyOriginValue, elementoDOM, insertarOpcionSeleccionar)
        {
            //  Vaciamos el combo
            $("body #" + elementoDOM).html('');

            //  Recuperamos los registros que correspondan según la entidad
            var promSelect = new Promise(function(resolve, reject)
            {
                apiFincatech.get(`${entidad}/list?target=cbo`).then( (data) =>
                // await apiFincatech.get(`${entidad}/list?target=cbo`).then( async (data) =>
                {

                    var htmlOutput = "";
                    var result = JSON.parse(data);
                    responseData = result.data;

                    //  Validamos que exista el dato
                    if(!responseData[entidad])
                    {
                        alert(`Error. La entidad ${entidad} no es correcta para el select`);
                        console.log('Error en el select ', elementoDOM, ' entidad: ', entidad);
                        return;
                    }

                    if(insertarOpcionSeleccionar){
                        htmlOutput = '<option value="-1" disabled selected="selected">Seleccione una opción</option>';
                        $("body #" + elementoDOM).append(htmlOutput);
                    }

                    //  Primero sacamos todos los valores que queremos pintar en el combo
                    var values = keyField.split(',');
                    var valueId;
                    var valueToShow;

                    // Obtenemos el nombre del campo que vamos a utilizar para el value del select
                    // y que viene definido en hs-list-value del propio select
                    keyValue = keyValue.split('.')

                    //  Recorremos todos los registros que hemos recuperado del WS
                    for(var i = 0; i < responseData[entidad].length; i++)
                    {
                        valueToShow = Array();
                        valueId = responseData[entidad][i][keyValue[1]];

                        for(var x = 0; x < values.length; x++)
                        {
                            //  Descomponemos para obtener la entidad y el campo que se va a mostrar
                            fieldValue = values[x].split('.');
                            //  Insertamos en el array el valor que se va a pintar
                            valueToShow.push( responseData[entidad][i][fieldValue[1]] );
                        }
                        //  Unimos el array para la visualización dentro del propio select
                        valueToShow = valueToShow.join(' - ');
                        //  Construimos el elemento html para el select
                        htmlOutput = `<option value="${valueId}">${valueToShow}</option>`;
                        //  Agregamos el elemento al dom
                        $("body #" + elementoDOM).append(htmlOutput);
                    }

                    //  Inicializamos el componente select2 para el select que se acaba de poblar
                    $('body #' + elementoDOM).select2({
                        theme: 'bootstrap4',
                        placeholder: "Seleccione una opción",
                    });
                    resolve(true);
                });
            });

            return promSelect.then(function(){
                return true;
            });
        },

    },

    Forms:{

        Errors: {

            hasErrors: false,

            /**
             * Establece el mensaje de error para un campo
             * @param {*} idDOM ID del campo que contiene el error
             * @param {*} msg   Mensaje que se va a mostrar
             */
            SetMessage: function(idDOM, msg)
            {
                //  Establecemos el mensaje de error
                $(`#${idDOM}-msg-error`).html(msg);
                CoreUI.Forms.Errors.hasErrors = true;
                CoreUI.Forms.Errors.ShowErrors();
            },

            /**
             * Muestra los posibles errores que tenga el formulario
             */
            ShowErrors: function()
            {
                //  Ocultamos todos los posibles errores que haya en el formulario
                $('.msg-form-error').removeClass('d-none').addClass('d-none');
                //  Recorremos todos los posibles errores que haya en el formulario
                $('.msg-form-error').each(function(ev){
                    let field = $(this).attr('data-input-field-id');
                    if($(this).html() !== '')
                    {
                        //  Quitamos la clase que oculta el label
                        $(this).removeClass('d-none');
                        //  Añadimos la clase de error al formulario
                        $(`#${field}`).removeClass('form-error').addClass('form-error');
                    }else{
                        //  Quitamos la marca de error sobre el input                        
                        $(`#${field}`).removeClass('form-error');
                    }
                });
            }
        }


    },

    Utils: {

        /**
         * Formatea un número a moneda
         * @param {*} numero 
         * @returns 
         */
        formatNumberToCurrency: function(numero)
        {
            // let total = parseFloat(numero);
            // total = total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&.');//.replace(',', '.');
            // return total;
            let total = parseFloat(numero);
            total = Math.round(total * 100) / 100; // Redondea al segundo decimal
            total = total
                .toString()
                .replace('.', ',') // Cambia el separador decimal a coma
                .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Inserta puntos como separadores de miles
            return total;          
        },

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
        },

        redirectTo(_url)
        {
            window.location = _url;
        },

        renderLabelByEstado: function(value)
        {
            if(value == '' || typeof value === 'undefined' || !value)
                return;
            
            var out;
            // console.log('Valor value: ' + value);
            switch (value.toLowerCase())
            {
                case 'no adjuntado':
                    out = '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>';
                    break;                 
                case 'adjuntado':
                    out = '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Adjuntado</span>';
                    break;
                case 'no descargado':
                    out = '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No descargado</span>';
                    break;  
                case 'no verificado':
                    out = '<span class="badge rounded-pill bg-warning pl-3 pr-3 pt-2 pb-2 d-block">Pendiente revisión</span>';
                    break; 
                case 'descargado':
                    out = '<span class="badge rounded-pill bg-primary pl-3 pr-3 pt-2 pb-2 d-block">Descargado</span>';
                    break;    
                case 'verificado':
                    out = '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Verificado</span>';
                    break;      
                case 'rechazado':
                    out = '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">Rechazado</span>';
                    break;  
                case 'pendiente verificación':
                    out = '<span class="badge rounded-pill bg-warning pl-3 pr-3 pt-2 pb-2 d-block">Pendiente revisión</span>';
                    break;
            }

            return out;

        },

        /**
         * 
         * @param {*} domInputName 
         */
        getSelectedFileInBase64Format: async function(domInputName)
        {
            var fileInput = document.getElementById(domInputName);
            if (fileInput.files.length === 0) {
                return false;
            }else{
                // Obtener el primer archivo seleccionado (puedes ajustar esto según tus necesidades)
                var selectedFile = fileInput.files[0];

                return new Promise((resolve, reject) =>{
                    const reader = new FileReader();

                    reader.onload = (event) => {
                        resolve(event.target.result);
                    };
    
                    reader.onerror = (error) => {
                        reject(error);
                    };
    
                    reader.readAsDataURL(selectedFile);
                });
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
                    "search": '<i class="bi bi-search"></i>',
                    "searching": true,
                    "searchPlaceholder": "Buscar",
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
                        "print": '<i class="bi bi-printer"></i> Imprimir listado',
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

        /**
         * Añade la definición de una columna
         * @param {*} id 
         * @param {*} nombre Nombre de la columna
         * @param {*} titulo Titulo
         * @param {*} renderHTML HTML a renderizar
         * @param {*} clase Clase que se va a aplicar al contenido
         * @param {*} widthColumn Ancho de la columna
         * @param {*} _render 
         * @param {*} _type 
         */
        addColumn: function(id, nombre, titulo, renderHTML, clase, widthColumn, _render = null, _type = null )
        {
            if(typeof CoreUI.tableData.columns[id] === 'undefined')
            {
                CoreUI.tableData.columns[id] = [];
            }

            var columna = {};

            // if(renderHTML !== undefined && renderHTML !== null && (_render == undefined || _render == null)){
            //     _render 
            //     const numericPart = parseInt(data.match(/^\d+/)?.[0], 10) || Number.MAX_SAFE_INTEGER;
            //     const textPart = data.replace(/^\d+/, '').toLowerCase();
            //     // Devuelve un valor de ordenación compuesto
            //     return JSON.stringify({ num: numericPart, text: textPart });
            // }

            // console.log(!!nombre);
            //             nombre = typeof nombre === 'function' ? '' : nombre;
            // console.log(typeof nombre);
            if(renderHTML !== undefined && renderHTML !== null)
            {
                columna = {
                    "title": titulo, 
                    "data": null,
                    "width": widthColumn,
                    "className": clase,
                    "name": (!!nombre ? nombre : null),
                    render: function(data, type, row, meta){
                        html = renderHTML;
                        field = "codigo";
                        html = CoreUI.tableData.extractFields(html, data);
                        feather.replace();
                        return html;
                    }
                };
            }else{

                if(typeof nombre !== 'function' && typeof nombre !== 'object')
                {
                    columna = {
                        "data": nombre, 
                        "name": nombre,  
                        "className": clase, 
                        "width": widthColumn, 
                        "render": _render, 
                        "title":titulo};
                }else{
                    columna = {
                        "data": nombre, 
                        "className": clase, 
                        "width": widthColumn, 
                        "render": _render, 
                        "title":titulo
                    };
                }
            }

            CoreUI.tableData.columns[id].push(columna);

        },

        formatComunidad: function(d)
        {
            //  Debemos comprobar el rol para subir fichero
                var canUploadFile = false;
                var needFileUpload = false;
                var fileLink = '';

                if(core.Security.getRole() == 'ADMINFINCAS' || core.Security.getRole() == 'SUDO')
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
                        salida += '<td class="text-center" >' + (!d.documentacioncomunidad[x].idficherorequerimiento ? '<span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>' : '<span class="badge rounded-pill bg-success pl-3 pr-3 pt-2 pb-2 d-block">Verificado</span>') + '</td>';

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
                        if(canUploadFile)
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

            //  SÓLO EL PERFIL DE CONTRATISTA PUEDE SUBIR FICHEROS o bien el administrador de la comunidad
            //  siempre que el empleado sea directo de la comunidad
                if( (core.Security.getRole() == 'CONTRATISTA' && !empleadoComunidad) )
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

                    if( empleadoComunidad && core.Security.getRole() == 'ADMINFINCAS')
                        canUploadFile = true;

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
                        salida += `<td class="text-center" ><span class="badge rounded-pill bg-${classRequerimiento} pl-3 pr-3 pt-2 pb-2 d-block">${estadoRequerimiento}</span></td>`;

                    //  Enlace al fichero de descarga si está ya adjuntado o bien para subir si tiene permiso
                        ficheroAdjuntado = (!d.documentacionprl[x].idficherorequerimiento ? false : true);
                        salida += ` <td class="text-center">`;
                        if(ficheroAdjuntado)   //  DESCARGAR FICHERO YA SUBIDO
                        {
                            salida += ` 
                                            <a href="${baseURL}public/storage/${d.documentacionprl[x].storageficherorequerimiento}" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" title="Ver documento">
                                                <i class="bi bi-cloud-arrow-down text-success" style="font-size: 30px;"></i>
                                            </a>`;
                        }

                        //  Si es un empleado de la comunidad y además pertenece al rol admin de fincas entonces le damos permiso para subir el fichero
                            if(canUploadFile)
                            {  
                                dataset = ` data-idcomunidad="" data-idempresa="" data-idempleado="${d.idempleado}" data-idrequerimiento="${d.documentacionprl[x].idrequerimiento}" data-idrelacionrequerimiento="${d.documentacionprl[x].idrelacion}" data-entidad="empleado" `;
                                salida += `<a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento" data-toggle="tooltip" ${dataset} data-placement="bottom" title="" id="home" data-original-title="Adjuntar documento"><i class="bi bi-cloud-arrow-up text-danger" style="font-size: 30px;"></i></a>`;
                            }

                        salida += `</td>`;



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
                                <th style="background: #dee2e6 !important;">Administrador</th>
                                <th class="text-left" style="background: #dee2e6 !important;width:150px;">Comunidad</th>`;
            salida += `        
                            </tr>
                        </thead>`;

            for(x = 0; x < d.comunidades.length; x++)
            {

                //  Nombre del requerimiento
                    salida += `
                        <tr>
                            <td width="50%" style="font-size:14px;">${d.comunidades[x].administrador}</td>
                            <td width="50%" class="text-left" style="font-size:14px;">${d.comunidades[x].nombre}</td>
                        </tr>`;

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
                    console.log('click empresas');
                    break;
                case 'listadoComunidad':
                    console.log('click comundiad');
                    salida = CoreUI.tableData.formatComunidad(d);
                    break;
                case 'listadoEmpleadosComunidad':
                    console.log('click empleados');
                    salida = CoreUI.tableData.formatEmpleado(d);
                    break;
            }

            return salida;

        },

        tableEventsClick: function(id, detailRows, endpoint = null)
        {
            if(typeof window['table' + id] !== 'undefined')
            {
               // On each draw, loop over the `detailRows` array and show any child rows
               window['table' + id].on( 'draw', function () {
                $.each( detailRows, function ( i, id ) {
                    $('body #'+id+' td.details-control').off();
                    $('body #'+id+' td.details-control').trigger( 'click' );
                } );
            } );
            }

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
                            if(typeof data !== 'undefined')
                                window.location.href = baseURL + endpoint.toLowerCase().replace('list', data.id);
                        }

                    });
                }     
        },

        /**
         * Renderiza la tabla
         * @param {*} id 
         * @param {*} entity 
         * @param {*} endpoint 
         * @param {*} allColumns 
         * @param {*} usePagination 
         * @param {*} _search 
         * @param {*} customRender 
         * @param {*} showPrint 
         * @param {*} groupResults 
         * @param {*} groupCols 
         * @param {*} _serverSide 
         * @param {*} _ajaxType 
         * @param {*} _responsive 
         * @param {*} _showExportToExcel 
         */
        render: async function(id, entity, endpoint, allColumns, 
            usePagination = true, _search = true, customRender = null, 
            showPrint = null, groupResults = false, groupCols = null, _serverSide = false, _ajaxType='GET',_responsive = true, _showExportToExcel = false)
        {
            let promesaTabla = new Promise( (resolve, reject) => {
                // console.log(this.columns.length);
                CoreUI.tableData.init();

                var detailRows = [];

                var opciones = {
                    "serverSide": _serverSide,
                    "autoWidth": false,
                    "select": true,
                    'scrollCollapse': true,
                    "retrieve": true,
                    "paging": usePagination,
                    "searching": _search,
                    "responsive": _responsive,
                    ajax: {
                        "url": config.baseURLEndpoint + endpoint,
                        "dataSrc": `data.${entity}`,
                        "type": _ajaxType
                    },
                    "deferRender": true,
                    "columns": CoreUI.tableData.columns[id],  
                    "columnDefs": [{
                        "targets": CoreUI.tableData.columns[id].length -1,
                        "className": CoreUI.tableData.columns[id].className,
                        "type": 'custom-alnum',
                        targets: 0
                    }],
                    "drawCallback": function(settings){
                        feather.replace();
                    },
                    "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                        $(nRow).attr('id', iDataIndex);
                    }                
                };

                if(showPrint === true)
                {
                    opciones['dom'] = 'Bfrtip';
                    opciones['buttons'] = [
                        'print'
                    ];
                }

                //  Agrupado
                if( groupResults === true)
                {
                    opciones['rowGroup'] = {'dataSrc': groupCols};
                }

                //  Exportación a Excel
                if(_showExportToExcel)
                {
                    opciones['buttons'] =[
                        {
                            extend: 'excel',
                            className: 'btn-success',
                            text: 'Exportar a fichero Excel',
                        }
                    ];

                    if(showPrint)
                    {
                        opciones['buttons'].push({
                            extend: 'print',
                            text: 'Imprimir',
                        });
                    }

                }

                if(customRender != null)
                    opciones['render'] = customRender;

                //  Comprobamos si ya está inicializada la tabla
                if ( $.fn.DataTable.isDataTable( `#${id}` ) ) {
                    window['table' + id].destroy();
                }

                window['table' + id] = $(`#${id}`).DataTable(opciones);

                CoreUI.tableData.tableEventsClick(id, detailRows, endpoint);
                resolve(true);
            });

            promesaTabla.then((result)=>{
                return result;
            });
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

        CustomHTML: async function(texto, titulo, callback = null, modalWidth = null, growType = null, callbackOnClose = null)
        {
            await Swal.fire({
                html: `${texto}`,
                title: titulo,
                showCancelButton: false,
                showConfirmButton: false,
                grow: (growType == '' ? 'fullscreen' : growType),
                width: (modalWidth == '' ? '32rem' : modalWidth),
                didOpen: 
                    function(){
                        if(callback !== null){
                            console.log('callback didopen');
                            callback();
                        } 
                },
                didClose:
                    function(){
                        if(callbackOnClose !== null){
                            console.log('callback close');
                            callbackOnClose();
                        } 
                }
            });  
        },

        /** Muestra un modal de ok */
        Success: function(texto, titulo, callback)
        {
            Swal.fire({
                html: `${texto}`,
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
                html: `${texto}`,
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
                html: `${texto}`,
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

        Question: function(texto, titulo, tituloBotonConfirmar, callback)
        {
            Swal.fire({
                title: titulo,
                html: texto,
                grow:'true',
                width: '60rem',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-circle mr-2"></i> ' + tituloBotonConfirmar,
                cancelButtonText: '<i class="bi bi-x-circle mr-2"></i> Cancelar',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    actions: 'text-center p-3 shadow-inset rounded-pill border-light border-2 bg-white',
                    confirmButton: 'btn btnConfirmModalAction btn-success rounded-pill shadow d-block pb-2 pt-2 confirmButtonModal',
                    cancelButton: ' btn btn-danger btnCancelSave rounded-pill shadow d-block pb-2 pt-2 mr-3 cancelButtonModal',
                    popup: 'bdg-transparent'
                }            
              }).then((result) => {
                if (result.isConfirmed) 
                {
                    callback();
                }
            }); 
        }

    },

    /**
     * Módulo de información de progreso
     */
    Progress: {

        Total: 100,
        Actual: 0,

        SetProgress: function(value){
            CoreUI.Progress.Actual = value;
            //  Porcentaje
            $('.fincatech--progreso .progress-bar').css('width',`${CoreUI.Progress.Actual}%`);
            $('.fincatech--progreso .progress-bar').html(`${CoreUI.Progress.Actual}%`);
        },

        /**
         * Establece los valores iniciales de la barra de progreso y el texto inicial
         */
        Init: function(){
            CoreUI.Progress.SetMessage('Iniciando proceso');
            $('.fincatech--progreso .progress-bar').html('0%');
            $('.fincatech--progreso .progress-bar').css('width','0');
        },

        /**
         * Muestra el cuadro de progreso
         */
        Show: function(){
            CoreUI.Progress.Init();
            $('body').addClass('progress');
            $('.fincatech--progreso').removeClass('d-none');
        },

        /**
         * Oculta el cuadro de pgoreso
         */
        Hide: function(){
            $('body').removeClass('progress');
            $('.fincatech--progreso .btnProcesoTerminado').hide();
            $('.fincatech--progreso').removeClass('d-none').addClass('d-none');
        },

        /**
         * Pinta el mensaje en el cuadro de progreso
         * @param {string} value Texto a mostrar
         */
        SetMessage: function(value){
            //  Texto
            $('body .fincatech--progreso .mensaje').html(value);
        },

        Completed: function(){
            $('body .fincatech--progreso .btnProcesoTerminado').show();
        }

    },

    Sidebar: {

        Comunidades: {

            cargarMenuComunidades: function()
            {

                var endpointComunidades = '';

                core.Security.getUserInfo().then( (result) =>
                {

                    switch( core.Security.getRole() )
                    {
                        case 'CONTRATISTA':
                            endpointComunidades = `empresa/${core.Security.user}/comunidades`;
                            break;
                        case 'ADMINFINCAS':
    
                            break;
                        default:
                            return;
                    }
    
                    apiFincatech.get( endpointComunidades ).then ( (result) =>
                    {
                        var datos = JSON.parse(result);
                        var comunidades = datos.data.Comunidades['Comunidad'];
                        // $('.navComunidades').html('');
                        var outHTML = ``;
                        $('.sidebar-nav').prepend(`
                        <div class="row pl-3 pr-3 sticky-top" style="background: #222e3c;">
                            <div class="col-12 pb-2 pt-2 mb-3 mt-2">
                                <p class="text-white text-uppercase mt-0 mb-0"><small>Buscar comunidad</small></p>
                                <div class="row">
                                    <div class="col-10">
                                        <input type="text" class="form-control busquedaComunidad" placeholder="Escriba cód o nombre">
                                    </div>
                                    <div class="col-2 align-self-center">
                                        <a href="javascript:void(0);" class="btnLimpiarBusqueda"><i class="bi bi-x-circle text-white"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>`);     

                        if( comunidades.length > 0 )
                        {             

                            for(x = 0; x < comunidades.length; x++)
                            {
                                outHTML = `<li class="sidebar-item pl-3 pr-3 pb-2 pt-2" data-codigo="${comunidades[x].codigo}" data-nombre="${comunidades[x].nombre}">
                                                <div class="row">
                                                    <div class="col-1 text-left pl-0 pr-0 align-self-center">
                                                        <img src="${config.baseURL}public/assets/img/icon_edificio.png" class="img-responsive feather">
                                                    </div>   
                                                    
                                                    <div class="col-11 pr-0">
                                                        <a href="${config.baseURL}comunidad/${comunidades[x].id}" class="text-white d-block">   
                                                            <span class="align-middle comunidad-${comunidades[x].id} d-block" style="font-size: 13px;">${comunidades[x].codigo} ${comunidades[x].nombre}</span>
                                                        </a>
                                                    </div>  
<!--
                                                    <div class="col-2 pr-0 text-center pl-0 align-self-center">
                                                        <a href="javascript:void(0);" class="btnEliminarComunidad" data-id="${comunidades[x].codigo}" data-nombre="${comunidades[x].nombre}">
                                                            <i data-feather="trash-2" class="text-danger"></i>
                                                        </a>
                                                    </div>
-->
                                                </div>
                                            </li>`;
                                $('.navComunidades').append(outHTML);
                            }
                        }else{
                            $('.navComunidades').append('<p class="text-center text-white">No tiene comunidades asignadas</p>');
                        }

                        $('body').on(core.helper.clickEventType,'.btnLimpiarBusqueda', function(evt){
                            $('.busquedaComunidad').val('');
                            CoreUI.Sidebar.Comunidades.buscarComunidad();
                        });
                
                        $('body').on('keyup', '.busquedaComunidad', function(evt)
                        {
                            CoreUI.Sidebar.Comunidades.buscarComunidad();
                        });

                        feather.replace();

                    });                    

                });

            },

            buscarComunidad: function()
            {
                var textoBusqueda = $('.busquedaComunidad').val().toLowerCase();

                if(textoBusqueda == '')
                {
                    $('.navComunidades .sidebar-item').removeClass('d-none');
                    return;
                }

                $('.navComunidades .sidebar-item').each( function(e)
                {
                    if( $(this).attr('data-nombre').toLowerCase().indexOf(textoBusqueda) >= 0 ||
                        $(this).attr('data-codigo').toLowerCase().indexOf(textoBusqueda) >= 0)
                        {
                            $(this).removeClass('d-none');
                        }else{
                            $(this).addClass('d-none');
                        }
                });
            }

        }

    }

}

$(()=>{
    CoreUI.init;
    // Plug-in para ordenación alfanumérica
    $.extend($.fn.dataTable.ext.type.order, {
        "custom-alnum-pre": function (data) {
            // Maneja valores nulos, indefinidos y vacíos
            // console.log(data);
            return ;
            // if (data == null || data === "") {
            //     return { numPart: Infinity, textPart: "" };
            // }
    
            // // Verificar si es una fecha válida
            // const date = new Date(data);
            // if (!isNaN(date.getTime())) {
            //     return { numPart: date.getTime(), textPart: "" }; // Ordena por timestamp
            // }
    
            // // Separar números y texto
            // const matches = data.toString().match(/(\d+)|(\D+)/g);
            // if (!matches) {
            //     return { numPart: Infinity, textPart: data.toLowerCase() }; // No hay números, solo texto
            // }
    
            // // Convertir la parte numérica a un número
            // const numPart = matches[0] ? parseInt(matches[0], 10) : Infinity;
            // const textPart = matches.slice(1).join("").toLowerCase(); // El resto es texto
    
            // return { numPart, textPart };
        },
        "custom-alnum-asc": function (a, b) {
            return a.numPart - b.numPart || a.textPart.localeCompare(b.textPart);
        },
        "custom-alnum-desc": function (a, b) {
            return b.numPart - a.numPart || b.textPart.localeCompare(a.textPart);
        }
    });
});
