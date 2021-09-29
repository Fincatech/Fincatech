<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Requerimiento</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="off">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                   
                    <!-- Nombre -->
                    <div class="form-group row mb-3">
                        <div class="col-12 col-md-10 text-left">
                            <label for="titulo" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>Nombre*</label>
                            <input type="text" class="form-control data text-left form-required" id="nombre" name="nombre" placeholder="Nombre del requerimiento" hs-entity="Requerimiento" hs-field="nombre" required>
                        </div>

                        <!-- Tipo -->
                        <div class="col-12 col-md-2">
                            <label for="email"><i class="bi bi-key pr-2"></i>Tipo</label>              
                            <select id="idrequerimientotipo" name="idrequerimientotipo" class="select-data data custom-select form-control selectpicker" data-live-search="true" hs-entity="Requerimiento" hs-field="idrequerimientotipo" hs-list-entity="Requerimientotipo" hs-list-field="nombre" hs-list-value="id"></select>
                        </div>  

                    </div>

                    <!-- Comunidad asociada -->
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="email"><i class="bi bi-building pr-2"></i>Comunidad asociada<small><i class="bi bi-info-circle ml-3 pl-1"></i> </span> Elija una opción solo si desea que el requerimiento sea asociado únicamente a una comunidad</label>              
                            <select id="idcomunidad" name="idcomunidad" class="select-data data custom-select form-control selectpicker" data-placeholder="Selecciona una opcion" data-allow-clear="true" data-live-search="true" hs-entity="Requerimiento" hs-field="idcomunidad" hs-list-entity="Comunidad" hs-list-field="nombre" hs-list-value="id">
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <div class="col-12">
                            <label for="titulo" class="pl-0"><i class="bi bi-gear-wide-connected pr-2"></i></i>OPCIONES</label>
                        </div>
                    </div>

                    <!-- Sujeto a revisión por parte del revisor documental -->
                    <div class="form-group row">

                        <!-- Sujeto a revisión por parte del revisor documental -->
                        <div class="col-12 col-md-4">

                            <label class="form-check m-0">
                                <input name="sujetorevision" id="sujetorevision" type="checkbox" class="form-check-input data" hs-entity="Requerimiento" hs-field="sujetorevision" value="0">
                                <span class="form-check-label">Sujeto a revisión por parte del gestor documental</span>
                            </label>

                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-12 col-md-3">

                            <label class="form-check m-0">
                                <input name="caduca" id="caduca" type="checkbox" class="form-check-input data" hs-entity="Requerimiento" hs-field="caduca" value="0">
                                <span class="form-check-label">Requerimiento con fecha de caducidad</span>
                            </label>

                        </div>

                    </div>

                    <!-- Requiere descargar previamente el documento -->
                    <div class="form-group row">

                        <div class="col-12 col-md-4">

                            <label class="form-check m-0">
                                <input name="requieredescarga" id="requieredescarga" value="0" type="checkbox" class="form-check-input data" hs-entity="Requerimiento" hs-field="requieredescarga">
                                <span class="form-check-label">Requiere descargar previamente el documento</span>
                            </label>

                        </div>

                    </div>

                    <div class="form-group row mt-3">
                        <div class="col-12 text-left">
                            <label class="mb-1 text-left text-uppercase font-weight-normal pr-3 pt-2" style="font-size:14px;"><i class="bi bi-paperclip"></i> Adjuntar fichero</label>
                        </div>
                    </div>
                    <!-- Fichero adjunto a la nota -->
                    <div class="form-group row mb-2">
                        <div class="col-12">  
                            <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-3 shadow-inset border-1 pt-3 pb-2">
                                <div class="col-1 align-self-center h-100 text-center">
                                    <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
                                </div>
                                <div class="col-11 pl-0 align-self-center">
                                    <input accept=".pdf, .docx, .doc" class="form-control form-control-sm ficheroadjuntar border-0" hs-fichero-entity="Notasinformativas" id="ficheroadjuntar" type="file">
                                </div>       
                            </div>
                        </div>
                    </div>



                    <?php $App->renderActionButtons(); ?>

                </form>

            </div>

    </div>

</div>