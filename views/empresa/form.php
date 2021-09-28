<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo">Empresa</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="off">

                    <!-- Estado de la empresa -->
                    <div class="form-group row mb-2 justify-content-end">
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Estado</label>
                            <select id="estado" name="estado" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Empresa" hs-field="estado" hs-list-entity="Estado" hs-list-field="nombre" hs-list-value="sId"></select>
                        </div>
                    </div>

                    <!-- CIF, Nombre -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
                            <input type="text" class="form-control data text-center form-required" id="cif" name="cif" placeholder="CIF"  hs-entity="Empresa" hs-field="cif" required>
                        </div>
                        <div class="col-12 col-md-10 text-left">
                            <label for="razonsocial"><i class="bi bi-person pr-2"></i>Nombre / Razón social</label>              
                            <input type="text" class="form-control data text-left form-required" id="razonsocial" name="razonsocial" hs-entity="Empresa" hs-field="razonsocial" maxlength="100" required>
                        </div>                        
                    </div>

                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Calle Buenos Aires, 1"  hs-entity="Empresa" hs-field="direccion" required>
                        </div>
                    </div> 

                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                            <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="28936"  hs-entity="Empresa" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Móstoles"  hs-entity="Empresa" hs-field="localidad" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provincia" name="provincia" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Empresa" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div> 

                    <!-- Teléfono y e-mail -->
                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono"  hs-entity="Empresa" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-10">
                            <label for="email"><i class="bi bi-envelope pr-2"></i>Email</label>                          
                            <input type="text" class="form-control data text-left" id="email" name="email" placeholder="Email"  hs-entity="Empresa" hs-field="email">
                        </div>                     

                    </div>                    

                    <!-- Si es una edición y además es sudo, listamos los empleados de la empresa -->
                    <?php if($App->getAction() == 'get' && $App->getUserRol() == 'ROLE_SUDO'): ?>
                    <div class="form-group row mb-2 mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-title">Empleados asociados a la empresa</div>
                                <div class="card-body p-0">
                                    <table class="table table-hover my-0 hs-tabla w-100" name="listadoEmpleadosEmpresa" id="listadoEmpleadosEmpresa" data-model="empleado">
                                        <thead class="thead"></thead>
                                        <tbody class="tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>