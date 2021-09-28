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
        <tbody>
            <tr>
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="cae">
                        <label class="form-check-label" for="cae">CAE</label>
                    </div>
                </td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0 text-right"><label class="retorno">0,00€</label></td>
            </tr>
            <tr>
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rgpd">
                        <label class="form-check-label" for="rgpd">RGPD</label>
                    </div>
                </td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0 text-right"><label class="retorno">0,00€</label></td>
            </tr>
            <tr>
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="prl">
                        <label class="form-check-label" for="prl">PRL</label>
                    </div>
                </td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0 text-right"><label class="retorno">0,00€</label></td>
            </tr>
            <tr>
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="instalaciones">
                        <label class="form-check-label" for="instalaciones">Instalaciones</label>
                    </div>
                </td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0 text-right"><label class="retorno">0,00€</label></td>
            </tr>
            <tr>
                <td class="mb-0 pb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="certificadosdigitales">
                        <label class="form-check-label" for="certificadosdigitales">Certificados digitales</label>
                    </div>
                </td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0"><input type="number" class="form-control text-center"></td>
                <td class="mb-0 pb-0 text-right"><label class="retorno">0,00€</label></td>
            </tr>
        </tbody>
    </table>
</div>
<?php   endif; ?>