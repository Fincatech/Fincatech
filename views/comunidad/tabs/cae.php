<div class="tab-pane fade h-100 tabCae" id="empresa" role="tabpanel">

    <div class="row h-100">

        <div class="col-12 d-flex">

            <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                <div class="card-header pl-1 mb-2">

                    <div class="row">

                        <div class="col-12 col-md-3">
                            <h5 class="card-title mb-0"><i class="bi bi-truck mr-2"></i> <span class="titulo">CAE</span></h5>
                        </div>

                        <?php if(!$App->isContratista() ): ?>
                        <div class="col-12 col-md-9 text-right">
                            <a href="javascript:void(0)" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4 btnAsociarEmpresaCAE mr-2"><i class="bi bi-shop pr-2 pl-2"></i> ALTA EMPRESA / AUTÓNOMO</a>
                            <a href="javascript:void(0)" class="btn btn-outline-info text-uppercase rounded-pill shadow pl-2 pr-4 btnNuevoEmpleadoComunidad d-none"><i class="bi bi-person pl-2 pr-2"></i> ALTA EMPLEADO COMUNIDAD</a>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>
                <div class="card-body shadow-inset rounded-lg border mb-1 border-white space-between">

                    <!-- Documentación básica -->
                    <div class="row flex-grow-1 wrapperDocumentacionBasica">

                        <div class="col-12">

                            <div class="card">

                                <div class="card-header pl-0 pt-0"><!--headerListado-->

                                    <div class="row">

                                        <div class="col-12 col-md-9">
                                            <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-folder-check pr-2"></i> Documentación comunidad</h5>
                                        </div>
                            
                                    </div>

                                </div>

                                <div class="card-body pt-0">

                            <!-- Operatoria y aceptación de condiciones -->
                            <?php if($App->isContratista() ): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card mb-0">
                                                <div class="card-body pt-0 pb-0 pl-0">
                                                    <table class="table my-0 hs-tabla w-100 no-clicable tablaOperatoriaCondiciones">
                                                        <tr>
                                                            <td>
                                                                <p class="mb-0"><i class="bi bi-card-checklist pr-2"></i>Declaración responsable ( <a href="<?php echo HOME_URL; ?>public/storage/989eb736182cfee203cedb08768ebf04.pdf" download="declaracion_responsable.pdf" data-idfichero="22" class="btnDescargarFichero"><i class="bi bi-cloud-arrow-down text-primary" style="font-size: 24px;"></i></a> Requiere descarga previa del documento )</p>
                                                            </td>
                                                            <td class="estado">
                                                                <span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>
                                                            </td>
                                                            <td class="fechaSubida text-center small">&nbsp;</td>                                                            
                                                            <td class="text-right">
                                                         
                                                                <a href="javascript:void(0)" class="btnAdjuntarOperatoria ml-2 btnSubidaFichero" data-toggle="tooltip" data-idcomunidad="<?php echo $App->getId();?>" data-idempresa="<?php echo $App->getUserId();?>" data-idempleado="" data-idrequerimiento="1" data-idrelacionrequerimiento="" data-entidad="empresa"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>                                                                
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php endif; ?>    

                                    <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoDocumentacionComunidadCae" id="listadoDocumentacionComunidadCae" data-model="Comunidad">
                                        <thead class="thead"></thead>
                                        <tbody class="tbody"></tbody>
                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>                

<?php if(!$App->isContratista() ): ?>
                    <div class="row empresasComunidadHeader">
                        <div class="col-12">
                            <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-shop pr-2"></i> <span class="tituloEmpresasComunidad">Empresas de la comunidad</span></h5>
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
<?php endif; ?>

                    <!-- Empleados asociados a la comunidad-->
                    <div class="row flex-grow-1 wrapperEmpleadosEmpresaComunidad mt-3" style="display: none;">
                        
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
                                                    <a class="nav-link text-dark btnVerEmpleadosComunidad" data-toggle="tab" href="#tab-2"><i class="bi bi-person mr-2"></i>Empleados</a>
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
                                        <!-- Documentación CAE -->
                                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
                            <!-- Operatoria y aceptación de condiciones -->
                                        <?php if($App->isAdminFincas() ): ?>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card mb-0">
                                                            <div class="card-body pt-0 pb-0 pl-0">
                                                                <table class="table my-0 hs-tabla w-100 no-clicable tablaOperatoriaCondiciones">
                                                                    <tr>
                                                                        <td>
                                                                            <p class="mb-0"><i class="bi bi-card-checklist pr-2"></i>Declaración responsable de <span class="empresaDeclaracion"></span></p>
                                                                        </td>
                                                                        <td class="estado">&nbsp;
                                                                        </td>
                                                                        <td class="fechaSubida text-center small">&nbsp;</td>                                                            
                                                                        <td class="fechaDescarga text-center small">&nbsp;</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endif; ?>    
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoDocumentacionEmpresa" id="listadoDocumentacionEmpresa" data-model="documentacioncae">
                                                    <thead class="thead"></thead>
                                                    <tbody class="tbody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Relación documentos descargados por empresa -->
                                        <div class="row pl-3 pr-3">
                                            <div class="col-12 pl-0 pr-0 pt-0">
                                                <h5 class="text-left br-10 mt-2" style="font-size: 1rem;"><i class="bi bi-file-earmark-arrow-down"></i> Relación de documentos descargados por la empresa</h5>
                                                    <table class="table table-hover my-0 hs-tabla w-100 no-clicable mt-0" name="listadoDocumentacionDescargaEmpresa" id="listadoDocumentacionDescargaEmpresa" data-model="documentacioncae">
                                                        <thead class="thead"></thead>
                                                        <tbody class="tbody"></tbody>
                                                    </table>
                                            </div>
                                        </div>
                                        </div>
                                        <!-- Empleados de la comunidad -->
                                        <div class="tab-pane fade text-center" id="tab-2" role="tabpanel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <table class="table table-hover my-0 hs-tabla w-100 no-clicable" name="listadoEmpleadosComunidad" id="listadoEmpleadosComunidad" data-order='[[ 2, "asc" ]]'  data-model="Empleado">
                                                        <thead class="thead"></thead>
                                                        <tbody class="tbody"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row flex-grow-1">

                                                <div class="col-12 text-center p-2 align-self-start wrapperDocumentacionEmpleado">
                                                    
                                                    <p class="m-0 mensajeInformacion"><i class="bi bi-info-circle"></i> Seleccione un empleado del listado para ver los requerimientos asociados</p>
                                                    <p class="m-0 empleadoRequerimientosInfo font-weight-bold text-uppercase shadow-neumorphic pt-2 pb-2 rounded-pill bg-secondary text-white" style="display:none;"></p>
                                                    
                                                    <div id="wrapperContratistaDocumentacionEmpleado" class="wrapperContratistaDocumentacionEmpleado mt-3 ml-2 mr-2 border rounded-3 row d-none">
                                                        <div class="col-12 contenido"></div>
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


        </div>

    </div>

</div>