<!-- Menú -->
<div class="row">
    <div class="col-12 d-flex">
        <div class="w-100">
            <div class="row">
                <!-- Emisión de facturación -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Generar Facturación", "facturacion/emision", "sudo-facturacion", null, "file-earmark-spreadsheet"); ?>
                </div>
                <!-- Facturas -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Facturas", "facturacion", "sudo-facturas", null, "receipt"); ?>
                </div>
                <!-- Facturas Rectificativas -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Facturas Rectificativas", "rectificativas", "sudo-rectificativas", null, "receipt-cutoff"); ?>
                </div>                
                <!-- Remesas -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Remesas", "remesa/list", "sudo-remesas", null, "files"); ?>
                </div>
                <!-- Liquidaciones -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Liquidaciones", "liquidaciones/list", "sudo-liquidaciones", null, "cash-stack"); ?>
                </div>   
                <!-- Ingresos a cuenta -->
                <!-- <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php //$App->renderBotonMenu("Entregas a cuenta", "ingresoscuenta/list", "sudo-ingresoscuenta", null, "cash-coin"); ?>
                </div>                                 -->
                <!-- Bancos -->                
                <div class="col-6 col-sm-4 col-lg-3  col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Bancos", "bank", "sudo-bank", null, "bank"); ?>
                </div>         
                <!-- Configuración -->       
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2 col-xxl">
                    <?php $App->renderBotonMenu("Configuración", "configuracion", "sudo-configuracion", null, "gear"); ?>
                </div>
            </div>     
        </div>
    </div>

</div>