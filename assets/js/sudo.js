let sudoCore = {

    comunidades: Object(),

    init: function()
    {
        //  Bindeamos los eventos de los diferentes botones de comunidades
        this.events();
    },

    // Gestión de eventos
    events: function()
    {

        $('body').on(core.helper.clickEventType, '.btnVerComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.verModalComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

        $('body').on(core.helper.clickEventType, '.btnEliminarComunidad', (evt)=>{
            evt.stopImmediatePropagation();
            comunidadesCore.eliminarComunidad( $(evt.currentTarget).attr('data-id'), $(evt.currentTarget).attr('data-nombre') );
        });

    },

    // /** Elimina una comunidad previa confirmación */
    // eliminarComunidad: function(idComunidad, nombreComunidad)
    // {
    //     Swal.fire({
    //         title:`¿Desea eliminar la comunidad:<br>${nombreComunidad}?`,
    //         text: "Se va a eliminar la comunidad y toda la información asociada",
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Eliminar'
    //       }).then((result) => {
    //         if (result.isConfirmed) {
    //           Swal.fire(
    //             'Comunidad eliminada correctamente',
    //             '',
    //             'success'
    //           )
    //         }
    //     });
    // },

    /** Muestra un modal con la info de la comunidad */
    // verModalComunidad: function(idComunidad)
    // {

    // },

    // renderMenuLateral: async function()
    // {
        
    //     await apiFincatech.getView('comunidades', 'listadomenulateral', comunidadesCore.comunidades, '.navComunidades')
    //     .then(async (data)=>{
    //         //$('.statscomunidades .total').html(comunidadesCore.comunidades.total);
    //         feather.replace();
    //     });

    // },

    /** Recupera el listado de comunidades en el dashboard */
    // listadoDashboard: async function()
    // {

    //     await comunidadesCore.getAll().then(async (data)=>{
    //            $('.statscomunidades .total').html(comunidadesCore.comunidades.total);
    //            feather.replace();
    //     });
  
    // },

    /** Recupera los datos de las comunidades desde el ws */
    getAll: async function()
    {
        
        await apiFincatech.get('comunidad/list').then(async (data)=>
        {
            result = JSON.parse(data);
            responseStatus = result.status;
            responseData = result.data;
            console.log(responseData);
            comunidadesCore.comunidades = responseData.Comunidad;
            comunidadesCore.comunidades.total = comunidadesCore.comunidades.length;
        });

    }

}

$(()=>{
    sudoCore.init();
})