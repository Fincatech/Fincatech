let Stats = {



    Init: function(){

        Stats.Events();

        //  Si hay algún chart en la página procedemos a la renderización del mismo de manera automática
        if($('.chart').length)
        {
            //  Recorremos todos los posibles chart que haya en la página y los vamos inicializando
            $('.chart').each(function(ev)
            {

                let id = $(this).attr('id');
                let type = $(this).attr('data-type');
                let entity = $(this).attr('data-entity');
                let date = $(this).attr('data-date');

                let p = new Promise(async(resolve, reject) =>{
                   let res = await Stats.Controller.InitializeChart( id, type, entity, date );
                   resolve(res);
                });

                p.then((result) =>{

                });

            });
        }

    },

    Controller: {

        InitializeStats: async function()
        {

        },

        InitializeChart: async function(chartId, charType, chartEntity, chartDate)
        {

            //  Llamamos al endpoint correspondiente

            new Chart(document.getElementById(chartId), {
                type: charType,
                data: {
                  labels: ["Social", "Search Engines", "Direct", "Other"],
                  datasets: [{
                    data: [260, 125, 54, 146],
                    backgroundColor: [
                      window.theme.primary,
                      window.theme.success,
                      window.theme.warning,
                      "#dee2e6"
                    ],
                    borderColor: "transparent"
                  }]
                },
                options: {
                  maintainAspectRatio: false,
                  cutoutPercentage: 65,
                }
              })
        }

    },

    Events: function(){


    },

    Facturacion:{

    }

}

document.addEventListener('coreInitialized', function(event){
    window.Chart = Chart;
    Stats.Init();
});