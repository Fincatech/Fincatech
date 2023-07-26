
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';
let baseURL = (environment == 'd' ? '/fincatech/' : '/');
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
          <p class="mt-3 text-center" style="font-size: 14px;">Para asignar una empresa, puede buscar e-mail.</p>
          <p class="mt-3 text-center" style="font-size: 14px;">Si no encuentra la empresa que desea asociar, puede crear una nueva clicando sobre el botón <strong>CREAR NUEVA EMPRESA</strong>. Una vez creada la empresa, ésta será asignada automáticamente a la comunidad actual.</p>
      </div>
    </div>

    <!-- input de empresa -->
    <div class="row mb-4 wrapperBusquedaEmpresa">
      <div class="col-12 col-sm-10">
        <div class="form-group">
          <input type="text" class="form-control" id="searchEmpresa" name="searchEmpresa" placeholder="Escriba el e-mail de la empresa que desea buscar">
        </div>
      </div>
      <div class="col-12 col-sm-2">
        <a href="javascript:void(0);" class="btn btn-primary btnBuscarEmpresaCAE pt-1 pb-1 d-block"><i class="bi bi-search"></i> BUSCAR EMPRESA</a>
      </div>
      <div class="col-12">
        <p class="m-0 mensaje">&nbsp;</p>
      </div>
    </div>

    <!-- Información de la empresa -->
    <div class="row mb-4 wrapperInfoEmpresa" style="display: none;">
        <div class="col-12">
          <div class="card">
            <div class="card-body shadow-inset">
                <p class="font-weight-bold"><span class="nombreEmpresa"></p>
                <p><span class="cifEmpresa"></p>
                <p><span class="emailEmpresa"></p>
                <div class="clearfix mt-3">&nbsp;</div>
                <p>Si los datos son correctos, clique sobre el botón <strong>ASIGNAR</strong> para asociarla a la comunidad</p>
                <a href="javascript:void(0);" class="btnConfirmarEmpresaCAE d-inline-block btn btn-sm btn-success" data-id="" data-nombre="">ASIGNAR</a>
            </div>
          </div>
        </div>
    </div>

    <!-- Listado simple de empresas del sistema-->

    <div class="row mb-4 wrapperSelectorEmpresa d-none">

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
              <a href="javascript:void(0);" class="btn btn-success bntCrearNuevaEmpresaCAE pt-1 pb-1" style="display: none;">CREAR NUEVA EMPRESA</a>
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
           
          <div class="row form-group wrapperTituloDocumento text-left">
            <div class="col-12">
              <label>Título</label>
              <input type="text" class="form-control w-100 tituloDocumentoRGPD mt-2" id="tituloDocumentoRGPD" maxlength="40" name="tituloDocumentoRGPD">
            </div>
          </div>
          <div class="row form-group wrapperObservaciones text-left mt-2">
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
          if(core.model != 'Dashboard'){

          }
            // await core.Modelo.getAll();
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
      $("body").on(core.helper.clickEventType, ".btnLogout", function(){
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

      /** Botón de cambiar contraseña */
      $('body').on(core.helper.clickEventType, '.btnChangePassword', function(e)
      {
          //  Lanzamos el modal de cambiar contraseña
              core.Security.changePassword();
      });

      /** Botón de reestablecer contraseña */
      $('body').on(core.helper.clickEventType, '.btnResetpassword', function(e)
      {
          //  Lanzamos el modal de cambiar contraseña
              core.Security.resetPassword();
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
    },

    validarEmail(email)
    {
      var validRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return validRegex.test(email.toLowerCase());
    },

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
    promiseInitialization: null,
    errorMessage: '',

    /**
     * Dispara el evento de formulario cargado (Pantalla cargada)
     * @param {*} formularioDestino 
     */
    triggerEventLoaded: function(_formularioDestino){

      var formularioDestino = 'body .form-data';
      if(_formularioDestino)
      { 
        formularioDestino = _formularioDestino;
      }

      $('body').trigger('hsFormDataLoaded',$(formularioDestino).attr('id'));

    },

    init: async function()
    {

      // var promesa = new Promise(function(resolve, reject){
      var promesa = new Promise(function(resolve, reject){
          core.Forms.initializeSelectData( (resultado) =>{
            console.log('----Resultado-----');
            resolve(true);
          });
      });

      promesa.then(function(){

        console.log('Ha terminado el initialize y entra en la promesa.then');
      
        console.log(' *** Promesa terminada ***');
      });

    },

    getModelo: function(){

      // console.log('getModelo');
      if(core.modelId != "")
      {

        apiFincatech.get(core.model.toLowerCase() + "/" + core.modelId).then( (result) =>{

          //  Primero controlamos el código de estado
          var estado = JSON.parse(result)["status"];
          if(estado.response === 'error')
          {

              if(estado.error === '403')
                apiFincatech.getView('comunes','403','','.content .container-fluid');

              return;

          }

          //  Mapeamos los datos en el formulario
          core.Modelo.entity[core.model] = JSON.parse(result)["data"][core.model];

          //console.log(core.Modelo.entity[core.model]);
          core.Forms.mapData();

          if($('#password').length)
          {
            $('#password').val('');
          }

          //  Si no está definido, entonces dejamos por defecto el que viene
              if(CoreUI.tituloModulo !== null)
              {
                var tituloPantalla = '';

                if(Array.isArray(CoreUI.tituloModulo))
                {
                  for(var i = 0; i<CoreUI.tituloModulo.length; i++)
                  {
                    tituloPantalla += (core.Modelo.entity[core.model][0][CoreUI.tituloModulo[i]]) + ' ' ;
                  }
                }else{
                  tituloPantalla = core.Modelo.entity[core.model][0][CoreUI.tituloModulo];
                }

                CoreUI.Utils.setTituloPantalla(null, null, tituloPantalla);
              }

            //  Disparamos el evento de modelo cargado
            core.Forms.triggerEventLoaded(null);

        });

      }  
    },

    /** Recuperamos el modelo para la entidad que se está cargando */
    getSchema: async function()
    {
      return apiFincatech.get(core.model.toLowerCase() + "/schema").then((result)=>{
        core.Modelo.schema = result;
      });
    },

    /** Inicializa los combos del formulario */
    initializeSelectData: async function()
    {

      var promesaSelect;
      var numeroCombos = $("body .form-data .select-data").length;
      var iCombo = 0;

      promesaSelect = new Promise(async function(resolve, reject){

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
                insertarOpcionSeleccionar = ($(this).attr('hs-seleccionar') == 'false' ? false : true);              
              }

              var promesaSelectActual = new Promise(async function(resolve2, reject2){
                // console.log('Llamada a getselectdata');
                core.Forms.getSelectData( entidadCombo, keyField, keyValue, null, null, elDOM, insertarOpcionSeleccionar ).then((result)=>{
                  // console.log('--- Ha resuelto GetSelectData ---');
                  iCombo++;
                  // console.log('numeroCombos: ' + numeroCombos);
                  // console.log('iCombo: ' + iCombo);
                  if(iCombo >= numeroCombos)
                  {
                    // console.log('icombo>=n')
                    resolve(true);
                  }
                });

              });

          });

        }else{
          // console.log('resolve2')
          resolve(true);
        }
        
      });

        promesaSelect.then(function(){
          // console.log('------------ promSelect2');
          core.Forms.getModelo();
          return promesaSelect;
        }); 

    },


    /** Carga los datos en el combo especificado y la entidad que se va a recuperar */
    getSelectData: async function(entidad, keyField, keyValue, keyOriginField, keyOriginValue, elementoDOM, insertarOpcionSeleccionar)
    {
        //  Vaciamos el combo
            $("body #" + elementoDOM).html("");
        //  Recuperamos los registros que correspondan según la entidad
       var promSelect = new Promise( function(resolve, reject)
        {
           apiFincatech.get(`${entidad}/list?target=cbo`).then( (data) =>
          // await apiFincatech.get(`${entidad}/list?target=cbo`).then( async (data) =>
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

              if(insertarOpcionSeleccionar)
              {
                htmlOutput = '<option value="-1" disabled selected="selected">Seleccione una opción</option>';
                $("body #" + elementoDOM).append(htmlOutput);
              }

              // console.log('El: ' + responseData[entidad][keyValue]);
              for(x = 0; x < responseData[entidad].length; x++)
              {
                  var valueId = responseData[entidad][x][keyValue];
                  var value = responseData[entidad][x][keyField];
                  htmlOutput = `<option value="${valueId}">${value}</option>`;
                  $("body #" + elementoDOM).append(htmlOutput);
              }

              $('body #' + elementoDOM).select2({
                theme: 'bootstrap4',
                placeholder: "Seleccione una opción",
              });

              // console.log('--- Ha recuperado los datos desde el WS ---');
              resolve(true);
          });
       });

       return promSelect.then(function(){
         return true;
       });
    },


    //TODO: Implementar mejora de mapeo de formulario
    mapDataFromModel: function(nombreFormulario, datosModelo)
    {

        //  Validamos que exista el formulario antes de procesar
            if( !$(`#${nombreFormulario}`).length )
              return;

        //  Iteramos sobre todos los campos del formulario
            $( `#${nombreFormulario} .data` ).each( function(){

                var entidad = $(this).attr('hs-entity');
                var campo = $(this).attr('hs-field') ;
                var valor = '';
                
                //  Comprobamos si el dato pertenece a una subentidad
                    if( typeof datosModelo[entidad] === 'undefined')
                    {
                        valor = datosModelo[campo];
                    }else{
                        valor = datosModelo[entidad][campo];
                    }

                    if( campo != 'password' )
                    {
                    //  FIXME: Arregla el if para evitar tantas anidaciones
                        if( $(this).hasClass("select-data") )
                        {
                          //  Leemos el id
                              var id = $(this).attr("id");
                          //  Validamos que exista el valor en el modelo antes de mapear
                          if(typeof datosModelo[id] !== 'undefined' && datosModelo[id] !== -1)
                          {
                            if(valor !== '')
                            {
                              $(`#${id}`).val(valor);
                              $(`#${id}`).trigger('change');

                              // $(`#${id} option[value=${valor}]`).attr('selected','selected');

                            }
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
    },

    /** Mapeamos la información devuelta por el endpoint con los datos del formulario */
    mapData: async function(formularioMapeo = null)
    {
      var formularioDestino = 'body .form-data';
      var valor = '';
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
          var entidadRelacionada = $(this).attr('hs-entity-related');
         
          //  Validamos que el campo venga informado desde el endpoint
          if( typeof entidadRelacionada === 'undefined' )
          {

            if(typeof core.Modelo.entity[ entidad ][0][campo] !== 'undefined')
            {
              valor = core.Modelo.entity[ entidad ][0][campo];
            }
          }else{
            if(typeof core.Modelo.entity[ entidad ][0][entidadRelacionada][0][campo] !== 'undefined')
            {
               valor = core.Modelo.entity[ entidad ][0][entidadRelacionada][0][campo];
            }
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
                    if(typeof id !== 'undefined' && id !== -1){
                        if(valor !== '' && valor !== null){
                          // console.log('Valor: ' + valor);
                          // console.log(`Campo: #${id} option[value=${valor}]`);
                          $(`body #${id} option[value=${valor}]`).attr('selected','selected');
                          $(`body #${id}`).val(valor);
                          $(`body #${id}`).trigger('change');
                          // console.log($(`body #${id} option:selected`).val());
                        }
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
            // $('.selectpicker').select2({
            //   theme: 'bootstrap4',           
            // });

            $('.selectpicker').each(function()
            {
             // $(this).trigger('change');
              // console.log($(this).val());
              // console.log(valor);
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

    prepareFormDataBeforeSend: function(formName)
    {

        core.Forms.data = Object();

        var modelo = $(`body #${formName}`).attr('data-model');
        // core.Forms.data[modelo] = {};
        //  Recorremos todos los input del form para procesarlo
        //  Recorremos todos los input del form para procesarlo
        $(`body .${formName} .data`).each(function()
        {
        
          var fieldName = $(this).attr("hs-field");
          var entity = $(this).attr("hs-entity");
          var entityRelated = null;

          if( $(this).attr("hs-entity-related") !== undefined && 
              $(this).attr("hs-entity-related") !== null )
          {
            entityRelated = $(this).attr("hs-entity-related");
          }

          // console.log('Entity: ' + entity);
          // console.log('Modelo: ' + modelo);
          // console.log('FieldName: ' + fieldName);

          if( (entity == modelo) && entityRelated == null)
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

          // core.Forms.data[fieldName] = core.Forms.getValueByTipoCampo( $(this) );

        });

        console.log(core.Forms.data);


    },

    /** Mapea el formulario para montar el json de envío al endpoint */
    mapFormDataToSave: function(formularioProceso, modelo = null)
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
          core.Forms.data = {};
          core.Forms.mapDataToSave();
        }

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

    GetErrorMessage: function()
    {
      return core.Forms.errorMessage;
    },

    SetError: function(_message)
    {
        core.Forms.errorMessage = `${core.Forms.errorMessage}<br/><i class="bi bi-x-circle mr-3 text-danger"></i>${_message} `;
    },

    ShowErrorMessage: function(){
      let errormsg = core.Forms.GetErrorMessage();
      if(errormsg !== '')
      {
        CoreUI.Modal.Error(`Se han detectado los siguientes errores, por favor, corríjalos para continuar:<br><br><p class="text-left">${errormsg}</p>`,'Error');
      }
    },

    /** Valida el formulario y devuelve la respuesta correspondiente */
    Validate: function(nombreFormulario = null)
    {
      var result = true;
      core.Forms.errorMessage = '';
      $('.form-error').removeClass('form-error');

      if(nombreFormulario == null)
        nombreFormulario = 'form-data'

      if( $(`body .${nombreFormulario} .form-required`).length == 0)
        return true;

      //  Recorre todos los elementos del dom que sean susceptibles de ser validados
      $(`body .${nombreFormulario} .form-required`).each(function()
      {

        if( $(this).val() == '' )
        {
          if( !$(this).hasClass('form-error') )
          {
            //  Comprobamos si tiene establecido el texto del mensaje de error
            if(typeof($(this).attr('form-error-message')) !== 'undefined')
              core.Forms.SetError($(this).attr('form-error-message'));

            $(this).addClass('form-error');

          }

          result = false;

        }

      });

      return result;

    },

  },

  Modelo: {

    entity: Object(),
    schema: Object(),
    insertedId: null,

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
    Insert: async function(entidadSave, postData, updateModel = true, mensaje = '', showMessage = true, showLoading = true)
    {
      await apiFincatech.post(`${entidadSave}/create`, postData, showLoading).then(async (response) =>
      {
console.log(response);
          var responseData = JSON.parse(response);

          if(responseData.status['response'] == "ok")
          {
              core.Modelo.insertedId = responseData.data['id'];

              if(updateModel)
              {
                  var idInsercion = responseData.data['id'];

                  $("body").attr("hs-action", "get");
                  $("body").attr("hs-model-id", idInsercion);

                  core.actionModel = "get";
                  core.modelId = idInsercion;

              }
              if(mensaje !== '' && mensaje !== false)
              {
                CoreUI.Modal.Success(mensaje);    
              }else{

                if(showMessage)
                  CoreUI.Modal.Success("El registro se ha creado correctamente");    
              }

              if(entidadSave.toLowerCase() == 'comunidad' && core.Security.getRole() == 'ADMINFINCAS')          
              {
                comunidadesCore.renderMenuLateral();
              }

          }else{

            //  TODO: Ver cuál es el error en el json
            CoreUI.Modal.Error("No se ha podido guardar por el siguiente motivo:<br><br>" + responseData.status.error);

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

                      if(typeof window[`table${nombreListadoDOM}`] !== 'undefined')
                      {
                        window[`table${nombreListadoDOM}`].ajax.reload();
                      }

                      //  Refrescamos el menú lateral para reflejar la eliminación de la comunidad
                      if(core.Security.getRole()=='ADMINFINCAS' && endpoint.toLowerCase()=='comunidad')
                      {
                        comunidadesCore.renderMenuLateral();
                      }
                 
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

      user: null,

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

              core.Security.user = respuesta.user.id;
              core.Security.rgpd = respuesta.user.rgpd;

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
                // CoreUI.Modal.Success('La sesión se ha cerrado correctamente', 'Fincatech', function(){
                  window.location.href = baseURL + 'login';
                // });
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
                  window.location.href = 'dashboard';
              }

            });
        //  En caso contrario mostramos el mensaje de error devuelto por el api

      },

      changePassword: async function()
      {
        const { value: formValues } = await Swal.fire({
          title: '<i class="bi bi-lock"></i> Cambiar contraseña',
          html: `
            <div class="row pl-3 pr-3 pt-3">
              <div class="col-12">
                <div class="row">
                  <div class="col-12 text-left">
                      <label for="nuevopassword">Contraseña</label>
                      <input id="nuevopassword" type="text" placeholder="Contraseña" class="form-control">
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-12 text-left">
                    <label for="confirmpassword">Confirmar contraseña</label>
                    <input id="confirmpassword" type="text" placeholder="Confirmación contraseña" class="form-control">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <p class="errorValidation text-danger" style="display: none;">&nbsp;</p>
                  </div>
                </div>
              </div>
            </div>`,
          focusConfirm: false,
          preConfirm: () => {
            var mensajeError = '';

            if($('#nuevopassword').val() != $('#confirmpassword').val())
                mensajeError = 'Las contraseñas no coinciden<br>';

            if( $('#nuevopassword').val() == '' || $('#confirmpassword').val() == '')
              mensajeError += 'Debe escribir la nueva contraseña y su confirmación<br>';
          
            if( $('#nuevopassword').val().length < 5 ||  $('#confirmpassword').val().length < 5 )
              mensajeError += 'La longitud de la contraseña es inferior a 5 caracteres<br>';

            if(mensajeError !== '')
            {
              $('.errorValidation').html(mensajeError);       
              $('.errorValidation').show();
              return false;
            }else{

              var encryptedPassword = core.Security.MD5($('#nuevopassword').val());
              var data = {
                password: encryptedPassword
              };

              apiFincatech.post('changepassword',data).then( (result) =>{
                  var resultado = JSON.parse(result);
                  if(resultado['data']['changepassword'] == 'ok')
                  {
                    CoreUI.Modal.Success('La contraseña se ha cambiado correctamente. Por su seguridad se va a cerrar sesión para que entre con la nueva contraseña.','Cambiar contraseña', function()
                    {
                      core.Security.logout();
                    });          
                  }else{
                    CoreUI.Modal.Error('La contraseña no ha podido cambiarse','Cambiar contraseña');          
                  }
              });
              return true;
            }

          }
        })
      },

      resetPassword: async function()
      {
          if($('#email').val() == '')
          {
            CoreUI.Modal.Error('Escriba el correo electrónico con el que accede a la plataforma','Reestablecer contraseña');
            return;
          }

              var _email = $('#email').val();
              var data = {
                email: _email
              };

              apiFincatech.post('resetpassword',data).then( (result) => {

                  var resultado = JSON.parse(result);
                  if(resultado['status']['response'] == 'error')
                  {
                    CoreUI.Modal.Error(resultado['status']['error'], 'E-mail no encontrado');
                    $('#email').val('');
                  }else{

                    if(resultado['data']['resetpassword'] == 'ok')
                    {
                      CoreUI.Modal.Success('Le hemos enviado un e-mail con la nueva contraseña. Podrá cambiar la misma, en su perfil. Por favor, compruebe su bandeja de entrada o la carpeta de correo no deseado.','Contraseña reestablecida');
                      $('#email').val('');
                    }else{
                      CoreUI.Modal.Error('La contraseña no ha podido reestablecerse','Reestablecer contraseña');          
                    }

                  }

              });

      },

      /**
       * Método que cambia el password para el usuario actual
       * @param {*} newPassword 
       */
      _changePassword: async function()
      {

        const { value: password } = await Swal.fire({
          title: 'Cambiar contraseña',
          input: 'text',
          inputLabel: 'Nueva contraseña',
          inputPlaceholder: 'Escriba la nueva contraseña',
          inputAttributes: {
            maxlength: 15,
            autocapitalize: 'off',
            autocorrect: 'off'
          }
        })
        
        if (password) 
        {
          //  Codificamos en md5 la contraseña y la enviamos al endpoint correspondiente
              var encryptedPassword = core.Security.MD5(password);
              var data = {
                password: encryptedPassword
              };
              apiFincatech.post('changepassword',data).then( (result) =>{
                  var resultado = JSON.parse(result);
                  if(resultado['data']['changepassword'] == 'ok')
                  {
                    CoreUI.Modal.Success('La contraseña se ha cambiado correctamente. Por su seguridad se va a cerrar sesión para que entre con la nueva contraseña.','Cambiar contraseña', function()
                    {
                      core.Security.logout();
                    });          
                  }else{
                    CoreUI.Modal.Error('La contraseña no ha podido cambiarse','Cambiar contraseña');          
                  }
              });
        }else{
          CoreUI.Modal.Info('La contraseña no ha sido cambiada','Cambiar contraseña');
        }
      },

      getRole: function()
      {
        return $('body').attr('hs-role');
      },

      MD5: function(value)
      {
        return md5(value).toLowerCase();
      }

  },

  /** Componente de validaciones diversas */
  Validator: {

    /**
     * Validación de formato del e-mail
     * @param {*} emailToValidate 
     * @returns 
     */
      Email: function(emailToValidate)
      {

        if(emailToValidate === '' || typeof(emailToValidate) === 'undefined')
        {
          return false;
        }
        var validRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return validRegex.test(emailToValidate.toLowerCase());
      },

      /**
       * Funcion para verificar si una cuenta IBAN es correcta
       * @param string iban
       * @return boolean
       */
      checkIBAN: function(iban)
      {
          if (iban == '')
            return false;

          if(iban.length==24)
          {
              var digitoControl = core.Validator.getCodigoControl_IBAN(iban.substr(0,2).toUpperCase(), iban.substr(4));
              if(digitoControl==iban.substr(2,2))
                  return true;
          }
          return false;
      },
  
      /**
       * Funcion que devuelve el codigo de verificacion de una cuenta bancaria
       * @param string codigoPais los dos primeros caracteres del IBAN
       * @param string cc la cuenta corriente, que son los ultimos 20 caracteres del IBAN
       * @return string devuelve el codigo de control
       */
      getCodigoControl_IBAN: function (codigoPais,cc)
      {
          // cada letra de pais tiene un valor
          valoresPaises = {
              'A':'10',
              'B':'11',
              'C':'12',
              'D':'13',
              'E':'14',
              'F':'15',
              'G':'16',
              'H':'17',
              'I':'18',
              'J':'19',
              'K':'20',
              'L':'21',
              'M':'22',
              'N':'23',
              'O':'24',
              'P':'25',
              'Q':'26',
              'R':'27',
              'S':'28',
              'T':'29',
              'U':'30',
              'V':'31',
              'W':'32',
              'X':'33',
              'Y':'34',
              'Z':'35'
          };
      
          // reemplazamos cada letra por su valor numerico y ponemos los valores mas dos ceros al final de la cuenta
          var dividendo = cc + valoresPaises[codigoPais.substr(0,1)] + valoresPaises[codigoPais.substr(1,1)] + '00';
      
          // Calculamos el modulo 97 sobre el valor numerico y lo restamos al valor 98
          var digitoControl = 98 - core.Validator.modulo(dividendo, 97);
      
          // Si el digito de control es un solo numero, añadimos un cero al delante
          if(digitoControl.length==1)
          {
              digitoControl='0'+digitoControl;
          }
          return digitoControl;
      },
  
      /**
       * Funcion para calcular el modulo
       * @param string valor
       * @param integer divisor
       * @return integer
       */
      modulo: function(valor, divisor) {
          var resto=0;
          var dividendo=0;
          for (var i=0;i<valor.length;i+=10) {
              dividendo = resto + "" + valor.substr(i, 10);
              resto = dividendo % divisor;
          }
          return resto;
      },
 
  }


};


//  Inicialización del core
$(function()
{

    apiFincatech.init();
    core.init();
    $('.loading').hide();
});

function md5(d){return rstr2hex(binl2rstr(binl_md5(rstr2binl(d),8*d.length)))}function rstr2hex(d){for(var _,m="0123456789ABCDEF",f="",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function rstr2binl(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function binl2rstr(d){for(var _="",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function binl_md5(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}