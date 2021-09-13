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
                    <?php $App->renderBotonMenu("Contratos", "contrato/list", "sudo-contrato", null, "card-checklist"); ?>
                </div>
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("CTs", "contrato/list", "sudo-contrato", null, "file-text"); ?>
                </div>									
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Usuarios", "usuario/list", "sudo-usuario", null, "person-lines-fill"); ?>
                </div>
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Empresas / Empleados", "empresa/list", "sudo-empresa", null, "diagram-2-fill"); ?>
                </div>   
                <div class="col-12 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Facturación", "facturacion/list", "sudo-facturacion", null, "bank"); ?>
                </div>      
            </div>     
        </div>
    </div>

</div>