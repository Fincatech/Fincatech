
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';
let baseURL = '/fincatech/';

let Constantes = {

   CargaComunidades: `
   <p class="text-center mb-0 mt-1"><i class="bi bi-file-earmark-arrow-up" style="font-size:60px;"></i></p>
   <h2 class="swal2-title" id="swal2-title" style="display: block;">Carga automática de comunidades</h2>
   <p class="mt-3" style="font-size: 14px;">Seleccione el fichero excel desde el que desea realizar la carga de comunidades de forma automática</p>
   <p style="font-size: 14px;">Sólo se permiten ficheros con extensión xls o xlsx</p>`
}

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

    if(core.model === 'Login' || core.model === 'Register')
    {
      //  Inicializamos la capa de seguridad
          core.Security.init();
    }else{
    
      switch(core.actionModel)
      {
        case "get":
        case "add":
          await core.Forms.init();
          break;
        case "list":
          if(core.model != 'Dashboard')
            await core.Modelo.getAll();
          break;
      }    

    }

    core.Events();

  },

  Events: function()
  {
      //  Logout
      $("body").on(core.helper.clickEventType, ".btnLogout", function()
      {
        core.Security.logout();
      });

      //  Botón de guardar
      $("body").on(core.helper.clickEventType, ".btnSaveData", function(){
        core.Forms.Save();
      });

      //  Carga de comunidades
      $('body').on(core.helper.clickEventType, '.btnCargarComunidadesExcel', async function()
      {
        const { value: file } = await Swal.fire({
          // title: 'Carga automática de comunidades',
          html: Constantes.CargaComunidades,
          footer: '<a href="javascript:void(0);"><i class="bi bi-file-earmark-arrow-down"></i> Descargue la plantilla desde este enlace</a>',
          showCancelButton: true,
          grow: 'row',
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#dc3545',
          cancelButtonText: 'Cancelar',
          confirmButtonText: 'Procesar',
          reverseButtons: true,
          showCloseButton: true,
          input: 'file',
          inputAttributes: {
            'accept': 'image/*',
            'aria-label': 'Upload your profile picture'
          }
        })

        if (file) {
          const reader = new FileReader()
          reader.onload = (e) => {
            Swal.fire({
              title: 'Your uploaded picture',
              imageUrl: e.target.result,
              imageAlt: 'The uploaded picture'
            })
          }
          reader.readAsDataURL(file)
        }
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

            $('.selectpicker').select2({
              theme: 'bootstrap4',
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

            //  Inicializamos el selectpicker
            $('.selectpicker').select2({
              theme: 'bootstrap4',
            });
            // $('.selectpicker').each(function()
            // {
            //   $(this).trigger('change');
            // });

      }



    },

    /** Mapea la información de la pantalla a la propiedad data para guardar */
    mapDataToSave: function()
    {

        core.Forms.data = {};

        //  Recorremos todos los campos del formulario
        $("body .form-data .data").each(function(){
        
          var fieldName = $(this).attr("hs-field");
          var entity = $(this).attr("hs-entity");

          if(entity == core.model)
          {
            // console.log('Acción del modelo: ' + core.actionModel + ' - ' + core.modelId);

            //TODO: MEJORAR LA LÓGICA METIÉNDOLO EN UN SWITCH PARA EL TIPO DE INPUT

            if(fieldName != "id")
            {
              core.Forms.data[fieldName] = $(this).val();
            }

            if( $(this).is('select'))
            {
              console.log('Es un combo!');
              console.log('Valor del select: ' + $(this).val());
            }

            if( $(this).attr('type') === 'checkbox')
            {
              if( $(this).is(':checked'))
              {
                core.Forms.data[fieldName] = $(this).val();
              }else{
                core.Forms.data[fieldName] = '';
              }

            }


          }else{

            if(fieldName != "id")
              core.Forms.data[entity][fieldName] = $(this).val();

          }

        });

        //  Si se está actualizando se mapea el ID
        if( core.actionModel == 'get' && core.Forms.data['id'] != '')
          core.Forms.data['id'] = core.modelId;  

    },

    /** Carga los datos en el combo especificado y la entidad que se va a recuperar */
    getSelectData: async function(entidad, keyField, keyValue, keyOriginField, keyOriginValue, elementoDOM)
    {
        //  Vaciamos el combo
        $("body #" + elementoDOM).html("");

        //  Recuperamos los registros que correspondan según la entidad
        await apiFincatech.get(`${entidad}/list?target=cbo`).then(async (data) =>
        {

            var htmlOutput = "";
            var result = JSON.parse(data);
            responseData = result.data;

            //  Comprobamos si el valor viene de una entidad asociada a la entidad principal
            //  Para eso buscamos si hay un . en el string del keyField
            if(keyField.indexOf('.') >= 0)
            {
              //  Actualizamos el valor de entidad para coger la que corresponde a la relación
              var entidadSeleccionada = keyField.split(".");
              entidad = entidadSeleccionada[0];
              keyField = entidadSeleccionada[1];
            }

            //  Comprobamos lo mismo pero para el campo valor
            //  TODO: Evaluar si debe ir dentro del for por el hecho de ser un valor y no el key 
            if(keyValue.indexOf('.') >= 0)
            {
              //  Actualizamos el valor de entidad para coger la que corresponde a la relación
              var entidadSeleccionada = keyValue.split(".");
              entidad = entidadSeleccionada[0];
              keyValue = entidadSeleccionada[1];
            }

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
              core.Modelo.Update(core.model.toLowerCase(), idSave, core.Forms.data);
              break;
          case 'add':
              core.Modelo.Insert(core.model.toLowerCase(), core.Forms.data );
              break;
        }

    },

    /** Valida el formulario y devuelve la respuesta correspondiente */
    Validate: function()
    {
      var result = true;

      if( $('body .form-required').length == 0)
        return true;

      //  Recorre todos los elementos del dom que sean susceptibles de ser validados
      $('body .form-required').each(function()
      {
        if($(this).val() == '')
        {
          result = false;
        }
      });

      return result;

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

            var idInsercion = responseData.data['id'];

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


  },

  Security: {

      init: function(){
        this.events();
        core.model = 'Login';
        if($('#email').length)
          $('#email').focus();

        $('#email').val('');
        $('#password').val('');
        $('form').attr('autocomplete', 'off');
        $('input').attr('autocomplete', 'off');
      },

      events: function(){
        $('body').on(core.helper.clickEventType, '.btnAuthenticate', function(e)
        {
          //  Validamos los campos obligatorios
              if( core.Forms.Validate() )
              {
                core.Forms.mapDataToSave();
                core.Security.checkLogin( core.Forms.data );
              }else{
                CoreUI.Modal.Error("Debe proporcionar todos los datos obligatorios");
              }
        });

        $("input").keyup(function(event) {
          if (event.key === 13) {
              $(".btnAuthenticate").trigger('click');
          }else if (event.keyIdentifier === 13)
          {
            $(".btnAuthenticate").trigger('click');
          }else if (event.keyCode === 13)
          {
            $(".btnAuthenticate").trigger('click');
          }
        });        
        
      },

      /** Logout del sistema */
      logout: async function()
      {
          await apiFincatech.get('logout' ).then( response => {
              respuesta = JSON.parse(response);
              if(respuesta.data.logout == 'ok')
              {
                //  Logout correcto
                CoreUI.Modal.Success('La sesión se ha cerrado correctamente', 'Fincatech', function(){
                  window.location.href = baseURL + 'login';
                });
              }

          });
      },

      /** Comprueba el login contra el endpoint según los datos proporcionados */
      checkLogin: async function( datos )
      {
        //  Comprobamos contra el endpoint de login si el usuario tiene acceso
            await apiFincatech.post('checklogin', JSON.stringify(datos) ).then( response => {
              respuesta = JSON.parse(response);
              if(respuesta.data.check === false)
              {
                //  Login erróneo
                CoreUI.Modal.Error("El e-mail y/o contraseña proporcionada es incorrecto");
              }else{
                //  Login correcto
                CoreUI.Modal.Success('Login Correcto', 'Fincatech', function(){
                  window.location.href = 'dashboard';
                });
              }

            });
        //  En caso contrario mostramos el mensaje de error devuelto por el api

      }

  }

};


//  Inicialización del core
$(function()
{

    apiFincatech.init();
    core.init();

});