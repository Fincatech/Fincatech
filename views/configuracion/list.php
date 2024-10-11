<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i style="color: #17a2b8;" data-feather="settings"></i> <span class="titulo titulo-modulo">Configuración</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating formConfiguracion" data-model="Configuracion" id="formConfiguracion" name="formConfiguracion" autocomplete="false">
                  
                    <div class="row">

                        <!-- Series de Facturación -->
                        <div class="col-12 col-md-4">

                            <div class="row">
                                <div class="col-12">
                                    <p class="card-title">Series de facturación</p>
                                </div>
                            </div>

                            <!-- Serie de facturación -->
                            <div class="form-group row mb-4">

                                <div class="col-12 col-md-6 text-left">
                                    <label for="prefseriefact" class="pl-0"><i class="bi bi-receipt pr-2"></i>Prefijo Factura</label>
                                    <input type="text" class="form-control data form-required text-center" form-error-message="Prefijo Serie de facturación" id="prefseriefact" name="prefseriefact" maxlength="5" placeholder="Prefijo serie Facturación" hs-entity="Configuracion" hs-field="prefseriefact" required>
                                </div>   

                                <div class="col-12 col-md-6 text-left">
                                    <label for="seriefacturacion" class="pl-0"><i class="bi bi-receipt pr-2"></i>Serie Factura</label>
                                    <input type="number" min="1" max="99999999999" step="1"form-error-message="Serie de facturación"  class="form-control form-required data text-center" id="seriefacturacion" name="seriefacturacion" maxlength="12" placeholder="Serie Facturación" hs-entity="Configuracion" hs-field="seriefacturacion" required>
                                </div>     
                                
                            </div>

                            <!-- Serie de facturación rectificativa -->
                            <div class="form-group row mb-4">

                                <div class="col-12 col-md-6 text-left">
                                    <label for="prefseriefacrect" class="pl-0"><i class="bi bi-receipt pr-2"></i>Prefijo Factura Rectificativa</label>
                                    <input type="text" class="form-control form-required data text-center"form-error-message="Prefijo Serie de facturación rectificativa"  id="prefseriefacrect" name="prefseriefacrect" maxlength="5" placeholder="Prefijo serie Facturación rectificativas" hs-entity="Configuracion" hs-field="prefseriefacrect" required>
                                </div>   

                                <!-- Serie de facturación Rectificativas-->
                                <div class="col-12 col-md-6 text-left">
                                    <label for="serierectificativa"><i class="bi bi-receipt-cutoff pr-2"></i>Serie Factura Rectificativa</label>              
                                    <input type="number" min="1" max="99999999999" step="1" form-error-message="Serie de facturación rectificativa"  class="form-control form-required data text-center" id="serierectificativa" name="serierectificativa" placeholder="Serie Facturación Rectificativas" maxlength="12"  hs-entity="Configuracion" hs-field="serierectificativa" required>
                                </div>   

                            </div> 

                        </div>

                        <!-- Nomenclatura de servicios -->
                        <div class="col-12 col-md-4">

                            <div class="row">
                                <div class="col-12">
                                    <p class="card-title">Nomenclatura para referencia contrato</p>
                                </div>
                            </div>

                            <!-- Nomenclatura de servicios -->
                            <div class="form-group row mb-4">
                        
                                <!-- CAE -->
                                <div class="col-12 col-md-4 text-left">
                                    <label for="nomcae" class="text-center"><i class="bi bi-alphabet-uppercase pr-2"></i>CAE</label>              
                                    <input type="text" form-error-message="Nomenclatura CAE"  class="form-control form-required data text-center" id="nomcae" name="nomcae" placeholder="CAE" maxlength="10" hs-entity="Configuracion" hs-field="nomcae" required>
                                </div> 

                                <!-- DOC CAE -->
                                <div class="col-12 col-md-4 text-left">
                                    <label for="nomdoccae" class="text-center"><i class="bi bi-alphabet-uppercase pr-2"></i>DOC CAE</label>              
                                    <input type="text" form-error-message="Nomenclatura DOC CAE"  class="form-control form-required data text-center" id="nomdoccae" name="nomdoccae" placeholder="DOCCAE" maxlength="10" hs-entity="Configuracion" hs-field="nomdoccae" required>
                                </div> 

                                <!-- DPD -->
                                <div class="col-12 col-md-4 text-left">
                                    <label for="nomdpd" class="text-center"><i class="bi bi-alphabet-uppercase pr-2"></i>DPD</label>              
                                    <input type="text" form-error-message="Nomenclatura DPD"  class="form-control form-required data text-center" id="nomdpd" name="nomdpd" placeholder="DPD" maxlength="10" hs-entity="Configuracion" hs-field="nomdpd" required>
                                </div> 

                                <!-- Certificados digitales -->
                                <div class="col-12 col-md-4 text-left">
                                    <label for="nomcd" class="text-center"><i class="bi bi-alphabet-uppercase pr-2"></i>Cert. digitales</label>              
                                    <input type="text" form-error-message="Nomenclatura Certificados digitales"  class="form-control form-required data text-center" id="nomcd" name="nomcd" placeholder="CD" maxlength="10" hs-entity="Configuracion" hs-field="nomcd" required>
                                </div> 

                            </div>

                            <div class="form-group row mb-4">


                                

                            </div>                           

                        </div>
                        
                        <!-- Transferencias SEPA -->
                        <div class="col-12 col-md-4">

                                <div class="row">
                                    <div class="col-12">
                                        <p class="card-title">Transferencias SEPA</p>
                                    </div>
                                </div>

                                <!-- Créditor ID para las transferencias -->
                                <div class="form-group row mb-4">

                                    <!-- Nombre empresa -->
                                    <div class="col-12 col-xl-6 text-left">
                                        <label for="sepaempresa" class="text-center"><i class="bi bi-credit-card-2-front pr-2"></i>Nombre Empresa</label>              
                                        <input type="text" form-error-message="Nombre de la empresa que figurará en el fichero SEPA" class="form-control form-required data text-center" id="creditorid" name="creditorid" placeholder="Nombre de la empresa que figurará en el fichero SEPA" maxlength="70" hs-entity="Configuracion" hs-field="sepaempresa" required>
                                    </div> 

                                    <!-- Creditor ID -->
                                    <div class="col-12 col-xl-6 text-left">
                                        <label for="creditorid" class="text-center"><i class="bi bi-credit-card-2-front pr-2"></i>Creditor ID</label>              
                                        <input type="text" form-error-message="Creditor ID necesario para la generación de remesas SEPA" class="form-control form-required data text-center" id="creditorid" name="creditorid" placeholder="Creditor ID (SEPA)" maxlength="20" hs-entity="Configuracion" hs-field="creditorid" required>
                                    </div> 

                                </div>

                                <!-- Tipo de adeudo -->
                                <div class="form-group row mb-4">                                 

                                <div class="col-12 col-xl-6 text-left">
                                        <label for="tipoadeudo" class="text-center"><i class="bi bi-credit-card-2-front pr-2"></i>Tipo de adeudo</label>              
                                        <select id="tipoadeudo" name="tipoadeudo" class="custom-select selectpicker form-control data form-data form-required br-8 shadow-inset" hs-entity="Configuracion" hs-field="tipoadeudo">
                                            <option value="D">Adeudo directo</option>
                                            <option value="T">Transferencia de créditos</option>
                                        </select>
                                        <!-- <input type="text" form-error-message="Creditor ID necesario para la generación de remesas SEPA" class="form-control form-required data text-center" id="creditorid" name="creditorid" placeholder="Creditor ID (SEPA)" maxlength="20" hs-entity="Configuracion" hs-field="creditorid" required> -->
                                    </div> 

                                </div>

                        </div>   

                    </div>

                    <!-- Configuración general -->
                    <div class="row">

                        <div class="col-12 col-md-4">

                            <div class="row">
                                <div class="col-12">
                                    <p class="card-title">Configuración General</p>
                                </div>
                            </div>                        

                            <!-- Impuestos -->
                            <div class="form-group row mb-4">
                        
                                <!-- % IVA -->
                                <div class="col-12 col-md-4 text-left">
                                    <label for="impuesto" class="text-center"><i class="bi bi-percent pr-2"></i>Impuesto (I.V.A.)</label>              
                                    <input type="number" min="0" max="99" step="0.01"form-error-message="% de impuestos"  class="form-control form-required data text-center" id="impuesto" name="impuesto" placeholder="%" maxlength="2" hs-entity="Configuracion" hs-field="impuesto" required>
                                </div> 

                            </div>     

                        </div>

                    </div>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>