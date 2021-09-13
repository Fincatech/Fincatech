
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';
let baseURL = '/fincatech/';

let core =
{
  model: null,
  actionModel: null,
  modelId: null,

  init: async function()
  {

    core.model = $("body").attr("hs-model");
    core.actionModel = $("body").attr("hs-action");
    core.modelId = $("body").attr("hs-model-id");

    switch(core.actionModel)
    {
      case "get":
      case "add":
        await core.Forms.init();
        break;
      case "list":
        await core.Modelo.getAll();
        break;
    }

    // if(core.actionModel == "get" || core.actionModel == "add")
    // {
    //   await core.Forms.init();
    // }

  },

  helper:
  {
    clickEventType : ((document.ontouchstart!==null)?'click':'touchstart'),

    sortList: function(selector) 
    {
      $(selector).find("option").sort(function(a, b) {
          return(Number(a.innerHTML) - Number(b.innerHTML));
      }).each(function(index, el) {
          $(el).parent().append(el);
      });
    }

  },

  /** Formularios de datos */
  Forms:
  {

    init: async function()
    {
      //  Cargamos el esquema para la entidad
      await core.Forms.getSchema().then( (result)=>
      {
        // console.log(result);
        core.Modelo.schema = result;

        //  Recuperamos los valores de los posible combos que haya en la pantalla
        if($("body .form-data .select-data").length)
        {

          $("body .form-data .select-data").each(function()
          {

              var entidadCombo = $(this).attr("hs-entity");
              var keyField = $(this).attr("hs-field");
              var keyValue = $(this).attr("hs-value");
              var elDOM = $(this).prop("id");

              core.Forms.getSelectData( entidadCombo, keyField, keyValue, elDOM );

          });

        }

        // Si hay un id informado recuperamos la entidad desde el endpoint
        if(core.modelId != "")
        {
           apiFincatech.get(core.model.toLowerCase() + "/" + core.modelId).then( (result) =>{

            //  Mapeamos los datos en el formulario
            core.Modelo.entity[core.model] = JSON.parse(result)["data"][core.model];
            // console.log(core.Modelo.entity[core.model]);
            core.Forms.mapData();
          });

        }

      });

    },

    /** Recuperamos el modelo para la entidad que se está cargando */
    getSchema: async function()
    {
      return apiFincatech.get(core.model.toLowerCase() + "/schema").then((result)=>{
        core.Modelo.schema = result;
      });
    },

    /** Mapeamos la información devuelta por el endpoint con los datos del formulario */
    mapData: async function()
    {
      //  Comprobamos que esté declarado el formulario
      if($("body .form-data").length)
      {
        //  .form-data -> Dentro de este container se encuentran todos los datos
        $("body .data").each( function(){

          var entidad = $(this).attr('hs-entity') ;
          var campo = $(this).attr('hs-field') ;
          var valor = core.Modelo.entity[ entidad ][0][campo];

          //  Leemos el modelo y el nombre del campo del que se va a recuperar la información
          //  y mapeamos el valor obtenido en el fetch
          // console.log( "Entidad: " + entidad );
          // console.log( "Field: " + campo );
          // console.log( "Valor: " + core.Modelo.entity[ entidad ][0][campo] );

          // console.log( core.Modelo.entity[ entidad ] );
          $(this).val( valor );

        });
      }


    },

    /** Carga los datos en el combo especificado y la entidad que se va a recuperar */
    getSelectData: async function(entidad, keyField, keyValue, elementoDOM)
    {
        //  Vaciamos el combo
        $("body #" + elementoDOM).html("");

        //  Recuperamos los registros que correspondan según la entidad
        await apiFincatech.get("provincia/list").then(async (data) =>
        {

            var htmlOutput = "";
            var result = JSON.parse(data);
            responseData = result.data;

            // console.log('El: ' + responseData[entidad][][keyValue]);
            for(x = 0; x < responseData[entidad].length; x++)
            {
                var valueId = responseData[entidad][x][keyValue];
                var value = responseData[entidad][x][keyField];
                htmlOutput = `<option value="${valueId}">${value}</option>`;
                $("body #" + elementoDOM).append(htmlOutput);
            }
            core.helper.sortList("#" + elementoDOM);
        });

    },

  },

  Modelo: {

    entity: Object(),
    schema: Object(),

    getAll: async function()
    {
      await apiFincatech.get(`${core.model.toLowerCase()}/list`).then(async (data)=>
      {
        result = JSON.parse(data);
        responseStatus = result.status;
        responseData = result.data;
        // console.log(responseData);
      });
    }


  }

};

//  Inicialización del core
$(function(){
    apiFincatech.init();
    core.init();
});