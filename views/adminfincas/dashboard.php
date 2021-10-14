            <!-- Info inicial -->
            <div class="row d-none">
                <!-- <div class="col-xl-6 col-xxl-5 d-flex"> -->
                <div class="col-12 d-flex">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <div class="card card-dashboard statscomunidades">
                                    <div class="card-body">
                                        <img src="<?php echo ASSETS_IMG; ?>icon_comunidad_dashboard.png" class="img-responsive icono-marcaagua" />
                                        <h5 class="card-title mb-3">Comunidades</h5>
                                        <h1 class="mt-1 mb-3 total">12</h1>
                                        <div class="mb-1">
                                            <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> &nbsp; </span>
                                            <span class="text-muted">&nbsp;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <img src="<?php echo ASSETS_IMG; ?>check-square.png" class="img-responsive icono-marcaagua" />
                                        <h5 class="card-title mb-4">Documentos</h5>
                                        <h1 class="mt-1 mb-3 text-success totalDocumentos">0</h1>
                                        <div class="mb-1">
                                            <!--<span class="text-muted"><i data-feather="eye"></i> <a href="javascript:void(0);" class="btnVerDocumentosPendientes text-dark">ver documentos pendientes</a></span>-->
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <div class="col">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <img src="<?php echo ASSETS_IMG; ?>check-square.png" class="img-responsive icono-marcaagua" />
                                        <h5 class="card-title mb-4">Documentos Verificados</h5>
                                        <h1 class="mt-1 mb-3 text-success totalDocumentosVerificados">0</h1>
                                        <div class="mb-1">
                                            <!--<span class="text-muted"><i data-feather="eye"></i> <a href="javascript:void(0);" class="btnVerDocumentosPendientes text-dark">ver documentos pendientes</a></span>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <img src="<?php echo ASSETS_IMG; ?>alert-triangle.png" class="img-responsive icono-marcaagua" />
                                        <h5 class="card-title mb-4">Documentos pendientes de subir</h5>
                                        <h1 class="mt-1 mb-3 text-danger text-center totalDocumentosPendientes">0</h1>
                                                <div class="mb-1 text-center d-none ">
                                                    <span class="text-muted"><i data-feather="eye"></i> <a href="javascript:void(0);" class="btnVerDocumentosPendientes text-dark">ver documentos</a></span>
                                                </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <img src="<?php //echo ASSETS_IMG; ?>alert-triangle.png" class="img-responsive icono-marcaagua" />
                                        <h5 class="card-title mb-4">Documentos pendientes de verificar</h5>
                                            <h1 class="mt-1 mb-3 text-danger text-center">12</h1>
                                            <div class="mb-1 text-center">
                                                <span class="text-muted"><i data-feather="eye"></i> <a href="javascript:void(0);" class="btnVerDocumentosPendientes text-dark">ver documentos</a></span>
                                            </div>
                                    </div>
                                </div>
                            </div>									 -->
                            <!-- <div class="col-sm-2">
                                <div class="card card-dashboard">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Lorem ipsum</h5>
                                        <h1 class="mt-1 mb-3">0</h1>
                                        <div class="mb-1">
                                            <span class="text-muted">Lorem ipsum</span>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>

            </div>
            <!-- Listado de comunidades -->
            <div class="row">

                <div class="col-12 d-flex">

                    <div class="card flex-fill shadow-neumorphic">

                        <div class="card-header">

                            <div class="row">

                                <div class="col-12 col-md-9">
                                    <h5 class="card-title mb-0"><i class="bi bi-building"></i> Tus comunidades</h5>
                                </div>
                                <div class="col-12 col-md-3 text-right">
                                    <a href="comunidad/add" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> AÑADIR COMUNIDAD</a>
                                </div>
                            </div>



                        </div>
                        
                        <div class="listado pl-3 pr-3 pb-3">

                            <table class="table table-hover my-0 hs-tabla" name="listadoComunidad" id="listadoComunidad" data-model="comunidad">
                                <thead class="thead"></thead>
                                <tbody class="tbody"></tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>
            