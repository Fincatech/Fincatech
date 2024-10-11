<div class="tab-pane fade flex-grow-1 space-between col-12 show active" id="rgpddocumentacionbasica" role="tabpanel">
    
    <div class="row">

        <div class="col-12 d-flex">

            <div class="card flex-fill pt-2">

                <!-- <div class="card-header pl-1 mb-2">

                    <div class="row mb-3">

                        <div class="col-12">
                            <h5 class="card-title mb-0"><i class="bi bi-chat-right-text mr-2"></i> <span class="titulo">Documentación básica</span></h5>
                        </div>

                    </div>

                </div> -->
        
                <div class="card-body p-0 rounded-lg mt-3">

                        <?php if($App->isAdminFincas() ): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card mb-0">
                                                <div class="card-body pt-0 pb-0 pl-0">
                                                    <table class="table my-0 hs-tabla w-100 no-clicable tablaContratoAdministracion">
                                                        <tr>
                                                            <td>
                                                                <p class="mb-0"><i class="bi bi-card-checklist pr-2"></i>Contrato administración de Fincas con Comunidad de Propietarios</p>
                                                            </td>
                                                            <td class="estado">
                                                                <span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>
                                                            </td>
                                                            <td class="text-right">
                                                                <a href="<?php echo HOME_URL; ?>public/storage/contrato_comunidad_de_propietarios_con_administrador_de_fincas.docx" download="Contrato Administración de Fincas con Comunidad de Propietarios.docx" class="btnDescargarFichero mr-2" aaaa><i class="bi bi-cloud-arrow-down text-primary" style="font-size: 24px;"></i></a>                                                                
                                                                <a href="javascript:void(0)" class="btnAdjuntarContrato ml-2" data-toggle="tooltip" data-idcomunidad="<?php echo $App->getId();?>" data-idadministrador="<?php echo $App->getUserId();?>" data-idempresa="" data-idempleado="" data-idrequerimiento="32" data-idrelacionrequerimiento="" data-entidad="comunidad"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>                                                                
                                                            </td>                                                            
                                                            <td class="fechaSubida text-center small">&nbsp;</td>                                                            
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php endif; ?> 

                    <div class="row">

                        <div class="col-12">

                            <table data-search="false" class="table table-bordered table-hover my-0 hs-tabla w-100 no-clicable" name="listadoDocumentacionBasica" id="listadoDocumentacionBasica" data-model="Requerimiento">
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
