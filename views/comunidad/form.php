<div class="row">
<?php if($App->getAction() == 'get' ):?>
    <div class="col-12 pb-4">

        <div class="row h-100">

            <div class="col-12 rounded-lg mb-1">

                <div class="card mb-1 shadow-neumorphic bg-white p-2">

                    <div class="list-group list-group-horizontal list-group-flush" role="tablist">
                        <a class="list-group-item list-group-item-action active d-none" data-toggle="list" href="#datos" role="tab" aria-selected="true"><i class="bi bi-archive mr-2"></i> Datos</a>
                        <a class="list-group-item list-group-item-action enlaceCae" data-toggle="list" href="#empresa" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> CAE</a>
                        <a class="list-group-item list-group-item-action enlaceDocumentacionComunidad" data-toggle="list" href="#documentos" role="tab" aria-selected="false"><i class="bi bi-folder-check"></i> DOC. COMUNIDAD CAE</a>
                        <a class="list-group-item list-group-item-action enlaceRGPD" data-toggle="list" href="#rgpd" role="tab" aria-selected="false"><i class="bi bi-shield-lock mr-2"></i> RGPD</a>
                    </div>
                    
                </div>

            </div>

        </div>

    </div>
<?php endif; ?>
    <div class="col-12">

        <div class="tab-content h-100">

            <!-- Datos de la comunidad -->
            <div class="tab-pane fade show active h-100" id="datos" role="tabpanel">

                <div class="row">

                    <div class="col-12 d-flex">

                        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

                            <div class="card-header pl-1 mb-2">

                                <div class="row">
                                    <div class="col-12 col-md-9">
                                        <h5 class="card-title mb-0"><i class="bi bi-building" style="color: #17a2b8;"></i> <span class="titulo titulo-modulo pl-1"></span></h5>
                                    </div>
                                    <div class="col-12 col-md-3 text-right">
                                        <!-- <a href="<?php // echo APPFOLDER . $App->getController() . "/add" ?>" class="btn btn-outline-success text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> NUEVA <?php echo strtoupper($App->getController() ); ?></a> -->
                                    </div>
                                </div>

                            </div>
                    
                            <div class="card-body shadow-inset rounded-lg border border-white">
                                <?php include_once('tabs/datos.php'); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

<?php if($App->getAction() == 'get' ):?>

            <!-- Datos de documentos asociados -->
            <?php include('tabs/documentos.php'); ?>            

            <!-- Datos de empresa / empleados -->
            <?php include('tabs/cae.php'); ?>            

            <!-- DPD -->
            <?php //include('tabs/dpd.php'); ?>

            <!-- RGPD -->
            <?php include('tabs/rgpd.php'); ?>

<?php endif; ?>

        </div>

    </div>

</div>