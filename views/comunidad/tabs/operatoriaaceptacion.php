    <!-- Operatoria y aceptación de condiciones -->
    <?php if($App->isContratista() ): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0">
                        <div class="card-body pt-0 pb-0 pl-0">
                            <table class="table my-0 hs-tabla w-100 no-clicable tablaOperatoriaCondiciones">
                                <tr>
                                    <td>
                                        <p class="mb-0"><i class="bi bi-card-checklist pr-2"></i>Declaración responsable (Requiere descarga previa del documento <a href="<?php echo HOME_URL; ?>public/storage/18763bf7e24925eb3b1e1540acc2a9e3.pdf" download="operatoria-aceptacion-de-condiciones.pdf" class="btnDescargarFichero mr-2"><i class="bi bi-cloud-arrow-down text-primary" style="font-size: 24px;"></i></a>                                                                )</p>
                                    </td>
                                    <td class="estado">
                                        <span class="badge rounded-pill bg-danger pl-3 pr-3 pt-2 pb-2 d-block">No adjuntado</span>
                                    </td>
                                    <td class="fechaSubida text-center">&nbsp;</td>                                                            
                                    <td class="text-right">
                                        
                                        <a href="javascript:void(0)" class="btnAdjuntarFicheroDocumento btnAdjuntarOperatoria ml-2" data-toggle="tooltip" data-idcomunidad="<?php echo $App->getId();?>" data-idempresa="<?php echo $App->getUserId();?>" data-idempleado="" data-idrequerimiento="1" data-idrelacionrequerimiento="" data-entidad="empresa"><i class="bi bi-cloud-arrow-up text-success" style="font-size: 24px;"></i></a>                                                                
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    <?php endif; ?> 