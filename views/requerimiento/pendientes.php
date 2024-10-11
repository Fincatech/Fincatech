<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <h5 class="card-title mb-0 text-center infoListado">Listado Requerimientos Pendientes <span class="seleccionado">&nbsp;</span></h5>
                        <p class="text-center d-none mb-0">Seleccione el tipo de listado que desea mostrar</p>
                    </div>
                </div>
                <div class="row mt-3 justify-content-center">
                    <div class="col-12 col-md-3 fincatech--cae">
                        <a href="javascript:void(0);" class="btnRequisitosPendientesCAE d-block btn btn-outline-secondary rounded-pill text-uppercase shadow pl-2 pr-4"><i class="bi bi-shield-exclamation"></i> CAE</a>
                    </div>
                    <div class="col-12 col-md-4 fincatech--rgpd">
                        <a href="javascript:void(0);" class="btnRequisitosPendientes d-block btn btn-outline-secondary rounded-pill  shadow text-uppercase pl-2 pr-4" data-tipo="rgpd"><i class="bi bi-shield-exclamation"></i> RGPD</a>
                    </div>
                </div>

                <div class="row mt-3 justify-content-center wrapperRequerimientosCAE" style="display: none;">
                    <p class="text-center">¿Qué requerimientos pendientes de CAE desea listar?</p>
                    <div class="col-12 col-md-4">
                        <a href="javascript:void(0);" class="btnRequisitosPendientes d-block btn btn-outline-info text-uppercase shadow-neumorphic pl-2 pr-4" data-tipo="cae"><i class="bi bi-building"></i> Requerimientos pendientes Comunidades</a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a href="javascript:void(0);" class="btnRequisitosPendientes d-block btn btn-outline-secondary text-uppercase shadow-neumorphic pl-2 pr-4" data-tipo="cae_empresa"><i class="bi bi-shop"></i> Requerimientos pendientes Proveedores</a>
                    </div>
                </div>                

            </div>
            
            <div class="card-body pt-0 d-flex space-between">
                <div class="row flex-grow-1 requerimientosPendientesComunidad">
                    <div class="col-12">
                        <table class="table table-hover my-0 hs-tabla no-clicable" name="listadoRequerimientosPendientes" id="listadoRequerimientosPendientes" data-model="rgpdempleadosadministracion">
                            <thead class="thead"></thead>
                            <tbody class="tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>