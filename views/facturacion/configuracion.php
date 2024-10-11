<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i style="color: #17a2b8;" data-feather="settings"></i> <span class="titulo titulo-modulo">Configuración</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating formConfiguracion" data-model="Configuracion" id="formConfiguracion" name="formConfiguracion" autocomplete="false">
                  
                    <!-- Serie de facturación -->
                    <div class="form-group row mb-4">

                        <div class="col-12 col-md-3 text-left">
                            <label for="prefseriefact" class="pl-0"><i class="bi bi-receipt pr-2"></i>Prefijo Serie de Facturación</label>
                            <input type="text" class="form-control data text-center" id="prefseriefact" name="prefseriefact" maxlength="5" placeholder="Prefijo serie Facturación" hs-entity="Configuracion" hs-field="prefseriefact" required>
                        </div>   

                         <div class="col-12 col-md-3 text-left">
                            <label for="seriefacturacion" class="pl-0"><i class="bi bi-receipt pr-2"></i>Serie de Facturación</label>
                            <input type="number" min="1" max="99999999999" step="1" class="form-control data text-center" id="seriefacturacion" name="seriefacturacion" maxlength="12" placeholder="Serie Facturación" hs-entity="Configuracion" hs-field="seriefacturacion" required>
                        </div>     
                        
                    </div>

                    <!-- Serie de facturación rectificativa -->
                    <div class="form-group row mb-4">

                        <div class="col-12 col-md-3 text-left">
                            <label for="prefseriefacrect" class="pl-0"><i class="bi bi-receipt pr-2"></i>Prefijo Serie de Facturación Rectificativas</label>
                            <input type="text" class="form-control data text-center" id="prefseriefacrect" name="prefseriefacrect" maxlength="5" placeholder="Prefijo serie Facturación rectificativas" hs-entity="Configuracion" hs-field="prefseriefacrect" required>
                        </div>   

                        <!-- Serie de facturación Rectificativas-->
                        <div class="col-12 col-md-3 text-left">
                            <label for="serierectificativa"><i class="bi bi-receipt-cutoff pr-2"></i>Serie de Facturación Rectificativas</label>              
                            <input type="number" min="1" max="99999999999" step="1" class="col-2 form-control data text-center" id="serierectificativa" name="serierectificativa" placeholder="Serie Facturación Rectificativas" maxlength="12"  hs-entity="Configuracion" hs-field="serierectificativa" required>
                        </div>   

                    </div>                     
                    
                    <div class="form-group row mb-4">
                   
                        <!-- % IVA -->
                        <div class="col-12 col-md-2 text-left">
                            <label for="impuesto" class="text-center"><i class="bi bi-percent pr-2"></i>Impuesto (I.V.A.)</label>              
                            <input type="number" min="0" max="99" step="0.01" class="form-control data text-center" id="impuesto" name="impuesto" placeholder="%" maxlength="2" hs-entity="Configuracion" hs-field="impuesto" required>
                        </div> 

                    </div>               
                       
                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>