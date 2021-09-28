<!-- Menú -->
<div class="row">
    <div class="col-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Administradores", "administrador/list", "sudo-adminfincas", null, "people"); ?>
                </div>
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Comunidades", "comunidad/list", "sudo-comunidades", null, "building"); ?>
                </div>
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Usuarios", "usuario/list", "sudo-usuario", null, "person-lines-fill"); ?>
                </div>
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Empresas", "empresa/list", "sudo-empresa", null, "shop"); ?>
                </div>   
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Empleados", "empleado/list", "sudo-empleado", null, "people"); ?>
                </div>                  
                <div class="col-12 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Facturación", "facturacion/list", "sudo-facturacion", null, "bank"); ?>
                </div>      
                <div class="col-12 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Spa", "spa/list", "sudo-spa", null, "shield-check"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Requerimientos", "requerimiento/list", "dpd-requerimiento", null, "list-check"); ?>
                </div>                                   
            </div>     
        </div>
    </div>

</div>