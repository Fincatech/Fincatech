<div class="row flex-grow-1">
    <div class="col-12" style="overflow-y: scroll;">
        <?php
        if(!is_null($datos)): ?>
        <?php 
            for($xComunidad = 0; $xComunidad < count($datos); $xComunidad++): 
                $name = 'comunidadid_' . $datos[$xComunidad]->id; 
        ?>
            <div class="row">
                <div class="col">
                    <input id="<?php echo $name; ?>" name="<?php echo $name; ?>" type="checkbox" class="form-check-input comunidadId">
                    <label class="form-check-label ml-2" for="<?php echo $name; ?>">
                     <?php echo $datos[$xComunidad]->codigo , ' - ', $datos[$xComunidad]->nombre; ?>
                    </label>                    
                </div>
            </div>
        <?php endfor; ?>
        <?php endif; ?>
    </div>
</div>