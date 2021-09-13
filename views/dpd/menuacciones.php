<!-- Menú -->
<div class="row">
    <div class="col-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Administradores", "sudo-adminfincas", null, "people"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Comunidades", "sudo-comunidades", null, "building"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Notas informativas", "sudo-contratos", null, "file-earmark-medical"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Conjunto documental básico", "sudo-contratos", null, "file-earmark-check"); ?>
                </div>									
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Consultas RGPD", "sudo-contratos", null, "chat-left"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl">
                    <?php $App->renderBotonMenu("Informes valoración y seguimiento", "sudo-documentos", null, "file-post"); ?>
                </div>   
            </div>     
        </div>
    </div>

</div>