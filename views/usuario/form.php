<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Usuario</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="chrome-off">

                    <!-- Estado del usuario -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Estado</label>
                            <select id="estado" name="estado" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="estado" hs-list-entity="Estado" hs-list-field="nombre" hs-list-value="sId"></select>
                        </div>
                    </div>
                    <!-- CIF, Email, Contraseña, Repetir Contraseña, ROL -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" class="form-control data text-center form-required" id="cif" name="cif" placeholder="CIF/NIF" hs-entity="Usuario" hs-field="cif" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="email"><i class="bi bi-envelope pr-2"></i>E-Mail de contacto / Login*</label>              
                            <input type="text" class="form-control data text-left form-required" id="email" name="email" placeholder="Correo electrónico"  hs-entity="Usuario" hs-field="email" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Contraseña*</label>              
                            <input type="password" class="form-control data text-left form-required" id="password" name="password" maxlength="20" placeholder="Contraseña" hs-entity="Usuario" hs-field="password" maxlength="20" required>                            
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Repetir contraseña</label>              
                            <input type="password" class="form-control text-left" id="passwordConfirme" name="passwordConfirme" maxlength="20" required>                            
                        </div>  
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>ROL / Tipo*</label>              
                            <select id="rolid" name="rolid" class="select-data data custom-select form-control selectpicker form-required" data-live-search="true" hs-entity="Usuario" hs-field="rolid" hs-list-entity="Rol" hs-list-field="nombre" hs-list-value="id"></select>
                        </div>                                                
                    </div>
                    <!-- Nombre -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="nombre"><i class="bi bi-person pr-2"></i>Nombre</label>
                            <input type="text" class="form-control data" id="nombre" name="nombre" maxlength="100" placeholder="Nombre de usuario" hs-entity="Usuario" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
                        </div>
                    </div>
                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" maxlength="255" placeholder="Dirección" hs-entity="Usuario" hs-field="direccion" required>
                        </div>
                    </div> 
                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                                    <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="Código postal" maxlength="5" hs-entity="Usuario" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                <input type="text" class="form-control data" id="localidad" name="localidad" maxlength="100" placeholder="Localidad" hs-entity="Usuario" hs-field="localidad" required>
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
                            <input type="text" class="form-control data text-center" maxlength="20"  id="telefono" name="telefono" placeholder="Teléfono"  hs-entity="Usuario" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="movil"><i class="bi bi-phone pr-2"></i>Móvil</label>                          
                            <input type="text" class="form-control data text-center" maxlength="20" id="movil" name="movil" placeholder="Móvil" hs-entity="Usuario" hs-field="movil">
                        </div>                     

                    </div>                    
                    <!-- Datos API Mensatek -->
                    <h4 class="mt-4 font-weight-bold">API Mensatek - E-mails certificados</h4>

                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-6">
                            <label for="apiuser"><i class="bi bi-person pr-2"></i>Usuario API</label>                          
                            <input type="text" class="form-control data text-center" autocomplete="chrome-off" maxlength="70"  id="apiuser" name="apiuser" placeholder="Usuario API Mensatek"  hs-entity="Usuario" hs-field="apiuser">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="apitoken"><i class="bi bi-key pr-2"></i>API Token</label>                          
                            <input type="text" class="form-control data text-center" autocomplete="chrome-off" maxlength="70" id="apitoken" name="apitoken" placeholder="Api Token Mensatek" hs-entity="Usuario" hs-field="apitoken">
                        </div>                     

                    </div> 
                    
                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>
<script>
    let tagArr = document.getElementsByTagName("input");
    for (let i = 0; i < tagArr.length; i++) {
    tagArr[i].autocomplete = 'off';
    }
</script>