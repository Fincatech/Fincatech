
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

    core.Events();

    // if(core.actionModel == "get" || core.actionModel == "add")
    // {
    //   await core.Forms.init();
    // }

  },

  Events: function()
  {
    //  Botón de guardar
    $("body").on(core.helper.clickEventType, ".btnSaveData", function(){
      core.Forms.Save();
    });
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

  Modal:{
  
    /** Muestra un modal de ok */
    Success: function(texto)
    {
      Swal.fire(
          `${texto}`,
          '',
          'success'
        );    
    },

    /** Muestra un modal de error */
    Error: function(texto)
    {
          Swal.fire(
          `${texto}`,
          '',
          'error'
        );  
    },

  },

  /** Formularios de datos */
  Forms:
  {

    data: null,

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

              var entidadCombo = $(this).attr("hs-list-entity");
              var keyField = $(this).attr("hs-list-field");
              var keyValue = $(this).attr("hs-list-value");
              var elDOM = $(this).prop("id");

              var keyOriginField = $(this).attr("hs-field");
              var keyOriginValue = $(this).attr("hs-value");

              core.Forms.getSelectData( entidadCombo, keyField, keyValue, keyOriginField, keyOriginValue, elDOM );

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

          console.log( "Entidad: " + entidad );
          console.log( "Field: " + campo );

          var valor = core.Modelo.entity[ entidad ][0][campo];

          //  Leemos el modelo y el nombre del campo del que se va a recuperar la información
          //  y mapeamos el valor obtenido en el fetch

          // console.log( core.Modelo.entity[ entidad ] );
          if($(this).hasClass("select-data"))
          {
            //  Leemos el id
            var id = $(this).attr("id");
            //  Validamos que exista el valor en el modelo antes de mapear
            $(`#${id} option[value=${valor}]`).attr('selected','selected');
          }else{
            $(this).val( valor );
          }

        });
      }


    },

    /** Mapea la información de la pantalla a la propiedad data para guardar */
    mapDataToSave: function()
    {

        core.Forms.data = {};

        var modeloPrincipal = $("body").attr("hs-model");

        //  Recorremos todos los campos del formulario
        $("body .form-data .data").each(function(){
        
          var fieldName = $(this).attr("hs-field");
          var entity = $(this).attr("hs-entity");

          if(entity == modeloPrincipal)
          {

            if(fieldName != "id")
              core.Forms.data[fieldName] = $(this).val();

          }else{

            if(fieldName != "id")
              core.Forms.data[entity][fieldName] = $(this).val();

          }

        });

        console.log( JSON.stringify(core.Forms.data) );

    },

    /** Carga los datos en el combo especificado y la entidad que se va a recuperar */
    getSelectData: async function(entidad, keyField, keyValue, keyOriginField, keyOriginValue, elementoDOM)
    {
        //  Vaciamos el combo
        $("body #" + elementoDOM).html("");

        //  Recuperamos los registros que correspondan según la entidad
        await apiFincatech.get(`${entidad}/list`).then(async (data) =>
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

        });

    },

    /** Crea o actualiza un registro */
    Save: async function()
    {
      //  Comprobamos la acción primero para saber si es un update o una inserción
      var entidadSave = $("body").attr("hs-model");
      var idSave = $("body").attr("hs-model-id");
      var actionSave = $("body").attr("hs-action");

      if(idSave != "" && actionSave == "get")
      {
          //  Actualización de registro
      }

      //  Creación 
      if(idSave == "" && actionSave == "add")
      {
        //  Reiniciamos la información a enviar
        core.Forms.data = {};

        //  Mapeamos los datos para poder enviar la info
        core.Forms.mapDataToSave();

        await apiFincatech.post(`${entidadSave}/create`, JSON.stringify(core.Forms.data)).then(async (data) =>
        {

            var htmlOutput = "";
            var result = JSON.parse(data);
            responseData = result.data;

            console.log('Resultado inserción: ' + responseData);

            if(responseData.status.response == "ok")
            {

              var idInsercion = responseData.id;

              $("body").attr("hs-action", "get");
              $("body").attr("hs-id", idInsercion);

              Modal.Success("El SPA ha sido dado de alta satisfactoriamente");

            }else{

              //  TODO: Ver cuál es el error en el json
              Modal.Error("No se ha podido guardar por el siguiente motivo:<br><br>" + responseData.status.response);

            }


        });
      }



    },

    /** Valida el formulario y devuelve la respuesta correspondiente */
    Validate: function()
    {
    
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