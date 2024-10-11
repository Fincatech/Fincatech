<!-- Datos de la factura -->
<div class="row mb-3 mx-0">
    <div class="col-12 text-left border br-6 p-3 bg-light">
        <p class="mb-0">Se va a proceder a realizar una factura rectificativa sobre la Factura <span class="font-weight-bold"><?php echo $datos->numero; ?></span> por un importe total  de <span class="font-weight-bold"><?php echo number_format($datos->total, 2, ',','.'); ?>€</span> <small>(impuestos excluidos)</small>.</p>
        <p class="">Si lo desea puede especificar otra cantidad en el campo "Importe". Recuerde que la cantidad debe ser sin impuestos.</p>
        <p class="mb-0"><span class="font-weight-bold">Factura</span>: <?php echo $datos->numero; ?></p>
        <p class="mb-0"><span class="font-weight-bold">Administrador</span>: <?php echo $datos->administrador; ?> - E-mail: <?php echo $datos->email; ?></p>
        <p class="mb-0"><span class="font-weight-bold">Comunidad</span>: <?php echo $datos->comunidad; ?></p>
    </div>
</div>

<!-- Captura de datos -->
<!-- Concepto -->
<div class="row mb-2">
    <div class="col-12 col-lg-9">
        <label class="form-label font-weight-bold text-left d-block">Concepto <small>(opcional)</small></label>
        <input type="text" class="form-control" id="conceptoFacturaRectificativa" name="conceptoFacturaRectificativa" placeholder="Escriba el concepto que aparecerá en el detalle de la factura. (opcional)" value="Fra. Rectificativa de Fra. <?php echo $datos->numero; ?>">
    </div>
    <!-- Importe -->
    <div class="col-12 col-lg-3">
        <label class="form-label font-weight-bold text-left d-block">Importe (Imp. Excluidos) <small>(opcional)</small></label>
        <input type="number" max="<?php echo $datos->total; ?>" class="form-control text-center" id="importeFacturaRectificativa" name="importeFacturaRectificativa" value="<?php echo number_format($datos->total, 2, '.',','); ?>">
    </div>
</div>

<!-- Cuerpo del E-mail -->
 <div class="row mb-2">
    <div class="col-12">
    <label class="form-label font-weight-bold text-left d-block">Cuerpo del e-mail <small>(opcional)</small> <small class="font-weight-normal text-secondary d-block mt-2"><i class="bi bi-info-circle"></i> Deje este campo en blanco si desea utilizar el texto por defecto del sistema</small></label>
        <div class="emailbody text-left"></div>
    </div>
 </div>

<!-- Botones de acción -->
<div class="row w-50 mx-auto mt-4 pb-2">
    <div class="col-6">
        <a href="javascript:swal.closeModal();" title="Cancelar" class="btn btn-danger shadow h-100 justify-content-center d-flex align-items-center">Cancelar</a>
    </div>
    <div class="col-6">
        <a href="javascript:void(0);" title="Generar" data-id="<?php echo $datos->id; ?>" class="btn btn-success shadow h-100 justify-content-center d-flex align-items-center btnModalCrearFacturaRectificativa">Generar Factura Rectificativa</a>
    </div>
</div>
<script type="text/javascript">
    $('.emailbody').trumbowyg();
</script>

