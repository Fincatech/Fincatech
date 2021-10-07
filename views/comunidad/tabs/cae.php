            <div class="tab-pane fade h-100" id="empresa" role="tabpanel">

                <div class="row h-100">

                    <div class="col-12 d-flex">

                        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                            <div class="card-header pl-1 mb-2">

                                <div class="row">

                                    <div class="col-12 col-md-3">
                                        <h5 class="card-title mb-0"><i class="bi bi-truck mr-2"></i> <span class="titulo">CAE</span></h5>
                                    </div>
                                    <div class="col-12 col-md-9 text-right">
                                        <a href="javascript:void(0)" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4 btnAsociarEmpresaCAE mr-2"><i class="bi bi-shop pr-2 pl-2"></i> ASOCIAR EMPRESA / AUTÓNOMO</a>
                                        <a href="javascript:void(0)" class="btn btn-outline-info text-uppercase rounded-pill shadow pl-2 pr-4 btnNuevoEmpleadoComunidad d-none"><i class="bi bi-person pl-2 pr-2"></i> ALTA EMPLEADO COMUNIDAD</a>
                                    </div>

                                </div>



                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border mb-1 border-white space-between">

                                <div class="row flex-grow-1">
<!-- Empresas asociadas -->
                                    <div class="col-12">

                                        <div class="card">

                                            <div class="card-header pl-0 pt-0"><!--headerListado-->

                                                <div class="row">

                                                    <div class="col-12 col-md-9">
                                                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-shop pr-2"></i> Empresas</h5>
                                                    </div>
                                        
                                                </div>

                                            </div>

                                            <div class="card-body">

                                                <table class="table table-hover my-0 hs-tabla w-100 no-clicable" data-order='[[ 1, "asc" ]]' name="listadoEmpresaComunidad" id="listadoEmpresaComunidad" data-model="empresa">
                                                    <thead class="thead"></thead>
                                                    <tbody class="tbody"></tbody>
                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="row flex-grow-1">
<!-- Empleados asociados -->
                                    <div class="col-12">

                                        <div class="card">

                                            <div class="card-header pl-0 pt-0"><!--headerListado-->

                                                <div class="row">

                                                    <div class="col-12 col-md-9">
                                                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-people pr-2"></i> Empleados</h5>
                                                    </div>
                                        
                                                </div>

                                            </div>

                                            <div class="card-body pt-0">

                                                <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoEmpleadosComunidad" id="listadoEmpleadosComunidad" data-order='[[ 2, "asc" ]]'  data-model="Empleado">
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

                </div>
            
            </div>