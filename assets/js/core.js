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

};

//  Inicialización del core
$(()=>{
    core.init();
    apiFincatech.init();
});