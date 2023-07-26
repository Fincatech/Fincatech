<?php    //  Alta / Modificación de servicios sólo si es SUDO 

if($App->isSudo()):?>

<div class="table-responsive form-servicioscontratados">
    <table class="table">
        <thead>
            <tr>
                <th class="bg-light font-weight-normal text-uppercase">Nombre y estado de contratación del servicio</th>
                <th width="150px" class="text-center bg-light font-weight-normal text-uppercase">PVP (Sin IVA)</th>
                <th width="150px" class="text-center bg-light font-weight-normal text-uppercase">Precio Comunidad</th>
                <th width="200px" class="text-center bg-light font-weight-normal text-uppercase">Mes de facturación</th>
                <th class="text-right bg-light font-weight-normal text-uppercase">Retorno</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?php else: ?>
    <div class="table-responsive form-servicioscontratados-info">
    <table class="table">
        <thead>
            <tr>
                <th class="bg-light font-weight-normal text-uppercase">Servicio</th>
                <th class="text-center bg-light font-weight-normal text-uppercase">Contratado</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?php endif; ?>