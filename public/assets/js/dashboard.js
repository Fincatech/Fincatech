let dashboard = {

    init: async function()
    {
        
        //  Recuperamos el listado de comunidades
        if(core.model == 'Dashboard')
        {
           await comunidadesCore.listadoDashboard();
        }

    }

}

$(()=>{
    // dashboard.init();
});

document.addEventListener('coreInitialized', function(event){
    dashboard.init();
});