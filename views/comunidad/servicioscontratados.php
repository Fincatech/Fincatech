<?php    //  Alta / Modificación de servicios sólo si es SUDO 

if($App->isSudo()):?>

<div class="table-responsive form-servicioscontratados">
    <table class="table">
        <thead>
            <tr>
                <th valign="middle" class="bg-light font-weight-normal text-uppercase">Nombre y estado de contratación del servicio</th>
                <th valign="middle" width="150px" class="text-center bg-light font-weight-normal text-uppercase">Precio Coste<br>(Sin IVA)</th>
                <th valign="middle" width="150px" class="text-center bg-light font-weight-normal text-uppercase">Precio<br>Comunidad</th>
                <th valign="middle" width="200px" class="text-center bg-light font-weight-normal text-uppercase">Mes<br>facturación</th>
                <th valign="middle" class="text-right bg-light font-weight-normal text-uppercase">Retorno</th>
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