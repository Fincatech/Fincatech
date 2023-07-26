<div id="appContainer" class="row flex-grow-1">
    <div class="col-12 d-flex">
        <div class="card flex-fill shadow-neumorphic">
            <div class="card-header">
                <div class="row">
                    <div class="col-12 col-sm-9">
                        <h5 class="card-title mb-0">Certificados digitales</h5>
                    </div>
                    <div class="col-12 col-sm-3 text-right">
                        
                    </div>
                </div>
            </div>
            <div class="card-body space-between pt-0">
                <!-- Listado de comunidades con certificado digital contratado -->
                <?php $App->renderView('certificadodigital/partials/listado.php'); ?>
                <!-- Datos del emisor -->
                <?php //$App->renderView('certificadodigital/partials/datosemisor.php'); ?>
            </div>
        </div>
    </div>
</div>
