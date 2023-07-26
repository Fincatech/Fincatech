<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">

                <h5 class="card-title mb-0"><i class="bi bi-shield-lock"></i> Panel de validación de certificados digitales</h5>

            </div>
            
            <div class="card-body pt-0 pb-0">

                <div class="row h-100">
                    
                    <div class="col-12 space-between d-flex">

                        <div class="row">

                            <!-- Menú de navegación -->
                            <div class="col-12 pb-4">
                                <div class="card mb-1 shadow-neumorphic bg-white p-2">
                                    <div class="list-group list-group-horizontal list-group-flush" role="tablist">
                                        <a class="list-group-item list-group-item-action active enlaceComunidades" data-toggle="list" href="#comunidades" role="tab" aria-selected="true"><i class="bi bi-archive mr-2"></i> Pendiente de revisión</a>
                                        <!-- <a class="list-group-item list-group-item-action enlaceCertificadosPendientes" data-toggle="list" href="#certificadospendientes" role="tab" aria-selected="false"><i class="bi bi-shield-lock mr-2"></i> Pendientes</a> -->
                                        <a class="list-group-item list-group-item-action enlaceCertificadosSolicitados" data-toggle="list" href="#certificadossolicitados" role="tab" aria-selected="false"><i class="bi bi-shield-check mr-2"></i> Certificados aprobados y solicitados a Uanataca</a>
                                    </div>                            
                                </div>
                            </div>

                        </div>

                        <div class="row flex-grow-1">
                            <!-- Contenido navegación -->
                            <div class="col-12">

                                <div class="tab-content h-100">

                                    <!-- Comunidades pendientes de revisión -->
                                    <?php include_once('partials/comunidades.php'); ?>
                            
                                    <!-- Datos de documentos asociados -->
                                    <?php include_once('partials/certificadosemitidos.php'); ?>

                                    <!-- Certificados pendientes de solicitud -->
                                    <?php //include_once('partials/certificadospendientes.php'); ?>
                                    
                                </div>

                            </div>

                        </div>

                    </div>

                </div>    

            </div>      
            
        </div>            

    </div>        

</div>