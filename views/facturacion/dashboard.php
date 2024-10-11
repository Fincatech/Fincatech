<!-- Contenedor de la aplicación -->
<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-inset" style="border-radius:20px;">

            <!-- <div class="card-header bg-transparent">

                <h5 class="card-title mb-0 text-center">Facturación</h5>

            </div> -->
            
            <div class="card-body dashboard-facturacion">

                <div class="row h-100">

                    <div class="col-12 pr-md-4 pr-0">

                        <!-- Facturación -->
                        <div class="row">
                            
                            <!-- Facturación Anual -->
                            <div class="col-12 col-md-6 col-lg-3 ps-3 pe-3">

                                <?php $App->renderView('facturacion/partials/dashboard/facturacion_anual'); ?>

                            </div>      

                            <!-- Facturación Mes en curso -->
                            <div class="col-12 col-md-6 col-lg-5 ps-3 pe-3">

                                <?php $App->renderView('facturacion/partials/dashboard/facturacion_mes'); ?>

                            </div>    
                            
                            <!-- Acciones de facturación -->
                            <div class="col-12 col-md-6 col-lg-4 ps-3 pe-3">

                                <?php $App->renderView('facturacion/partials/dashboard/estadisticas'); ?>

                            </div>                

                        </div>   
                        
                        <!-- Listados -->
                        <div class="row">

                            <!-- Facturas emitidas -->
                            <div class="col-12 col-md-6 col-lg-6 ps-3 pe-3">
                                <?php $App->renderView('facturacion/partials/dashboard/facturas_emitidas'); ?>
                            </div>  

                            <!-- Facturas devueltas -->
                            <div class="col-12 col-md-6 col-lg-6 ps-3 pe-3">
                                <?php $App->renderView('facturacion/partials/dashboard/facturas_devueltas'); ?>
                            </div>  

                        </div>   

                        <!-- Remesas -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="col-12 ps-3 pe-3">
                                    <?php $App->renderView('facturacion/partials/dashboard/remesas'); ?>
                                </div>  
                            </div>
                        </div>
                        
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>