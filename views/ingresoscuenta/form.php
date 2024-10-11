<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Ingreso a Cuenta</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating form-ingreso-cuenta" autocomplete="off">

                    <div class="form-group row mb-2">

                        <!-- Fecha del ingreso a cuenta -->
                        <div class="col-12 col-md-4 col-xl-2">
                            <label for="codigo" class="pl-0"><i class="bi bi-calendar pr-2"></i>Fecha del Ingreso</label>
                            <input type="date" form-error-message="Fecha de la anotación" class="form-control data text-center form-required" id="fechaingreso" name="fechaingreso" maxlength="10" placeholder="dd/mm/aaaa"  hs-entity="IngresosCuenta" hs-field="fechaingreso" required>
                         </div>

                        <!-- Nombre del administrador -->
                        <div class="col-12 col-md-4 col-xl-4">
                            <?php $App->RenderView('ingresoscuenta/partials/administrador'); ?>                            
                        </div>

                        <!-- Estado de si ha sido liquidado previamente -->
                         <div class="col-12 col-md-2 d-flex flex-column align-items-center justify-content-end">
                            <div class="form-check">
                                <input id="procesado" name="procesado" type="checkbox" hs-entity="IngresosCuenta" hs-field="procesado" class="data form-check-input disabled" value="">
                                <label class="form-check-label" for="procesado">¿Procesado en liquidación?</label>
                            </div>
                         </div>
                    </div>
                    
                    <!-- Concepto -->
                    <div class="form-gropup row mb-2">

                         <div class="col-12 col-md-6 col-xl-12">
                            <label for="codigo" class="pl-0"><i class="bi bi-info-square pr-2"></i>Concepto</label>
                            <input type="text" form-error-message="Concepto del ingreso a cuenta" class="form-control data text-left form-required" id="concepto" name="concepto" maxlength="250" placeholder="Escriba el concepto para la anotación de ingreso a cuenta" hs-entity="IngresosCuenta" hs-field="concepto" required>
                         </div>

                    </div>

                    <div class="form-gropup row mb-2">
                         <!-- Importe -->
                         <div class="col-12 col-md-4 col-xl-2">
                            <label for="total" class="pl-0"><i class="bi bi-cash-coin pr-2"></i>Importe</label>
                            <input type="text" form-error-message="Importe del ingreso a cuenta"  min="0" max="9999999" step="0.01" class="form-control data text-center form-required" id="total" name="total" maxlength="12" placeholder="" hs-entity="IngresosCuenta" hs-field="total" required>
                         </div>

                    </div> 

                    <!-- Observaciones -->
                    <div class="form-group row mb-2">

                         <div class="col-12 col-md-6 col-xl-12">
                            <label for="observaciones" class="pl-0"><i class="bi bi-blockquote-left pr-2"></i>Observaciones</label>
                            <textarea class="form-control data text-left" id="observaciones" rows="7" name="observaciones" maxlength="3000" placeholder="Observaciones" hs-entity="IngresosCuenta" hs-field="observaciones"></textarea>
                         </div>

                    </div>
                    <!-- Información de Liquidación -->

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>