// const { ajaxPrefilter } = require("jquery");

let CoreUI = {

    init: function(){

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
    },

    tableData: {

        _this: null,
        columns: [],

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
                    "searchPlaceholder": "Término de búsqueda",
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

        /** Añade la definición de una columna */
        addColumn: function(nombre, titulo, renderHTML, clase, widthColumn )
        {

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

            this.columns.push(columna);

        },

        /** Renderiza la tabla */
        render: function(id, entity, endpoint, allColumns, _paging = true, _search = true)
        {
            // console.log(this.columns.length);
            CoreUI.tableData.init();
            if(allColumns === true)
            {
                //  Nos traemos la definición de la entidad para poder generar las columnas de la tabla
            }

            //  Cargamos el listad
            window['table' + id] = $(`#${id}`).DataTable({
                "serverSide": true,
                "autoWidth": true,
                "select": true,
                "retrieve": true,
                "paging": _paging,
                "searching": _search,
                ajax: {
                    "url": config.baseURLEndpoint + endpoint,
                    "dataSrc": "data." + entity 
                },
                "columns": this.columns,  
                "columnDefs": [{
                    "targets": this.columns.length -1,
                    "className": "p-0 text-center"
                }],
                "drawCallback": function(settings){
                    feather.replace();
                }
            });

            //  Suscripción al evento del click salvo que la tabla no sea clicable
            if( !$(`#${id}`).hasClass('no-clicable') )
            {
                $(`#${id}`).on('click', 'tr > td', function (e) 
                {
                    console.log($(this));
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