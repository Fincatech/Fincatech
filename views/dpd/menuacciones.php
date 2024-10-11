<!-- Menú -->
<div class="row">
    <div class="col-12 d-flex">
        <div class="w-100">
            <div class="row"> 
                <div class="col-6 col-xl">
                    <?php $App->renderBotonMenu("Administradores", "administrador/list", "sudo-adminfincas", null, "people"); ?>
                </div>
                <div class="col-6 col-xl">
                    <?php $App->renderBotonMenu("Consultas", "dpd/list", "dpd-consultas", null, "chat-square"); ?>
                </div>                 
                <div class="col-6 col-xl">
                    <?php $App->renderBotonMenu("Notas informativas", "notasinformativas/list", "dpd-notasinformativas", null, "info-circle"); ?>
                </div>
                <div class="col-6 col-xl">
                    <?php $App->renderBotonMenu("Informes de evaluación y seguimiento", "informevaloracionseguimiento/list", "dpd-informevaloracionseguimiento", null, "journal-bookmark"); ?>
                </div>                   
                <div class="col-6 col-xl">
                    <?php $App->renderBotonMenu("Exportar Administradores", null, "dpd-exportar", null, "list-check", 'dpd-exportar'); ?>
                </div>                 
            </div>     
        </div>
    </div>

</div>