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
                                        <a href="javascript:void(0)" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4 btnAsociarEmpresaCAE mr-2"><i class="bi bi-shop pr-2 pl-2"></i> ALTA EMPRESA / AUTÓNOMO</a>
                                        <a href="javascript:void(0)" class="btn btn-outline-info text-uppercase rounded-pill shadow pl-2 pr-4 btnNuevoEmpleadoComunidad d-none"><i class="bi bi-person pl-2 pr-2"></i> ALTA EMPLEADO COMUNIDAD</a>
                                    </div>

                                </div>



                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border mb-1 border-white space-between">

                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-shop pr-2"></i> Empresas de la comunidad</h5>
                                    </div>
                                </div>

                                <!-- Empresas asociadas -->
                                <div class="row flex-grow-1 wrapperEmpresasComunidad">
                                    <div class="col-12">

                                        <div class="card">

                                            <div class="card-header pl-0 pt-0"><!--headerListado-->

                                                <div class="row">

                                                    <div class="col-12 col-md-9">
                                                        
                                                    </div>
                                        
                                                </div>

                                            </div>

                                            <div class="card-body pt-0">

                                                <table class="table table-hover my-0 hs-tabla w-100 no-clicable" data-order='[[ 1, "asc" ]]' name="listadoEmpresaComunidad" id="listadoEmpresaComunidad" data-model="empresa">
                                                    <thead class="thead"></thead>
                                                    <tbody class="tbody"></tbody>
                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- Empleados asociados a la comunidad-->
                                <div class="row flex-grow-1 wrapperEmpleadosEmpresaComunidad mt-3">
                                    
                                    <div class="col-12 p-1 border-0">

                                        <div class="card h-100 infoAmpliadaCAE rounded-0">

                                            <div class="card-header bg-transparent pb-3 pt-0">

                                                <div class="row">

                                                    <div class="col-12 col-md-6">
                                                        <ul class="nav nav-tabs border-bottom-0" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active text-dark" data-toggle="tab" href="#tab-1"><i class="bi bi-folder-check mr-2"></i>Documentos</a>
                                                            </li>
                                                            <li class="nav-item ">
                                                                <a class="nav-link text-dark" data-toggle="tab" href="#tab-2"><i class="bi bi-person mr-2"></i>Empleados</a>
                                                            </li>
                                                        </ul>     
                                                    </div>

                                                    <div class="col-12 col-md-6 text-right">

                                                        <a href="javascript:void(0);" class="d-block mr-3 btnCerrarEmpleadosComunidad btn btn-outline-danger rounded-pill" aria-label="Close">
                                                        <span><i class="bi bi-x-circle mr-2"></i>VOLVER AL LISTADO DE EMPRESAS DE LA COMUNIDAD</span></a>

                                                    </div>

                                                </div>
                                                                                            </div>

                                            <div class="card-body border pt-3 shadow-neumorphic ml-3 mr-3 mt-0">
                                                <div class="tab-content">
                                                    <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
                                                        <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoDocumentacionEmpresa" id="listadoDocumentacionEmpresa" data-model="documentacioncae">
                                                            <thead class="thead"></thead>
                                                            <tbody class="tbody"></tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade text-center" id="tab-2" role="tabpanel">
                                                        <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoEmpleadosComunidad" id="listadoEmpleadosComunidad" data-order='[[ 2, "asc" ]]'  data-model="Empleado">
                                                            <thead class="thead"></thead>
                                                            <tbody class="tbody"></tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab-3" role="tabpanel">
                                                        <h5 class="card-title">Card with tabs</h5>
                                                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>
            
            </div>