
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';
let baseURL = '/fincatech/';
var role = null;
var showingLoadComunidades = false;

let Constantes = {

   CargaComunidades: `
   <div class="row">
        <div class="col-12 text-center text-uppercase align-self-center">
          <p class="m-0" style="display: block; font-size: 18px;"> Carga automática de comunidades</p>
        </div>
    </div>
    <div class="row mb-2 wrapperInformacion">
      <div class="col-12">
          <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el administrador de fincas al que asignar las comunidades</p>
          <p class="mt-3 text-justify" style="font-size: 14px;">2. Seleccione el fichero excel desde el que desea realizar la carga de comunidades de forma automática</p>
          <p class="m-0 text-center" style="font-size: 14px;">
            <a href="${baseURL}public/storage/plantilla_comunidades_fincatech.xlsx" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> Descargue la plantilla desde este enlace</a>
          </p>
          <p class="mt-3 text-left text-justify" style="font-size: 14px;">3. Una vez seleccionado, clique sobre el botón PROCESAR</p>
      </div>
    </div>
    <!-- Selector de administrador -->
    <div class="row mb-4 wrapperSelectorAdministrador">
      <div class="col-12 text-left"><form>
      <label for="administradorCargaId" class="mb-2">Seleccione el Administrador</label>
      <select id="administradorCargaId" name="administradorCargaId" class="custom-select data form-control selectpicker mt-2" data-live-search="true" hs-entity="Administrador" hs-field="usuarioId" hs-list-entity="Administrador" hs-list-field="Usuario.nombre" hs-list-value="Usuario.id"></select></form>
      </div>  
    </div>

    <div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">
      <div class="col-12">  
          <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
              <div class="col-2 align-self-center h-100 text-center">
                  <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
              </div>
              <div class="col-10 pl-0 align-self-center">
                  <input accept=".xls, .xlsx" class="form-control form-control-sm ficheroAdjuntarExcel border-0" hs-fichero-entity="Notasinformativas" id="ficheroAdjuntarExcel" type="file">
              </div>       
          </div>
          <span class="pb-3 d-block text-center pt-2" style="font-size: 13px;">Sólo se permiten ficheros con extensión xls o xlsx</span>    
          
          <!-- Mensaje de error --> 
          <div class="wrapperMensajeErrorCarga row text-light p-3" style="display: none; font-size: 14px;">
              <div class="col-12 bg-danger p-3 rounded shadow-neumorphic">
                <p class="mensaje"></p>
              </div>
          </div>          

          <!-- Botón de iniciar proceso -->
          <div class="row mt-3">
            <div class="col-12">
              <a href="javascript:void(0);" class="btn d-block btn-success bntProcesarImportacion pt-3 pb-3">PROCESAR</a>
            </div>
          </div>
      </div>
    </div>

    <div class="wrapperProgresoCarga row" style="display: none;">
      <div class="col-12">
          <label class="text-center mb-2 mt-3">Procesando fichero</label>
          <div class="progress mb-3" style="height: 30px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">Animated</div>
          </div>
          <label class="progresoCarga">(n de y procesados)</label>
          <div class="row mt-3 btnCerrarProceso" style="display: none;">
            <div class="col-12">
              <a href="javascript:swal.close();" class="btn btn-success">OK</a>
            </div>
          </div>
      </div>
    </div>
    `,

   CargaDocumento: `
   <div class="row">
        <div class="col-12 text-center text-uppercase align-self-center">
          <p class="m-0" style="display: block; font-size: 18px;"> Carga de documento</p>
        </div>
    </div>
    <div class="row mb-2 wrapperInformacion">
      <div class="col-12">
          <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el fichero que desea adjuntar al requerimiento</p>
          <p class="mt-3 text-justify" style="font-size: 14px;">2. Presione el botón <strong>Adjuntar documento</strong></p>
      </div>
    </div>
    <div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">
      <div class="col-12">  
          <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
              <div class="col-2 align-self-center h-100 text-center">
                  <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
              </div>
              <div class="col-10 pl-0 align-self-center">
                  <input accept=".pdf, .doc, .docx" class="form-control form-control-sm ficheroAdjuntar border-0" hs-fichero-entity="Documental" id="ficheroadjuntar" name="ficheroadjuntar" type="file">
              </div>       
          </div>
          <span class="pb-3 d-block text-center pt-2" style="font-size: 13px;">Sólo se permiten ficheros con extensión pdf, doc o docx</span>    
          
          <!-- Mensaje de error --> 
          <div class="wrapperMensajeErrorCarga row text-light p-3" style="display: none; font-size: 14px;">
              <div class="col-12 bg-danger p-3 rounded shadow-neumorphic">
                <p class="mensaje"></p>
              </div>
          </div>          

          <!-- Botón de iniciar proceso -->
          <div class="row mt-3">
            <div class="col-12">
              <a href="javascript:void(0);" class="btn d-block btn-success bntUploadDocumento pt-3 pb-3">Adjuntar documento</a>
            </div>
          </div>
      </div>
    </div>
    `,

    AsignacionEmpresa: `
    <div class="row">
        <div class="col-12 text-center text-uppercase align-self-center">
          <p class="m-0" style="display: block; font-size: 18px;"> Asociar empresa externa a comunidad</p>
        </div>
    </div>
    <div class="row mb-2 wrapperInformacion">
      <div class="col-12">
          <p class="mt-3 text-justify" style="font-size: 14px;">Para asignar una empresa, puede buscar por nombre, e-mail o CIF/NIF en el listado de empresas que tiene en pantalla.</p>
          <p class="mt-3 text-justify" style="font-size: 14px;">Si no encuentra la empresa que desea asociar, puede crear una nueva clicando sobre el botón CREAR NUEVA EMPRESA. Una vez creada la empresa, ésta será asignada automáticamente a la comunidad actual.</p>
      </div>
    </div>

    <!-- Listado simple de empresas del sistema-->

    <div class="row mb-4 wrapperSelectorEmpresa">

      <div class="col-12 text-left">

          <div class="card-body shadow-inset rounded-lg border mb-1 border-white space-between">

          <!-- Empresas -->

              <div class="row flex-grow-1">
                  <div class="col-12">

                      <div class="card">

                          <div class="card-header pl-0 pt-0"><!--headerListado-->

                              <div class="row">

                                  <div class="col-12 col-md-9">
                                      <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-shop pr-2"></i> Empresas disponibles</h5>
                                  </div>
                      
                              </div>

                          </div>

                          <div class="card-body">

                              <table class="table table-hover my-0 hs-tabla w-100" name="listadoSimpleEmpresas" id="listadoSimpleEmpresas" data-model="Empresa">
                                  <thead></thead>
                                  <tbody></tbody>
                              </table>

                          </div>

                      </div>

                  </div>
              </div>

          </div>

      </div>  

    </div>

    <div class="form-group row mb-2 justify-content-center wrapperCrearNuevaEmpresa">
      <div class="col-12">           
          <!-- Botón de crear empresa y asignar a comunidad -->
          <div class="row mt-3">
            <div class="col-12 text-center">
              <a href="javascript:void(0);" class="btn btn-success bntCrearNuevaEmpresaCAE pt-1 pb-1">CREAR NUEVA EMPRESA</a>
            </div>
          </div>
      </div>
    </div>
    `,

    CargaDocumentoRGPD: `
    <div class="row">
         <div class="col-12 text-center text-uppercase align-self-center">
           <p class="m-0" style="display: block; font-size: 18px;"> Carga de documento</p>
         </div>
     </div>
     <div class="row mb-2 wrapperInformacion">
       <div class="col-12">
           <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el fichero que desea adjuntar</p>
           <p class="mt-3 text-justify" style="font-size: 14px;">2. Presione el botón <strong>Adjuntar documento</strong></p>
       </div>
     </div>
     <div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">
       <div class="col-12">  
           <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
               <div class="col-2 align-self-center h-100 text-center">
                   <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
               </div>
               <div class="col-10 pl-0 align-self-center">
                   <input accept=".pdf, .doc, .docx" class="form-control form-control-sm ficheroAdjuntar border-0" hs-fichero-entity="Documental" id="ficheroadjuntar" name="ficheroadjuntar" type="file">
               </div>       
           </div>
           <span class="pb-3 d-block text-center pt-2" style="font-size: 13px;">Sólo se permiten ficheros con extensión pdf, doc o docx</span>    
           
          <div class="row form-group text-left">
            <div class="col-12">
              <label>Título</label>
              <input type="text" class="form-control w-100 tituloDocumentoRGPD mt-2" id="tituloDocumentoRGPD" maxlength="40" name="tituloDocumentoRGPD">
            </div>
          </div>
          <div class="row form-group text-left mt-2">
            <div class="col-12">
              <label>Descripción</label>
              <textarea class="form-control w-100 observacionesDocumentoRGPD shadow-inset border-0 mt-2" id="observacionesDocumentoRGPD" rows="3" name="observacionesDocumentoRGPD"></textarea>
            </div>
          </div>          
           <!-- Mensaje de error --> 
           <div class="wrapperMensajeErrorCarga row text-light p-3" style="display: none; font-size: 14px;">
               <div class="col-12 bg-danger p-3 rounded shadow-neumorphic">
                 <p class="mensaje"></p>
               </div>
           </div>          
 
           <!-- Botón de adjuntar documento -->
           <div class="row mt-3">
             <div class="col-12">
               <a href="javascript:void(0);" class="btn d-block btn-success bntUploadDocumentoRGPD pt-3 pb-3">Adjuntar documento</a>
             </div>
           </div>
       </div>
     </div>
     `,

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

    //  Recuperamos la info del usuario
        core.Security.getUserInfo();

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
        core.Forms.Save( false );
      });

      //  Carga de comunidades
      $('body').on(core.helper.clickEventType, '.btnCargarComunidadesExcel', async function()
      {
        const { value: file } = await Swal.fire({
          title: '',
          html: Constantes.CargaComunidades,
          showCancelButton: false,
          showConfirmButton: false,
          // grow: 'row',
          showCloseButton: true,
          didOpen: function()
          {
            //  Indicamos que el file debe ser atachado el del modal que acaba de mostrarse para poder interactura con él
            showingLoadComunidades = true;

            //  Inicializamos el componente de ficheros
            core.Files.init();

            //  Cargamos la lista de administradores de fincas disponibles
            core.Forms.getSelectData( 'Administrador', 'Usuario.nombre', 'Usuario.id', 'usuarioId', $('#administradorCargaId').attr("hs-value"), $('#administradorCargaId').prop("id"), false ).then(()=>{
              $('#administradorCargaId').select2({
                dropdownParent: $('.swal2-container')
              });
            });

            //  Bindeamos el evento del botón procesar importación
            $('body').on(core.helper.clickEventType, '.bntProcesarImportacion', function()
            {
              comunidadesCore.Import.importarComunidades();
            });

          }

        });

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
// console.log(file);
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

    isExcelFile: function(idDOMInputFile)
    {

        var resultado = false;
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xlsx|.xls)$/;  
        /*Checks whether the file is a valid excel file*/  
        if (regex.test($(`#${idDOMInputFile}`).val().toLowerCase())) {  
          return true;
        }else{
          return false;
        }

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
        core.Forms.initializeSelectData();

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
            //  Pintamos el titulo según el campo que hemos establecido en el componente correspondiente

            //  Si no está definido, entonces dejamos por defecto el que viene
                if(CoreUI.tituloModulo !== null)
                {
                  // console.log(core.Modelo.entity[core.model]);
                  // console.log( core.Modelo.entity[core.model][CoreUI.tituloModulo] );
                  CoreUI.Utils.setTituloPantalla(null, null, core.Modelo.entity[core.model][0][CoreUI.tituloModulo]);
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

    /** Inicializa los combos del formulario */
    initializeSelectData: function()
    {
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

              core.Forms.getSelectData( entidadCombo, keyField, keyValue, null, null, elDOM, insertarOpcionSeleccionar );
              $('.selectpicker').select2({
                theme: 'bootstrap4',          
              });
          });

          $('.selectpicker').select2({
            theme: 'bootstrap4',          
          });

        }
    },

    /** Mapeamos la información devuelta por el endpoint con los datos del formulario */
    mapData: async function(formularioMapeo = null)
    {
      var formularioDestino = 'body .form-data';
      if(formularioMapeo !== null)
        formularioDestino = `body .${formularioMapeo}`;

      //  Comprobamos que esté declarado el formulario
      // if($("body .form-data").length)
      if( $(formularioDestino).length )
      {
        //  .form-data -> Dentro de este container se encuentran todos los datos
        // $("body .data").each( function(){
        $(`${formularioDestino} .data`).each( function(){


          var entidad = $(this).attr('hs-entity') ;
          var campo = $(this).attr('hs-field') ;
          var valor = '';
          //  console.log('Entidad: ' + entidad + ' Campo: ' + campo);
          //  Validamos que el campo venga informado desde el endpoint
          
          if(typeof core.Modelo.entity[ entidad ][0][campo] !== 'undefined')
          {
            valor = core.Modelo.entity[ entidad ][0][campo];
          }

          //var valor = core.Modelo.entity[ entidad ][0][campo];
          if( campo != 'password' )
          {
          //  FIXME: Arregla el if para evitar tantas anidaciones
              if($(this).hasClass("select-data"))
              {
                //  Leemos el id
                var id = $(this).attr("id");
                //  Validamos que exista el valor en el modelo antes de mapear
                if(typeof id !== 'undefined' && id !== -1)
                {
                  if(valor !== '')
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
            $('body input[type="number"]').each(function(e)
            {
                $(this).val( $(this).attr('value') );
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

    /** Mapea el formulario para montar el json de envío al endpoint */
    mapFormDataToSave: function(formularioProceso)
    {
        core.Forms.data = {};

        if(formularioProceso == null)
          formularioProceso = 'form-data';    

        //  Obtenemos el nombre del modelo
            var modelo = $(`body .${formularioProceso}`).attr('hs-model');

        //  Recorremos todos los input del form para procesarlo
            $(`body .${formularioProceso} .data`).each(function()
            {
            
              var fieldName = $(this).attr("hs-field");
              var entity = $(this).attr("hs-entity");

              core.Forms.data[fieldName] = core.Forms.getValueByTipoCampo( $(this) );

            });

    },

    /** Deprecated: Mapea la información de la pantalla a la propiedad data para guardar */
    mapDataToSave: function(formularioProceso = null)
    {

        core.Forms.data = {};

        if(formularioProceso == null)
          formularioProceso = 'form-data';

        //  Recorremos todos los campos del formulario
        $(`body .${formularioProceso} .data`).each(function(){
        
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

              //  Obtenemos el valor del campo según el tipo que sea (text, select,...)
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

    /**
     * Guarda y mapea los datos para enviar al endpoint
     * @param {boolean} dataAlreadyMapped Optional. Indica si los datos ya han sido previamente mapeados. De esa forma no vuelve a mapear
     */
    Save: async function(dataAlreadyMapped = true)
    {
      //  Comprobamos la acción primero para saber si es un update o una inserción
      var entidadSave = $("body").attr("hs-model");
      var idSave = $("body").attr("hs-model-id");
      var actionSave = $("body").attr("hs-action");

        //  Reiniciamos la información a enviar
        

        //  Mapeamos los datos para poder enviar la info
        if(dataAlreadyMapped == false)
        {
console.log('dataAlreadymapped false');
          core.Forms.data = {};
          core.Forms.mapDataToSave();
        }
console.log('accion: ' + actionSave);
        //  Comprobamos la acción del modelo
        switch(actionSave)
        {
          case 'get':
              core.Modelo.Update(core.model.toLowerCase(), idSave, core.Forms.data);
              break;
          case 'add':
              core.Modelo.Insert(core.model.toLowerCase(), core.Forms.data, true );
              break;
        }

    },

    /** Valida el formulario y devuelve la respuesta correspondiente */
    Validate: function(nombreFormulario = null)
    {
      var result = true;

      if(nombreFormulario == null)
        nombreFormulario = 'form-data'


      if( $(`body .${nombreFormulario} .form-required`).length == 0)
        return true;

      //  Recorre todos los elementos del dom que sean susceptibles de ser validados
      $(`body .${nombreFormulario} .form-required`).each(function()
      {
        if($(this).val() == '')
          result = false;
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
    Insert: async function(entidadSave, postData, updateModel = true)
    {
      await apiFincatech.post(`${entidadSave}/create`, postData).then(async (response) =>
      {

          var responseData = JSON.parse(response);

          // console.log('Resultado inserción1: ' + responseData.data);
          // console.log('Resultado inserción2: ' + responseData.status);
          // console.log('Result status: ' + responseData.status);

          if(responseData.status['response'] == "ok")
          {
              if(updateModel)
              {
                  var idInsercion = responseData.data['id'];

                  $("body").attr("hs-action", "get");
                  $("body").attr("hs-model-id", idInsercion);

                  core.actionModel = "get";
                  core.modelId = idInsercion;

              }
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

      /** Información del usuario autenticado */
      getUserInfo: async function()
      {
          await apiFincatech.get('userinfo' ).then( response => 
          {
              respuesta = JSON.parse(response);
              if(respuesta.user.nombre != null && respuesta.user.nombre != '')
              {
                  $('.usuarioFincatech').text(respuesta.user.nombre);
              }else{
                  $('.usuarioFincatech').text('Fincatech');
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

      },

      getRole: function()
      {
        return $('body').attr('hs-role');
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