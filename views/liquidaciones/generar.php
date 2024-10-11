<?php $App->renderView('facturacion/partials/emision/progreso'); ?>

<div class="row flex-grow-1">

    <div class="col-12 d-flex position-relative">

        <div class="card flex-fill pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header">

                <h5 class="card-title mb-0"><span class="titulo titulo-modulo pl-0"><i class="bi bi-receipt pr-2"></i> Generación Informe de Liquidación</span></h5>

            </div>
    
            <div class="card-body rounded-lg border mb-1 border-white d-flex pt-0">

                <form class="form-data form-floating form-facturacion d-flex flex-grow-1" autocomplete="off">

                <div class="row flex-grow-1">
                    <!-- Opciones de la generación -->
                    <div class="col-12 col-lg-8">

                        <!-- Servicio a facturar -->
                        <div class="form-group row mb-2">


                            <div class="col-12 col-lg-5">
                                <?php $App->RenderView('facturacion/partials/emision/administrador'); ?>
                            </div>                       

                            <!-- Fecha desde -->
                            <div class="col-12 col-lg-3">
                                <div class="form-group row">
                                    <input type="date" id="datefrom" name="datefrom" />
                                </div>
                            </div>                              

                            <!-- Fecha hasta -->
                            <div class="col-12 col-lg-3">
                                <input type="date" id="dateto" name="dateto" />
                            </div>                              


                        </div> 


                        <!-- Servicios a facturar -->
                        <div class="form-group row mb-2">
                            <div class="col-12 pt-2">
                                <?php $App->RenderView('facturacion/partials/emision/servicios'); ?>
                            </div>
                        </div>

                        <!-- Cuerpo del E-mail -->
                        <div class="form-group row mb-2">
                            <div class="col-12 pt-2">
                                <label for="emailbody" class="font-weight-bold"><i class="bi bi-blockquote-left pr-2"></i>Cuerpo del e-mail (Opcional)</label>  
                                <?php $App->RenderView('comunes/editor_email'); ?>
                            </div>
                        </div>

                    </div>

                    <!-- Información adicional -->
                    <div class="col-12 col-lg-4">
                        <div class="row h-100">
                            <div class="col-12 br-10 p-3 d-flex flex-column" style="background:#f7f7fc;">
                                <?php $App->RenderView('facturacion/partials/emision/opciones'); ?>
                                <?php $App->RenderView('facturacion/partials/emision/info'); ?>
                            </div>
                        </div>    
                    </div>

                </div>
                  

                </form>

            </div>

    </div>

</div>