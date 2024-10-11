<!-- Datos de la factura -->
<?php
    $havefacturaRectificativa = false;
    $sFacturaRectificativa = 'No';
    //  Comprobamos si tiene factura rectificativa asociada
    if($datos->idrectificativa !== '' && $datos->idrectificativa !== 'undefined' && $datos->idrectificativa !== 'null' && !is_null($datos->idrectificativa))
    {
        $havefacturaRectificativa = true;
        $sFacturaRectificativa = $datos->numerorectificativa;
    }
?>
<form id="formModalEnvioFactura" name="formModalEnvioFactura">

    <!-- Información de la factura que se va a enviar -->
    <div class="row mb-3 mx-0">
        <div class="col-12 text-left border br-6 p-3 bg-light">
            <p>Se va a proceder a enviar por e-mail la Factura <span class="font-weight-bold"><?php echo $datos->numero; ?></span> por un importe total  de <span class="font-weight-bold"><?php echo number_format($datos->total, 2, ',','.'); ?>€</span> <small>(impuestos excluidos)</small>.</p>
            <p class="mb-0"><span class="font-weight-bold">Factura</span>: <?php echo $datos->numero; ?> - <?php echo $datos->fecha; ?></p>
            <?php if($havefacturaRectificativa): ?>
                <p class="mb-0"><span class="font-weight-bold">Factura Rectificactiva asociada</span>: <?php echo $sFacturaRectificativa; ?></p>
            <?php endif; ?>
            <p class="mb-0"><span class="font-weight-bold">Administrador</span>: <?php echo $datos->administrador; ?> - E-mail: <?php echo $datos->email; ?></p>
            <p class="mb-0"><span class="font-weight-bold">Comunidad</span>: <?php echo $datos->comunidad; ?></p>
        </div>
    </div>

    <!-- Email y Asunto -->
    <div class="row mb-2">
        <!-- E-mail -->
        <div class="col-12">
            <label class="form-label font-weight-bold text-left d-block" for="emailEnvioFactura">Email destinatario</label>
            <input type="text" class="form-control" id="emailEnvioFactura" name="emailEnvioFactura" placeholder="E-mail del destinatario" value="<?php echo $datos->email; ?>">
            <label id="emailEnvioFactura-msg-error" data-input-field-id="emailEnvioFactura" class="msg-form-error d-none"></label>
            <label class="form-label info pl-2 text-left d-block mt-2"><i class="bi bi-info-circle"></i> Escriba las direcciones de e-mail separadas por ; si desea enviar el e-mail a varios destinatarios. Por defecto siempre se envía una copia al Master.</label>
        </div>    
    </div>

    <!-- Asunto -->
    <div class="row mb-2">
        <div class="col-12">
            <label class="form-label font-weight-bold text-left d-block">Asunto <small>(opcional)</small></label>
            <input type="text" class="form-control" id="asuntoEnvioFactura" name="asuntoEnvioFactura" placeholder="Escriba el asunto del e-mail (opcional)" value="Fincatech - Fra. <?php echo $datos->numero; ?>">
        </div>
    </div>

    <!-- Cuerpo del E-mail -->
    <div class="row mb-2">
        <div class="col-12">
            <label class="form-label font-weight-bold text-left d-block">Cuerpo del e-mail <small>(opcional)</small> <small class="font-weight-normal text-secondary d-block mt-2"><i class="bi bi-info-circle"></i> Deje este campo en blanco si desea utilizar el texto por defecto del sistema</small></label>
            <div class="emailbody text-left"></div>
            <label id="resultado-msg-error" data-input-field-id="resultado" class="msg-form-error d-none"></label>
        </div>
    </div>

 </form>

<!-- Botones de acción -->
<div class="row w-50 mx-auto mt-4 pb-2">
    <div class="col-6">
        <a href="javascript:swal.closeModal();" title="Cancelar" class="btn btn-danger shadow h-100 justify-content-center d-flex align-items-center">Cancelar</a>
    </div>
    <div class="col-6">
        <a href="javascript:void(0);" title="Enviar Factura por e-mail" data-id="<?php echo $datos->id; ?>" class="btn btn-success shadow h-100 justify-content-center d-flex align-items-center btnModalEnviarFactura">Enviar Factura</a>
    </div>
</div>