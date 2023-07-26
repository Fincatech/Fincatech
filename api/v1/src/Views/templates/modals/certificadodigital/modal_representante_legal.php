<div class="row">
    <div class="col-12">
        <p class="text-left">Para emitir el certificado sobre la comunidad <strong><?php echo $datos->codigoComunidad, ' ' , $datos->nombreComunidad; ?></strong> necesitamos que estableza el representante legal.</p>
        <!-- Seleccione el representante legal de la comunidad y que será utilizado en la solicitud del certificado digital</p> -->
    </div>
</div>

<!-- Representantes legales -->
<div class="row">
    <div class="col-12 text-left form-data">
        <label for="representanteLegal" class="text-left d-block mb-2"><i class="bi bi-person pr-2"></i>Representante legal*</label>                        
        <select id="representanteLegal" name="representanteLegal" class="custom-select form-control selectpicker form-required" data-live-search="true">
            <?php for($x = 0; $x < count($datos->representanteslegales); $x++): 
                $seleccionado = $x == 0 ? 'selected' : '';
                ?>
                <option value="<?php echo $datos->representanteslegales[$x]->id;?>" <?php echo $seleccionado; ?>><?php echo $datos->representanteslegales[$x]->documento, ' - ' , $datos->representanteslegales[$x]->nombre, ' ' , $datos->representanteslegales[$x]->apellido, ' ' , $datos->representanteslegales[$x]->apellido2;?></option>
            <?php endfor; ?>
        </select>
        <p class="text-left text-danger mt-2">IMPORTANTE: Asegúrese de seleccionar el representante legal correcto ya que esta acción no se puede deshacer.</p>
    </div>
</div>

<!-- Botón de seleccionar -->
<div class="row mt-3">
    <div class="col-12 text-center">
        <a href="javascript:void(0);" class="btn btn-outline-success text-uppercase rounded-pill shadow pl-2 pr-2 btnConfirmarRepresentanteLegal">Seleccionar y solicitar certificado</a>
    </div>
</div>