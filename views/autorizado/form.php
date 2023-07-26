<div class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Usuarios autorizados</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form id="form-autorizado" name="form-autorizado" class="form-data form-autorizado form h-100 d-flex space-between" data-model="Autorizado" autocomplete="off">

                    <div class="form-group row mb-2">

                        <div class="col">
                            <label for="nombreusuario">E-mail / Login*</label>
                            <input type="text" class="form-control data form-required" id="email" name="email" placeholder="Correo electrónico" hs-entity="Autorizado" hs-field="email" form-error-message="E-mail" required>
                            <input type="hidden" class="form-control data" id="idadministrador" name="idadministrador" hs-entity="Autorizado" hs-field="idadministrador" value="<?php echo $App->getLoggedUserId();?>">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Contraseña*</label>              
                            <input type="text" class="form-control data text-left" id="password" name="password" maxlength="20" placeholder="Contraseña" hs-entity="Autorizado" hs-field="password" form-error-message="Contraseña" maxlength="20" >                            
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Repetir contraseña</label>              
                            <input type="text" class="form-control text-left" id="passwordConfirme" name="passwordConfirme" maxlength="20" placeholder="Confirmar Contraseña"  >                            
                        </div>  

                    </div>

                    <div class="form-group row mb-2">
                        <div class="col">
                            <label for="nombreusuario">Nombre*</label>
                            <input type="text" class="form-control data form-required" id="nombre" name="nombre" placeholder="Nombre y apellidos" hs-entity="Autorizado" hs-field="nombre" form-error-message="Nombre" required>
                        </div>
                        <div class="col">
                            <label for="nombreusuario">NIF/CIF*</label>
                            <input type="text" class="form-control data form-required" id="cif" name="cif" placeholder="NIF/CIF" hs-entity="Autorizado" hs-field="cif" maxlength="12" form-error-message="NIF/CIF" required>
                        </div>                        
                        <div class="col col-lg-4">
                            <label for="nombreusuario">Teléfono</label>
                            <input type="text" class="form-control data  text-center" maxlength="15" id="nombre" name="nombre" placeholder="Teléfono" hs-entity="Autorizado" hs-field="telefono">
                        </div>                        
                    </div>

                    <div class="form-group row mt-4 mb-2">
                        <div class="col">
                            <h4 ><i class="bi bi-building"></i> Comunidades disponibles para asignar</h4>
                        </div>
                    </div>

                    <!-- Listado de comunidades para asignar -->
                    <?php include_once('comunidades.php'); ?>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

        </div>

    </div>

</div>