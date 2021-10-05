let dashboard = {

    init: async function()
    {
        //  Recuperamos el listado de comunidades
           await comunidadesCore.listadoDashboard();
        //  Renderizamos el menú lateral
        //    await comunidadesCore.renderMenuLateral();
    }

}

$(()=>{
    dashboard.init();
});