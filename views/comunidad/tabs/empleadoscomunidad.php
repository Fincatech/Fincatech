<div class="tab-pane fade flex-grow-1 space-between col-12" id="empleadoscomunidadcontratista" role="tabpanel">
    
    <div class="row">

        <div class="col-12 d-flex">

            <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                <div class="card-header pl-1">

                    <div class="row">

                        <div class="col-12 col-md-6">
                            <h5 class="card-title mb-0"><i class="bi bi-people-fill mr-2"></i> <span class="titulo">Empleados de la comunidad</span></h5>
                        </div>

                        <?php if($App->isContratista() ): ?>

                            <div class="col-12 col-md-6 text-right">
                                <a href="javascript:void(0)" class="btn btn-outline-info text-uppercase rounded-pill shadow pl-2 pr-4 btnModalAsignarEmpleado"><i class="bi bi-person pl-2 pr-2"></i> ASIGNAR EMPLEADO COMUNIDAD</a>
                            </div>

                        <?php endif; ?>

                    </div>                    

                </div>
        
                <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                    <div class="row">

                        <div class="col-12">

                            <table class="table table-hover my-0 hs-tabla w-100 no-clicable" data-order='[[ 1, "asc"]]' name="listadoEmpleadosComunidad" id="listadoEmpleadosComunidad" data-model="Empleado">
                                <thead class="thead"></thead>
                                <tbody class="tbody"></tbody>
                            </table>

                        </div>

                    </div>                  

                </div>

            </div>

        </div>

    </div>

</div>
