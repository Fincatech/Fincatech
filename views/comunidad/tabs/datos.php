
<form class="form-data form-floating form-comunidad">

<!-- Código - CIF - Nombre -->
<div class="form-group row mb-2">

    <!-- Código de comunidad -->
    <div class="col-12 col-md-2">
        <label for="codigo" class="pl-0"><i class="bi bi-key-fill pr-2"></i>Código*</label>
            <input type="text" class="form-control data text-center form-required" id="codigo" name="codigo" placeholder="Código interno" hs-entity="Comunidad" hs-field="codigo" required>
    </div>

    <!-- CIF -->
    <div class="col-12 col-md-2 text-left">
        <label for="cif" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>CIF*</label>
            <input type="text" class="form-control data text-center form-required" id="cif" name="cif" placeholder="CIF/NIF" hs-entity="Comunidad" hs-field="cif" required>
    </div>  

    <!-- Administrador asignado -->
    <!-- Sólo pueden los sudo -->
    <?php if( $App->isSudo() ): ?>
    <div class="col-12 col-md-8">
        <label for="usuarioId"><i class="bi bi-person-fill pr-2"></i>Administrador asignado</label>
        <select id="usuarioId" name="usuarioId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-entity="Comunidad" hs-field="usuarioId" hs-list-entity="Administrador" hs-list-field="Usuario.nombre" hs-list-value="Usuario.id"></select>
    </div>                 
    <?php endif; ?>

</div>

<!-- Nombre de la comunidad - Nombre del presidente -->
<div class="form-group row mb-2">

    <!-- Nombre de la comunidad -->
    <div class="col-12 col-md-9 text-left">
        <label for="nombre"><i class="bi bi-building pr-2"></i>Nombre*</label>
        <input type="text" class="form-control data form-required" id="nombre" name="nombre" placeholder="Nombre de la comunidad"  hs-entity="Comunidad" hs-field="nombre" aria-label="nombre" aria-describedby="addon-nombre" required>
    </div>                      

    <!-- Nombre del presidente -->
    <div class="col-12 col-md-3 text-left">
        <label for="presidente"><i class="bi bi-person pr-2"></i>Nombre del presidente</label>
        <input type="text" class="form-control data" id="presidente" name="presidente" placeholder="Nombre del presidente"  hs-entity="Comunidad" hs-field="presidente" aria-label="nombre" aria-describedby="addon-nombre">
    </div>  

</div>

<!-- Spa -->
<div class="form-group row mb-2">
    <div class="col-12">
        <label for="nombre"><i class="bi bi-shield-check pr-2"></i>SPA Asignado*</label>
        <select id="idspa" name="idspa" class="select-data data custom-select form-control selectpicker form-required" data-live-search="true" hs-entity="Comunidad" hs-field="idspa" hs-list-entity="Spa" hs-list-field="nombre" hs-list-value="id"></select>
    </div>
</div> 

<!-- dirección -->
<div class="form-group row mb-2">
    <div class="col-12">
        <label for="direccion"><i class="bi bi-map pr-2"></i>Dirección</label>                        
        <input type="text" class="form-control data" id="direccion" name="direccion" placeholder="Dirección"  hs-entity="Comunidad" hs-field="direccion" required>
    </div>
</div> 

<!-- Código postal, Localidad y provincia -->                   
<div class="form-group row mb-2">
    <div class="col-12 col-md-2">
        <label for="codpostal"><i class="bi bi-mailbox pr-2"></i>Código postal</label>
                <input type="text" class="form-control data text-center" id="codpostal" name="codpostal" placeholder="Código postal"  hs-entity="Comunidad" hs-field="codpostal">
    </div>
    <div class="col-12 col-md-7">
        <label for="localidad"><i class="bi bi-geo-alt pr-2"></i>Localidad*</label>
            <input type="text" class="form-control data form-required" id="localidad" name="localidad" placeholder="Localidad"  hs-entity="Comunidad" hs-field="localidad" required>
    </div>
    <div class="col-12 col-md-3">
        <label for="provincia"><i class="bi bi-geo-alt pr-2"></i>Provincia</label>
        <select id="provinciaid" name="provinciaid" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-entity="Comunidad" hs-field="provinciaid" hs-list-entity="Provincia" hs-list-field="Nombre" hs-list-value="Id"></select>
    </div>                        
</div> 

<!-- Teléfono y e-mail -->
<div class="form-group row mb-2">
    <div class="col-12 col-md-2">
        <label for="telefono"><i class="bi bi-phone pr-2"></i>Teléfono</label>                          
            <input type="text" class="form-control data text-center" id="telefono" name="telefono" placeholder="Teléfono"  hs-entity="Comunidad" hs-field="telefono">
    </div>
    <div class="col-12 col-md-10">
        <label for="email"><i class="bi bi-envelope pr-2"></i>E-Mail*</label>              
        <input type="text" class="form-control data text-left form-required" id="email" name="email" placeholder="E-mail"  hs-entity="Comunidad" hs-field="emailcontacto" required>
    </div>                       

</div>                    

<!-- IBAN -->
<div class="form-group row mb-2">
    <div class="col-12">
        <label for="ibancomunidad"><i class="bi bi-bank pr-2"></i>IBAN</label>              
        <input type="text" class="form-control data text-center text-uppercase" id="ibancomunidad" name="ibancomunidad" placeholder="Código Cuenta IBAN"  hs-entity="Comunidad" hs-field="ibancomunidad" maxlength="30">
    </div>                       

</div> 

<!-- Servicios contratados -->
<?php if($App->isSudo()): ?>
<div class="form-group row mb-2">

    <div class="col-12">

        <div class="card border rounded-0 mt-3">

            <div class="card-header p-0">
                <div class="alert alert-success m-0 justify-content-center rounded" role="alert">
                    <p class="m-0 p-3 text-uppercase">Servicios contratados</p>
                </div>
            </div>

            <div class="card-body">
                <?php include_once(VIEWS_DIR . 'comunidad/servicioscontratados.php') ; ?>
            </div>

        </div>

    </div>

</div>
<?php endif; ?>
<?php $App->renderActionButtons(); ?>

</form>