<!-- Menú -->
<div class="row">
    <div class="col-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Control documental", "controldocumental/list", "dpd-controldocumental", null, "shield-check"); ?>
                </div>            
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Notas informativas", "notasinformativas/list", "dpd-notasinformativas", null, "info-circle"); ?>
                </div>
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Consultas", "dpd/list", "dpd-consultas", null, "chat-square"); ?>
                </div> 
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Informes de valoración y seguimiento", "informevaloracionseguimiento/list", "dpd-informevaloracionseguimiento", null, "journal-bookmark"); ?>
                </div>                   
                <div class="col-6 col-sm-4 col-xl-2">
                    <?php $App->renderBotonMenu("Requerimientos", "requerimiento/list", "dpd-requerimiento", null, "list-check"); ?>
                </div>                 
            </div>     
        </div>
    </div>

</div>