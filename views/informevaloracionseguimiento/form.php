<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Informe de valoración y seguimiento</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="off">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                   
                    <!-- Título -->
                    <div class="form-group row mb-3">
                        <div class="col-12 col-md-10 text-left">
                            <label for="titulo" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>Título*</label>
                            <input type="text" class="form-control data text-left form-required" id="nombre" name="nombre" placeholder="Título del informe" hs-entity="Informevaloracionseguimiento" hs-field="titulo" required>
                            <input type="hidden" class="form-control data text-left d-none" id="estado" name="estado" hs-entity="Informevaloracionseguimiento" hs-field="estado" value="A">
                        </div>
                    </div>

                    <!-- Administrador de fincas -->
                    <div class="form-group row mb-3">

                        <div class="col-12">            
                            <label for="usuarioId"><i class="bi bi-person-fill pr-2"></i>Administrador de fincas</label>
                            <select id="usuarioId" name="usuarioId" class="select-data custom-select data form-control selectpicker form-required" data-live-search="true" hs-entity="Informevaloracionseguimiento" hs-field="usuarioId" hs-list-entity="Administrador" hs-list-field="Usuario.nombre" hs-list-value="Usuario.id"></select>
                        </div>  

                    </div>

                    <div class="form-group row mt-3">
                        <div class="col-12 text-left">
                            <label class="mb-1 text-left text-uppercase font-weight-normal pr-3 pt-2" style="font-size:14px;"><i class="bi bi-paperclip"></i> Adjuntar fichero</label>
                        </div>
                    </div>

                    <!-- Fichero adjunto al informe -->
                    <div class="form-group row mb-2">
                        <div class="col-12">  
                            <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-3 shadow-inset border-1 pt-3 pb-2">
                                <div class="col-1 align-self-center h-100 text-center">
                                    <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
                                </div>
                                <div class="col-11 pl-0 align-self-center">
                                    <input accept=".pdf, .docx, .doc" class="form-control form-control-sm ficheroadjuntar border-0" hs-fichero-entity="Informevaloracionseguimiento" id="ficheroadjuntar" type="file">
                                </div>       
                            </div>
                        </div>
                    </div>

                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>