let adminFincas = {

    Init: async function()
    {

        comunidadesCore.renderMenuLateral();

        //  Título del módulo
        if($('.titulo-modulo').length && core.model == 'Comunidad')
        {
        }

    },

    Events: function()
    {

    }

}

$(()=>
{
    adminFincas.Init();
})