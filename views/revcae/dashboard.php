<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">

                <div class="row">
                    <div class="col-12 col-xl-6 text-center text-xl-left">
                        <h5 class="card-title mb-2 mb-xl-0">Comunidades que tienen requerimientos de CAE pendientes - <span class="ml-2 mr-2 tipo-listado">Pendiente Comunidades</span></h5>
                    </div>
                    <div class="col-12 col-xl-3 mb-3 mb-xl-0">
                        <button role="button" class="btn btn-outline-dark w-100 mr-0 mr-xl-2 active btnSeleccionPendiente" data-tipo="comunidades">Pendiente comunidades</button>
                    </div>
                    <div class="col-12 col-xl-3">                    
                        <button role="button" class="btn btn-outline-dark w-100 btnSeleccionPendiente" data-tipo="administradores">Pendiente nuevos administradores</button>
                    </div>
                </div>
                

            </div>
            
            <div class="card-body pt-0 pb-0">
                <div class="row h-100 pb-4">
                    <div class="col-12">
                        <table class="table table-hover my-0 hs-tabla h-100" name="listadoDocumentosPendientesCAE" id="listadoDocumentosPendientesCAE" data-model="documentacioncae">
                            <thead class="thead"></thead>
                            <tbody class="tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>