
let clickEventType = ((document.ontouchstart!==null)?'click':'touchstart');
let environment = 'd';

let core =
{

  init: function()
  {

  },

  helper:
  {
    clickEventType : ((document.ontouchstart!==null)?'click':'touchstart'),
  },

  /** Formularios de datos */
  Forms:
  {

  },

};

//  Inicialización del core
$(()=>{
    core.init();
    apiFincatech.init();
});