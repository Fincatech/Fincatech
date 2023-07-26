<?php if($App->isContratista() ): ?>
<div class="row mensajeRGPD" style="display: none;">
    <div class="col-12 ">
        <div class="row alert bg-info text-white text-justify pl-3 pr-3 pb-3 pt-3">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <p class="mb-0">Conforme al art. 7 del RGPD si marco Sí , consiento en que mis datos personales sean tratados por FINCATECH SOFTWARE SL, para sus fines propios, control normativo de las obligaciones documentales para dar cumplimiento a la legalidad vigente, y están sujetos a confidencialidad. Para este asunto FINCATECH SOFTWARE SL ha nombrado DPD a Salvador Zotano Sánchez a su disposición en la dirección: dpd@fincatech.es. Usted puede ejercer sus derechos de acceso, cancelación, rectificación, oposición y portabilidad en la dirección: info@fincatech.es</p>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col text-center">
                        <a href="javascript:void(0);" data-id="<?php echo $App->getUserId(); ?>" class="btn btn-success btnAceptarRGPD">Aceptar</a>
                        <a href="javascript:void(0);" data-id="<?php echo $App->getUserId(); ?>" class="btn btn-danger btnRechazarRGPD">Rechazar</a>
                        <a href="https://fincatech.es/confidencialidad-y-proteccion-de-datos/" target="_blank" class="btn btn-light btnRechazarRGPD">Más información</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>