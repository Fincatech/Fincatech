let dashboard = {

    init: async function()
    {
        //  Renderizamos el menú lateral
           await comunidadesCore.renderMenuLateral();
    }

}

$(()=>{
    dashboard.init();
});