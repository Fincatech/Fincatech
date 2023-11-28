<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Administrador de fincas</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="false">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                    <input type="hidden" hs-entity="Usuario" hs-field="rolid" class="data" name="rolid" value="5">
                    <!-- Estado del usuario -->
                    <div class="form-group row mb-4 justify-content-end">
                        <!-- Pendiente de visita -->
                        <div class="col-12 col-md-10 text-left justify-content-end d-flex flex-column align-items-start">
                            <div class="form-check">
                                <input class="form-check-input visitado data" type="checkbox" hs-entity="Usuario" hs-field="visitado" value="" id="visitado">
                                <label class="form-check-label" for="visitado">Visitado</label>
                            </div>
                        </div>
                        <!-- Estado del administrador -->
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Estado</label>
                            <select id="estado" name="estado" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="estado" hs-list-entity="Estado" hs-list-field="nombre" hs-list-value="sId"></select>
                        </div>
                    </div>                    
                    <!-- Código y CIF -->
                    <div class="form-group row mb-4">
                         <div class="col-12 col-md-2 text-left">
                            <label for="codigo" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>Código*</label>
                            <input type="text" class="form-control data text-center text-uppercase" id="codigo" name="codigo" maxlength="10" placeholder="Cód." hs-entity="Usuario" hs-field="codigo" required>
                        </div>                        
                        <div class="col-12 col-md-6">
                            <label for="emailcontacto"><i class="bi bi-envelope pr-2"></i>E-Mail de acceso / Login</label>              
                            <input type="text" class="form-control data text-left" id="email" name="email" placeholder="E-mail" maxlength="255"  hs-entity="Usuario" hs-field="email" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Contraseña</label>              
                            <input type="password" autocomplete="<?php echo time();?>" class="form-control data text-left form-required" id="password" name="password" hs-entity="Usuario" hs-field="password" maxlength="15" required>                            
                        </div>  
                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Fijo oficina</label>                          
                            <input type="text" maxlength="15" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono" hs-entity="Usuario" hs-field="telefono">
                        </div>                                                
                    </div> 
                    <!-- Nombre -->
                    <div class="form-group row mb-4">
                        <div class="col-12 col-md-3">
                            <label for="nombre"><i class="bi bi-person pr-2"></i>Razón social</label>
                            <input type="text" class="form-control data" id="nombre" name="nombre" placeholder="Nombre del administrador" hs-entity="Usuario" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
                        </div>
                        <div class="col text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" maxlength="15" class="form-control data text-center text-uppercase" id="cif" name="cif" placeholder="CIF/NIF" hs-entity="Usuario" hs-field="cif" required>
                        </div>                        
                        <div class="col-12 col-md-3 text-left">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección"  hs-entity="Usuario" maxlength="255" hs-field="direccion" required>
                        </div>
                        <div class="col-4 col-xl">
                            <label for="codpostal" class="text-nowrap"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                            <input type="text" class="form-control data text-center" maxlength="5" id="codpostal" name="codpostal" placeholder="Código postal" hs-entity="Usuario" hs-field="codpostal">
                        </div>
                        <div class="col-4 col-xl">
                            <label for="localidad" class="text-nowrap"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                            <input type="text" class="form-control data" id="localidad" maxlength="70" name="localidad" placeholder="Localidad"  hs-entity="Usuario" hs-field="localidad" required>
                        </div>
                        <div class="col-4 col-xl">
                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provincia" name="provincia" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Usuario" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div>               
                       
                    <!-- Datos del administrador Nombre administrador, teléfono, e-mail -->                   
                    <div class="form-group row mb-4">

                        <div class="col-12 col-md-6">
                            <label for="telefono"><i class="bi bi-person pr-2"></i>Nombre del administrador</label>                          
                            <input type="text" class="form-control data text-left" maxlength="100" id="administradornombre" name="administradornombre" placeholder="Nombre del administrador" hs-entity="Usuario" hs-field="administradornombre">
                        </div>
    
                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Móvil</label>                          
                            <input type="text" class="form-control data text-center" maxlength="15" id="administradormovil" name="administradormovil" placeholder="Móvil" maxlength="15" hs-entity="Usuario" hs-field="administradormovil">
                        </div>  
                        
                        <div class="col">
                            <label for="telefono"><i class="bi bi-envelope pr-2"></i>E-mail</label>                          
                            <input type="text" class="form-control data text-left" id="administradoremail" name="administradoremail" placeholder="E-mail del administrador" maxlength="255" hs-entity="Usuario" hs-field="administradoremail">
                        </div>  

                    </div>

                    <!-- Persona de contacto: Nombre, teléfono, e-mail -->                   
                    <div class="form-group row mb-4">

                        <div class="col-12 col-md-6">
                            <label for="telefono"><i class="bi bi-person pr-2"></i>Persona de contacto</label>                          
                            <input type="text" class="form-control data text-left" maxlength="100" id="contactonombre" name="contactonombre" placeholder="Nombre persona de contacto" hs-entity="Usuario" hs-field="contactonombre">
                        </div>
    
                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" maxlength="15" id="contactotelefono" name="contactotelefono" placeholder="Teléfono" hs-entity="Usuario" hs-field="contactotelefono">
                        </div>

                        <div class="col">
                            <label for="telefono"><i class="bi bi-envelope pr-2"></i>E-mail</label>                          
                            <input type="text" class="form-control data text-left" id="emailcontacto" name="emailcontacto" placeholder="E-mail del administrador" maxlength="255" hs-entity="Usuario" hs-field="emailcontacto">
                        </div>  

                    </div>                  

                <!-- Observaciones -->

                    <div class="form-group row mb-4">

                        <div class="col-12">
                            <label for="observaciones"><i class="bi bi-pencil pr-2"></i>Observaciones</label>                          
                            <textarea id="observaciones" name="observaciones" class="form-control data text-left shadow-inset border-0" rows="5" hs-entity="Usuario" hs-field="observaciones"></textarea>
                        </div>

                    </div>  

                    <?php if($App->getAction() == 'get'): ?>

                        <div class="card-header pl-1 mb-2">

                        <h5 class="card-title mb-0"><span class="titulo">Comunidades Asignadas / Representantes Legales</span></h5>

                        </div>

                    <?php $App->renderView('administrador/tabs'); ?>

                    <?php endif; ?>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>