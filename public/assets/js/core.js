
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

          // console.log( "Entidad: " + entidad );
          // console.log( "Field: " + campo );

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

            console.log('Map Data');
            console.log('Acción del modelo: ' + core.actionModel + ' - ' + core.modelId);

            if(fieldName != "id")
              core.Forms.data[fieldName] = $(this).val();

          }else{

            // if(fieldName != "id")
            //   core.Forms.data[entity][fieldName] = $(this).val();

          }

        });

        //  Si se está actualizando se mapea el ID
        if( core.actionModel == 'get' && core.Forms.data['id'] != '')
        {
          console.log("ID: " + core.modelId);
          core.Forms.data['id'] = core.modelId;
        }        

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

        //  Reiniciamos la información a enviar
        core.Forms.data = {};

        //  Mapeamos los datos para poder enviar la info
        core.Forms.mapDataToSave();

        //  Comprobamos la acción del modelo
        switch(actionSave)
        {
          case 'get':
              core.Modelo.Update(core.model, idSave, core.Forms.data);
              break;
          case 'add':
              core.Modelo.Insert(core.model, core.Forms.data );
              break;
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
    },

    /**
     * Inserta un registro en la base de datos mediante restful api
     * @param {*} entidadSave 
     * @param {*} postData 
     */
    Insert: async function(entidadSave, postData)
    {
      await apiFincatech.post(`${entidadSave}/create`, JSON.stringify(postData)).then(async (response) =>
      {

          var responseData = JSON.parse(response);

          // console.log('Resultado inserción1: ' + responseData.data);
          // console.log('Resultado inserción2: ' + responseData.status);
          // console.log('Result status: ' + responseData.status);

          if(responseData.status['response'] == "ok")
          {

            var idInsercion = responseData.id;

            $("body").attr("hs-action", "get");
            $("body").attr("hs-model-id", idInsercion);

            core.actionModel = "get";
            core.modelId = idInsercion;

            CoreUI.Modal.Success("El registro se ha creado correctamente");

          }else{

            //  TODO: Ver cuál es el error en el json
            Modal.Error("No se ha podido guardar por el siguiente motivo:<br><br>" + responseData.status.response);

          }

      });
    },

    /**
     * Elimina un registro de la base de datos y toda la posible información relacionada
     * @param {*} endpoint Nombre del endpoint
     * @param {*} id ID del registro que se va a eliminar
     * @param {*} nombre Nombre para mostrar en el modal
     * @param {*} nombreListadoDOM Nombre del objeto de listado
     * @param {*} titulo (opcional) Título del modal
     * @param {*} mensaje (opcional) Mensaje del modal
     */
    Delete: async function(endpoint, id, nombre, nombreListadoDOM, titulo = null, mensaje = null)
    {
        //  TODO: Posibilidad de personalizar título y mensaje
        Swal.fire({
            title:`¿Desea eliminar el registro y toda la información relacionada?`,
            text: `${nombre}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
                //  Llamamos al endpoint de eliminar
                apiFincatech.delete(endpoint, id).then((result) =>{
                    Swal.fire(
                        'Registro eliminado correctamente',
                        '',
                        'success'
                      );
                      $(`#${nombreListadoDOM}`).DataTable().ajax.reload();
                });
            }
        });
    },

    /**
     * Actualiza el modelo de la entidad deseada
     * @param {*} endpoint 
     * @param {*} id 
     * @param {*} jsonPostData 
     */
    Update: async function(endpoint, id, jsonPostData)
    {

        await apiFincatech.put(`${endpoint}/${id}`, JSON.stringify(jsonPostData)).then(async (response) =>
        {

            var responseData = JSON.parse(response);

            // console.log('Resultado inserción1: ' + responseData.data);
            // console.log('Resultado inserción2: ' + responseData.status);
            // console.log('Result status: ' + responseData.status);

            if(responseData.status['response'] == "ok")
            {

              CoreUI.Modal.Success("El registro se ha actualizado correctamente");

            }else{

              //  TODO: Ver cuál es el error en el json
              Modal.Error("No se ha podido guardar por el siguiente motivo:<br><br>" + responseData.status.response);

            }

        });
    },


  }

};

//  Inicialización del core
$(function(){
    apiFincatech.init();
    core.init();
});