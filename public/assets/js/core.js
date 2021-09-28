
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';
let baseURL = '/fincatech/';
var role = null;

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

  /** Módulo de gestión de ficheros */
  Files:{
  
    Fichero: Object(),
    file: null,

    init: async function()
    {
        core.Files.Fichero.entidad = null;
        core.Files.Fichero.entidadId = null;
        core.Files.Fichero.nombre = null;
        core.Files.Fichero.base64 = null;
        core.Files.events();
    },

    events: function()
    {
      if(!$('#ficheroadjuntar').length)
        return;

      const fileInput = document.getElementById('ficheroadjuntar')
      // fileInput.addEventListener('change', readFichero, false);;

      fileInput.addEventListener('change', (e) => {

          // get a reference to the file
          const file = e.target.files[0];
          if(file !== undefined)
          {
            console.log(file);
            // encode the file using the FileReader API
            const reader = new FileReader();
            reader.onloadend = () => {

                // use a regex to remove data url part
                const base64String = reader.result;
                //.result
                //     .replace('data:', '')
                //     .replace(/^.+,/, '');

                core.Files.Fichero.base64 = base64String;
                core.Files.Fichero.entidad = core.model;

                var fullPath = document.getElementById('ficheroadjuntar').value;
                if (fullPath) {
                    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                    var filename = fullPath.substring(startIndex);
                    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                        filename = filename.substring(1);
                    }
                    core.Files.Fichero.nombre = filename;
                    console.log('Nombre: ' + filename);
                }          
            };
            reader.readAsDataURL(file);
          }else{
            core.Files.Fichero.base64 = '';
            core.Files.Fichero.nombre = '';
          }  

      });

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
              var insertarOpcionSeleccionar = false;

              var entidadCombo = $(this).attr("hs-list-entity");
              var keyField = $(this).attr("hs-list-field");
              var keyValue = $(this).attr("hs-list-value");
              var elDOM = $(this).prop("id");

              var keyOriginField = $(this).attr("hs-field");
              var keyOriginValue = $(this).attr("hs-value");

              if($(this).attr('hs-seleccionar') !== undefined)
              {
                insertarOpcionSeleccionar = $(this).attr('hs-seleccionar');              
              }

              core.Forms.getSelectData( entidadCombo, keyField, keyValue, keyOriginField, keyOriginValue, elDOM, insertarOpcionSeleccionar );

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
            if($('#password').length)
            {
              $('#password').val('');
            }
            
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
          if( campo != 'password' )
          {
          //  FIXME: Arregla el if para evitar tantas anidaciones
              if($(this).hasClass("select-data"))
              {
                //  Leemos el id
                var id = $(this).attr("id");
                //  Validamos que exista el valor en el modelo antes de mapear
                if(id !== undefined && id !== -1)
                {
                  $(`#${id} option[value=${valor}]`).attr('selected','selected');
                }else{
                
                }
              }else{

                //  ¿ Es un checkbox ? 
                if($(this).hasClass("form-check-input"))
                {
                  console.log('Valor checkbox campo : ' + campo + ' - ' +  valor);
                  if(valor == 1)
                  {
                    console.log('Intentando checar')  ;
                    $(this).attr('checked', true);
                  }else{
                  console.log('Intentando checar false')  ;
                    $(this).attr('checked', false);
                  }
                }else{
                  $(this).val( valor );
                }

              }
          }else{
            $(this).val('');
          }
        });

            //  Inicializamos el selectpicker
            $('.selectpicker').select2({
              theme: 'bootstrap4',           
            });
            $('.selectpicker').each(function()
            {
              $(this).trigger('change');
            });
      }



    },

    /** Devuelve el valor de un campo según su tipo */
    getValueByTipoCampo: function(e)
    {
        //  Comprueba si es un select
            if( e.is('select'))
              return ( (e.val() == null || e.val() == '') ? -1 : e.val() ); 

        //  Comprueba si es un checkbox
            if( e.is('checkbox') || e.attr('type') == 'checkbox')
              return ( e.is(':checked') ? 1 : 0 );

        //  Si es otro tipo de campo devuelve el valor directamente
            return e.val();  
    },

    /** Mapea la información de la pantalla a la propiedad data para guardar */
    mapDataToSave: function()
    {

        core.Forms.data = {};

        //  Recorremos todos los campos del formulario
        $("body .form-data .data").each(function(){
        
          var fieldName = $(this).attr("hs-field");
          var entity = $(this).attr("hs-entity");
          var entityRelated = null;

          if( $(this).attr("hs-entity-related") !== undefined && 
              $(this).attr("hs-entity-related") !== null )
          {
            entityRelated = $(this).attr("hs-entity-related");
          }

          if( (entity == core.model) && entityRelated == null)
          {
            
            if(fieldName != "id")
              core.Forms.data[fieldName] = $(this).val();

            core.Forms.data[fieldName] = core.Forms.getValueByTipoCampo( $(this) );

          }else{

            if(fieldName != "id")
            {
              //  Si la entidad no existe, debemos inicializar
              if( core.Forms.data[entityRelated] === undefined )
                core.Forms.data[entityRelated] = {};

              core.Forms.data[entityRelated][fieldName] = core.Forms.getValueByTipoCampo( $(this) );

            }

          }

        });

        //  Si se está actualizando se mapea el ID
        if( core.actionModel == 'get' && core.Forms.data['id'] != '')
          core.Forms.data['id'] = core.modelId;  

    },

    /** Carga los datos en el combo especificado y la entidad que se va a recuperar */
    getSelectData: async function(entidad, keyField, keyValue, keyOriginField, keyOriginValue, elementoDOM, insertarOpcionSeleccionar)
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

            htmlOutput = '<option value="-1" disabled selected="selected">SELECCIONE UNA OPCIÓN</option>';
            $("body #" + elementoDOM).append(htmlOutput);

            // console.log('El: ' + responseData[entidad][][keyValue]);
            for(x = 0; x < responseData[entidad].length; x++)
            {
                var valueId = responseData[entidad][x][keyValue];
                var value = responseData[entidad][x][keyField];
                htmlOutput = `<option value="${valueId}">${value}</option>`;
                $("body #" + elementoDOM).append(htmlOutput);
            }

            $('body #' + elementoDOM).select2({
              theme: 'bootstrap4'
            });

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
              console.log(core.Forms.data);
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

    getAll: async function(params)
    {
      var data = {};
      if(params !== undefined && params !== null)
      {
        data = params;
      }
      // await apiFincatech.get(`${core.model.toLowerCase()}/list`).then(async (data)=>
      await apiFincatech.post(`${core.model.toLowerCase()}/list`, data).then(async (data)=>
      {
        result = JSON.parse(data);
        responseStatus = result.status;
        responseData = result.data;
      }).catch(function(error){
      });
    },

    /**
     * Inserta un registro en la base de datos mediante restful api
     * @param {*} entidadSave 
     * @param {*} postData 
     */
    Insert: async function(entidadSave, postData)
    {
      await apiFincatech.post(`${entidadSave}/create`, postData).then(async (response) =>
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
        //  CHECKME: Posibilidad de personalizar título y mensaje
        Swal.fire({
            title: (titulo == null ? `¿Desea eliminar el registro y toda la información relacionada?` : titulo),
            text:  (mensaje == null ? `${nombre}` : mensaje),
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

        await apiFincatech.put(`${endpoint}/${id}`, jsonPostData).then(async (response) =>
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
            await apiFincatech.post('checklogin', datos ).then( response => {
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

  },


};


//  Inicialización del core
$(function()
{

    apiFincatech.init();
    core.init();
    $('.loading').hide();
});