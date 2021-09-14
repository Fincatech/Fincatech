<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic p-3">

            <div class="card-header pl-0">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo">Usuario</span></h5>

            </div>
    
            <div class="card-body">

                <form class="form-data form-floating">

                    <!-- Código y CIF -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" class="form-control data text-center" id="cif" name="cif" placeholder="00.000.000-X"  hs-entity="Usuario" hs-field="cif" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="email"><i class="bi bi-envelope pr-2"></i>E-Mail de contacto / Login</label>              
                            <input type="text" class="form-control data text-left" id="emailcontacto" name="emailcontacto" placeholder="example@fincatech.es"  hs-entity="Usuario" hs-field="emailcontacto" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Contraseña</label>              
                            <input type="password" class="form-control data text-left" id="password" name="password" hs-entity="Usuario" hs-field="emailcontacto" maxlength="20" required>                            
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Repetir contraseña</label>              
                            <input type="password" class="form-control data text-left" id="passwordConfirme" name="passwordConfirme" hs-entity="Usuario" hs-field="emailcontacto" maxlength="20" required>                            
                        </div>  
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>ROL / Tipo</label>              
                            <select id="rolid" name="rolid" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="rolid" hs-list-entity="Rol" hs-list-field="nombre" hs-list-value="id"></select>
                        </div>                                                
                    </div>
                    <!-- Nombre -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="nombre"><i class="bi bi-person pr-2"></i>Nombre</label>
                            <input type="text" class="form-control data" id="nombre" name="nombre" placeholder="Comunidad de propietarios Fincatech"  hs-entity="Usuario" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
                        </div>
                    </div>
                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Calle Buenos Aires, 1"  hs-entity="Usuario" hs-field="direccion" required>
                        </div>
                    </div> 
                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                                    <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="28936"  hs-entity="Usuario" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Móstoles"  hs-entity="Usuario" hs-field="localidad" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provincia" name="provincia" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div> 
                    <!-- Teléfono y e-mail -->
                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="28936"  hs-entity="Usuario" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Móvil</label>                          
                            <input type="text" class="form-control data text-center" id="movil" name="movil" placeholder="Móvil"  hs-entity="Usuario" hs-field="movil">
                        </div>                     

                    </div>                    

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>