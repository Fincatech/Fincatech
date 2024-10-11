let Proveedor = {

    //  Se utiliza cuando selecciona un proveedor nuevo poder saber a cuántas comunidades va a afectar el cambio
    comunidadesAfectadas: 0,
    empresaId: -1,
    empresaNuevaId: -1,

    Init: function(){
        CoreUI.Controller.InitializeSelectData();
        Proveedor.Events();
    },

    Events: function(){
        $('body').on('change','#proveedorAntiguoId',function(evt){
            //  Renderizamos la tabla de comunidades que tiene asignadas el proveedor que se desea reasignar
            let empresaId = $(this).val();
            Proveedor.empresaId = empresaId;
            Proveedor.Controller.LoadComunidadesProveedor();
        });

        $('body').on('change','#proveedorNuevoId',function(evt){
            //  Renderizamos la tabla de comunidades que tiene asignadas el proveedor que se desea reasignar
            let empresaId = $(this).val();
            Proveedor.empresaNuevaId = empresaId;
        });

        $('body').on(core.helper.clickEventType, '.btnReasignarProveedor', function(){
           Proveedor.Controller.ReasignarProveedor();
        });
    },

    Controller:{
        /**
         * Ejecuta el proceso de reasignación de proveedor previa confirmación al usuario
         */
        ReasignarProveedor: function()
        {

            //  Validamos que haya seleccionado los 2 proveedores
            if(Proveedor.empresaId <= 0 || Proveedor.empresaNuevaId <= 0)
            {
                CoreUI.Modal.Error('Debe seleccionar el proveedor actual y el proveedor al que desea reasignar las comunidades');               
                return;
            }

            //  Validamos que no haya seleccionado el mismo proveedor
            if(Proveedor.empresaId == Proveedor.empresaNuevaId)
            {
                CoreUI.Modal.Error('El proveedor actual y el proveedor de destino es el mismo');               
                return;
            }

            //  Validamos que tenga comunidades asignadas
            if(window['tablelistadoComunidadesProveedor'].data().length == 0)
            {
                CoreUI.Modal.Error('El proveedor seleccionado no tiene asignada actualmente ninguna comunidad');
                return;
            }

            //  Preguntamos primero al usuario si desea continuar con la reasignación del proveedor
            var htmlModal = `<p class="mb-4">¿Desea continuar con el proceso de reasignación?</p>
            <p class="mb-2">
                <span class="font-weight-bold">Proveedor actual</span><br>
                ${$('#proveedorAntiguoId option:selected').text()}
            </p>
            <p class="mb-4">
            <span class="font-weight-bold">Proveedor de destino</span><br>
                ${$('#proveedorNuevoId option:selected').text()}
            </p>
            <p class="text-danger"><span class="font-weight-bold">Atención</span>: Este cambio es irreversible.</p>`;
            //  Debemos avisar que el cambio es irreversible
            CoreUI.Modal.Question(htmlModal, 'Reasignación de proveedor', 'Reasignar proveedor', function(){
                    var data = Object();
                    data ={
                        empresaId: Proveedor.empresaId,
                        empresaNuevaId: Proveedor.empresaNuevaId
                    };

                    apiFincatech.post(`empresa/${Proveedor.empresaId}/reasignar/${Proveedor.empresaNuevaId}`,data).then( result =>{
                        CoreUI.Modal.Success('El proceso ha finalizado');
                        //  Deseleccionamos los dos combos y limpiamos la tabla
                        Proveedor.Controller.LoadComunidadesProveedor();
                    });
            });
            
        },

        /**
         * Carga la tabla de comunidades asociadas a un contratista/empresa
         */
        LoadComunidadesProveedor: function()
        {
            Proveedor.View.RenderTableComunidadesProveedor();
        }
    },

    Model: {

    },

    View:{
        RenderTableComunidadesProveedor: function()
        {
            if($('#listadoComunidadesProveedor').length)
            {

                if(typeof window['listadoComunidadesProveedor'] != 'undefined')
                {
                    CoreUI.tableData.columns['listadoComunidadesProveedor'] = [];
                }

                CoreUI.tableData.init();
            
                CoreUI.tableData.addColumn('listadoComunidadesProveedor', 
                function(row, type, val, meta)
                {
                    return `${row.codigo}`;
                },
                "Código", null, 'text-left');

                CoreUI.tableData.addColumn('listadoComunidadesProveedor', 
                function(row, type, val, meta)
                {
                    return `${row.nombre}`;
                },
                "Comunidad", null, 'text-left');

                CoreUI.tableData.addColumn('listadoComunidadesProveedor', 
                function(row, type, val, meta)
                {
                    return `${row.direccion}`;
                },
                "Dirección", null, 'text-left');

                CoreUI.tableData.addColumn('listadoComunidadesProveedor', 
                function(row, type, val, meta)
                {
                    return `${row.provincia}`;
                },
                "Provincia", null, 'text-left');                

                CoreUI.tableData.addColumn('listadoComunidadesProveedor', 
                function(row, type, val, meta)
                {
                    return `${row.administrador}`;
                },
                "Administrador", null, 'text-left');   

                $('#listadoComunidadesProveedor').addClass('no-clicable');
                CoreUI.tableData.render("listadoComunidadesProveedor", "Comunidades", `empresa/${Proveedor.empresaId}/comunidadesasignadas`, null, false, false, null,null,false,null,true);
                
            }  
        }
    }
}

$(() =>{
    if(core.model == 'Proveedor')
    {
        Proveedor.Init();
    }
})