<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Banco</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating form-banco" autocomplete="off">

                    <!-- Nombre del banco -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-6 col-xl-10">
                            <label for="nombre"><i class="bi bi-bank pr-2"></i>Nombre del banco</label>                
                            <input type="text" class="form-control data form-required" id="nombre" name="nombre" maxlength="150" placeholder="Nombre de la entidad"  hs-entity="Bank" hs-field="nombre" required>
                        </div>

                    </div> 

                    <div class="form-group row mb-2">
                        <!-- Código Entidad -->
                        <div class="col-12 col-md-6 col-xl-2 text-left">
                            <label for="codigo" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>Cód. Entidad*</label>
                            <input type="text" class="form-control data text-center form-required" id="codigo" name="codigo" maxlength="11" placeholder="Código Entidad"  hs-entity="Bank" hs-field="codigo" required>
                        </div>

                        <!-- BIC/SWIFT -->
                        <div class="col-12 col-md-6 col-xl-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>BIC/SWIFT*</label>
                            <input type="text" class="form-control data text-center form-required" id="bic" name="bic" maxlength="11" placeholder="BIC/SWIFT"  hs-entity="Bank" hs-field="bic" required>
                        </div>                        
                    </div>

                    <!-- IBAN -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="iban" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>IBAN</label>
                            <input type="text" class="form-control data text-center" id="iban" name="iban" maxlength="20" placeholder="IBAN"  hs-entity="Bank" hs-field="iban" required>
                        </div>
                    </div>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>