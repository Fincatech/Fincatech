<?php include(VIEWS_DIR . 'contratista/mensajergpd.php'); ?>

<div class="row">
<?php if($App->getAction() == 'get' ):?>
    <div class="col-12 pb-4">

        <div class="row h-100">

            <div class="col-12 rounded-lg mb-1">

                <h3 class="card-title ml-1"><i class="bi bi-building" style="color: #17a2b8;"></i> <span class="titulo titulo-modulo pl-0"></span></h3>

                <div class="card mb-1 shadow-neumorphic bg-white p-2">

                    <div class="list-group list-group-horizontal list-group-flush fincatech--enlaces-comunidd" role="tablist">
                        <a class="list-group-item list-group-item-action active d-none fincatech--datos" data-toggle="list" href="#datos" role="tab" aria-selected="true"><i class="bi bi-archive mr-2"></i> Datos</a>
                        <a class="list-group-item list-group-item-action enlaceCae fincatech--cae" data-toggle="list" href="#empresa" role="tab" aria-selected="false"><i class="bi bi-truck mr-2"></i> CAE</a>
                        <!-- <a class="list-group-item list-group-item-action enlaceDocumentacionComunidad" data-toggle="list" href="#documentos" role="tab" aria-selected="false"><i class="bi bi-folder-check"></i> DOC. COMUNIDAD CAE</a> -->
                        <?php if( $App->isContratista()): ?>
                            <a class="list-group-item list-group-item-action enlaceEmpleadosComunidadContratista" data-toggle="list" href="#empleadoscomunidadcontratista" role="tab" aria-selected="false"><i class="bi bi-people-fill mr-2"></i> Empleados Comunidad</a>
                        <?php endif; ?>
                        <?php if( !$App->isContratista() ): ?>
                            <a class="list-group-item list-group-item-action enlaceRGPD fincatech--rgpd" data-toggle="list" href="#rgpd" role="tab" aria-selected="false"><i class="bi bi-shield-lock mr-2"></i> RGPD</a>
                            <!-- <a class="list-group-item list-group-item-action enlaceCertificadoDigital" data-toggle="list" href="#certificadodigital" role="tab" aria-selected="false"><i class="bi bi-patch-check mr-2"></i> Certificado Digital</a> -->
                        <?php endif; ?>
                        <?php if( $App->isContratista() ): ?>
                            <a class="list-group-item list-group-item-action enlaceEmpresasExternas" data-toggle="list" href="#empresasexternascontratista" role="tab" aria-selected="false"><i class="bi bi-shop mr-2"></i> Empresas Concurrentes</a>
                        <?php endif; ?>                        
                    </div>
                    
                </div>

            </div>

        </div>

    </div>
<?php endif; ?>
    <div class="col-12">

        <div class="tab-content h-100">

<?php if( isset($_GET['view']) || $App->getAction() == 'add' ): ?>
    <!-- Datos de la comunidad -->
    <div class="tab-pane fade show active h-100 tabDatos" id="datos" role="tabpanel">

        <div class="row">

            <div class="col-12 d-flex">

                <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3 card-principal">

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
       
<?php endif; ?>


<?php if($App->getAction() == 'get' ):?>

            <!-- Datos de documentos asociados -->
            <?php include('tabs/documentos.php'); ?>            

            <!-- Datos de empresa / empleados -->
            <?php include('tabs/cae.php'); ?>            

            <!-- DPD -->
            <?php //include('tabs/dpd.php'); ?>

            <!-- Empresas externas -->
            <?php include('tabs/empresasexternas.php'); ?>

            <!-- Comunidades del empleado -->
            <?php include('tabs/empleadoscomunidad.php'); ?>            

            <!-- RGPD -->
            <?php include('tabs/rgpd.php'); ?>

            <!-- CERTIFICADO DIGITAL -->
            <?php //include('tabs/certificadodigital.php'); ?>

<?php endif; ?>

        </div>

    </div>

</div>