<!-- Contenedor de la aplicación -->
<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">

                <h5 class="card-title mb-0">Mensajes certificados</h5>

            </div>

            <!-- Menú de acciones -->
            <div class="card-body">
                <div class="row h-100">
                    <!-- Menú de acciones -->
                    <div class="col-12 rounded-lg mb-4">
                        <div class="card mb-1 shadow-neumorphic bg-white p-2">
                            <div class="list-group list-group-horizontal list-group-flush" role="tablist">
                                <a class="list-group-item list-group-item-action active" data-toggle="list" href="#email" role="tab" aria-selected="true"><i class="bi bi-envelope mr-2"></i> E-mail certificado</a>
                                <a class="list-group-item list-group-item-action" data-toggle="list" href="#sms" role="tab" aria-selected="false"><i class="bi bi-chat-left-text mr-2"></i> SMS Certificado</a>
                                <a class="list-group-item list-group-item-action d-none" data-toggle="list" href="#contrato" role="tab" aria-selected="false"><i class="bi bi-file-earmark-check mr-2"></i> Firma contrato</a>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content h-100">

                        <!-- TAB EMAIL -->
                        <div class="tab-pane fade show active h-100 tabEmail" id="email" role="tabpanel">

                            <div class="row flex-grow-1">

                                <div class="col-12 d-flex">

                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/email.php'); ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row flex-grow-1">
                                <div class="col-12 d-flex">
                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                    
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/emaillist.php'); ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>                     

                        <!-- TAB SMS -->
                        <div class="tab-pane fade show h-100 tabSMS" id="sms" role="tabpanel">

                            <div class="row flex-grow-1">

                                <div class="col-12 d-flex">

                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/sms.php'); ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row flex-grow-1">
                                <div class="col-12 d-flex">
                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                    
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/smslist.php'); ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>  
                    
                        <!-- TAB Contrato -->
                        <div class="tab-pane fade show h-100 tabContrato" id="contrato" role="tabpanel">

                            <div class="row flex-grow-1">

                                <div class="col-12 d-flex">

                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/contrato.php'); ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row flex-grow-1">
                                <div class="col-12 d-flex">
                                    <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                                    
                                        <div class="card-body rounded-lg border border-white">
                                            <?php include_once('partials/contratolist.php'); ?>
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