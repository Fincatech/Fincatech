<?php
    if($App->getUriValue('id') === false)
    {
        $formAction = 'add';
        $empleadoId = '';
    }else{
        $formAction = 'get';
        $empleadoId = $App->getUriValue('id');
    }
?>
<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill"></i> <span class="titulo titulo-modulo">Nuevo Empleado</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating form-empleado" name="form-empleado" id="form-empleado" data-model="Empleado" data-action="<?php echo $formAction; ?>" data-id="<?php echo $empleadoId; ?>" autocomplete="off">

                    <input type="hidden" id="idempresa" name="idempresa" class="data" hs-entity="empleadoempresa" hs-entity-related="empleadoempresa" hs-field="idempresa">

                    <div class="form-group row mb-2 justify-content-start">
                        
                        <!-- Estado del empleado -->
                        <div class="col-12 col-md-2 text-left">
                            <label for="estado"><i class="bi bi-geo-alt pr-2"></i>Estado</label>
                            <select id="estado" data-placeholder="Seleccione una opción" name="estado" class="select-data data custom-select form-control selectpicker" data-live-search="false" hs-entity="Empleado" hs-field="estado" hs-list-entity="Estado" hs-list-field="nombre" hs-list-value="sId"></select>
                        </div>

                        <!-- Puesto de trabajo -->
                        <div class="col-12 col-md-8">
                            <label for="direccion"><i class="bi bi-wrench pr-2"></i>Puesto de trabajo</label>                        
                            <select id="idtipopuestoempleado" name="idtipopuestoempleado" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Empleado" hs-field="idtipopuestoempleado" hs-list-entity="Tipopuestoempleado" hs-list-field="nombre" hs-list-value="id"></select>
                        </div> 

                        <!-- Fecha de alta -->
                        <div class="col-12 col-md-2">
                            <label for="fechaalta"><i class="bi bi-wrench pr-2"></i>Fecha de alta</label>                        
                            <input type="date" class="form-control" id="fechaalta" name="fechaalta" hs-entity="Empleado" hs-field="fechaalta" placeholder="dd/mm/aaaa">
                        </div> 

                    </div>

                    <!-- DNI/NIE, Nombre -->
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2 text-left">
                            <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>DNI/NIE*</label>
                            <input type="text" class="form-control data text-center form-required" id="numerodocumento" name="numerodocumento" maxlength="12" placeholder="Documento de identidad" hs-entity="Empleado" hs-field="numerodocumento" required>
                        </div>
                        <div class="col-12 col-md-10 text-left">
                            <label for="razonsocial"><i class="bi bi-person pr-2"></i>Nombre y apellidos*</label>              
                            <input type="text" class="form-control data text-left form-required" id="nombre" name="nombre" hs-entity="Empleado" hs-field="nombre" maxlength="100" required>
                        </div>                        
                    </div>

                    <!-- dirección -->
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
                                <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección"  hs-entity="Empleado" hs-field="direccion" maxlength="255" required>
                        </div>
                    </div> 

                    <!-- Código postal, Localidad y provincia -->                   
                    <div class="form-group row mb-2">
                        <div class="col-12 col-md-2">
                            <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                            <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="C.P." maxlength="5"  hs-entity="Empleado" hs-field="codpostal">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad</label>
                                <input type="text" class="form-control data" id="localidad" name="localidad" placeholder="Localidad" maxlength="100"  hs-entity="Empleado" hs-field="localidad" required>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
                            <select id="provincia" name="provincia" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Empleado" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
                        </div>                        
                    </div> 

                    <!-- Teléfono y e-mail -->
                    <div class="form-group row mb-2">

                        <div class="col-12 col-md-2">
                            <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
                            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono" maxlength="20" hs-entity="Empleado" hs-field="telefono">
                        </div>

                        <div class="col-12 col-md-10">
                            <label for="email"><i class="bi bi-envelope pr-2"></i>Email*</label>                          
                            <input type="email" maxlength="255" class="form-control data text-left form-required" id="email" name="email" placeholder="Email" hs-entity="Empleado" hs-field="email" pattern="[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{1,5}" required>
                        </div>                     

                    </div>                    

                    <!-- Si es una edición y además es sudo, listamos los empleados de la empresa -->
                    <?php if($App->getAction() == 'get' && $App->getUserRol() == 'ROLE_SUDO'): ?>
                    <div class="form-group row mb-2">
                        <div class="col-12">
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>