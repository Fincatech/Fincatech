<?php    //  Alta / Modificación de servicios sólo si es SUDO 
if($App->isSudo()):?>

<div class="table-responsive form-servicioscontratados">
    <table class="table">
        <thead>
            <tr>
                <th class="bg-light font-weight-normal text-uppercase">Nombre y estado del servicio</th>
                <th class="text-center bg-light font-weight-normal text-uppercase">PVP (Sin IVA)</th>
                <th class="text-center bg-light font-weight-normal text-uppercase">Precio Comunidad</th>
                <th class="text-center bg-light font-weight-normal text-uppercase">Retorno</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?php   endif; ?>