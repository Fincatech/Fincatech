<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">

            <div class="card-header pl-1 mb-2">

                <h5 class="card-title mb-0"><i data-feather="<?php echo $iconoAccion; ?>"></i> <span class="titulo titulo-modulo">Notas informativas</span></h5>

            </div>
    
            <div class="card-body shadow-inset rounded-lg border mb-1 border-white">

                <form class="form-data form-floating" autocomplete="off">
                    <input autocomplete="new-password" name="hidden" type="text" style="display:none;">
                   
                    <!-- Título -->
                    <div class="form-group row mb-3">
                        <div class="col-12 text-left">
                            <label for="titulo" class="pl-0"><i class="bi bi-credit-card-2-front pr-2"></i>Título*</label>
                            <input type="text" class="form-control data text-left form-required" id="titulo" name="titulo" placeholder="Titulo de la nota informativa" hs-entity="Notasinformativas" hs-field="titulo" required>
                        </div>
                    </div>
                    <!-- Contenido de la nota -->
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="descripcion"><i class="bi bi-person pr-2"></i>Contenido de la nota</label>
                            <textarea rows="10" class="form-control data shadow-inset border-0 rounded form-required" id="descripcion" name="descripcion" hs-entity="Notasinformativas" hs-field="descripcion" aria-label="descripcion" aria-describedby="addon-descripcion" required></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 text-left">
                            <p class="mb-1 text-left text-uppercase font-weight-normal pr-3 pt-2" style="font-size:14px;">Adjuntar fichero</p>
                        </div>
                    </div>
                    <!-- Fichero adjunto a la nota -->
                    <div class="form-group row mb-2">
                        <div class="col-12">  
                            <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-3 shadow-inset border-1 pt-3 pb-2">
                                <div class="col-1 align-self-center h-100 text-center">
                                    <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
                                </div>
                                <div class="col-11 pl-0">
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