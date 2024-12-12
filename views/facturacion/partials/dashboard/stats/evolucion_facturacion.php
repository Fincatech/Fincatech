<div class="card shadow-neumorphic border" style="border-radius:20px;">

<div class="card-body">

    <div class="row stats-facturacion-evolucion">

        <div class="col-12 mt-0">
            <h5 class="card-title font-weight-bold border-bottom pb-2">Evolución Facturación <?php echo date('Y'); ?></h5>
            <div>
                <canvas id="chartEvolucionFacturacion" class="chart" data-date="<?php echo date('Y-m-d'); ?>" data-type="bar" data-entity="facturacion/evolucion"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
