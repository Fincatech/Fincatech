<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic p-3">

            <div class="card-header pl-0">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo">Spa</span></h5>

            </div>
    
            <div class="card-body">

                <form class="form-data form-floating" autocomplete="off">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                    <!-- Código y CIF -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" class="form-control data text-center" id="cif" name="cif" maxlength="20" placeholder="CIF/NIF"  hs-entity="Spa" hs-field="cif" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="emailcontacto"><i class="bi bi-envelope pr-2"></i>E-Mail de contacto</label>              
                            <input type="text" class="form-control data text-left" id="email" name="email" maxlength="255" placeholder="example@fincatech.es"  hs-entity="Spa" hs-field="email" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="nombre"><i class="bi bi-person pr-2"></i>SPA</label>
                            <input type="text" class="form-control data" id="nombre" name="nombre" placeholder="Nombre del SPA" maxlength="255" hs-entity="Spa" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
                        </div>                        
                    </div>
                    <!-- Persona de contacto -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="nombre"><i class="bi bi-person pr-2"></i>Persona de contacto</label>
                            <input type="text" class="form-control data" id="personacontacto" name="personacontacto" placeholder="Persona de contacto" maxlength="100" hs-entity="Spa" hs-field="personacontacto" aria-label="personacontacto" aria-describedby="addon-nombre" required>
                        </div>
                    </div>
                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección" maxlength="255" hs-entity="Spa" hs-field="direccion" required>
                        </div>
                    </div> 
                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                            <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" maxlength="5" placeholder="Código postal"  hs-entity="Spa" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                            <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Localidad" maxlength="255" hs-entity="Spa" hs-field="localidad" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provincia" name="provincia" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Spa" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div> 
                    <!-- Teléfono y e-mail -->
                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono" maxlength="20" hs-entity="Spa" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Móvil</label>                          
                            <input type="text" class="form-control data text-center" id="movil" name="movil" placeholder="Móvil" maxlength="20" hs-entity="Spa" hs-field="movil">
                        </div>                     

                    </div>                    

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>