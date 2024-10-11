<div class="card shadow-neumorphic" style="border-radius:20px;">

<div class="card-body">

    <div class="row stats-facturacion-mes">

        <div class="col-12 mt-0">

            <h5 class="card-title font-weight-bold border-bottom pb-2">Facturación <span class="month"><?php echo $App->CurrentMonth();?></span> <span class="year"><?php echo date('Y');?></span></h5>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40%;">Servicio</th>
                        <th class="text-right" style="width:20%">Previsión</th>
                        <th class="text-right" style="width:20%">Facturas emitidas</th>
                        <th class="text-right" style="width:20%">Facturas devueltas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CAE</td>
                        <td class="text-right"><span class="total_cae">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                    </tr>
                    <tr>
                        <td>DPD</td>
                        <td class="text-right"><span class="total_dpd">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                    </tr>
                    <tr>
                        <td>Certificados digitales</td>
                        <td class="text-right"><span class="total_certificados">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                    </tr>
                    <tr>
                        <td class="text-right"><span class="font-weight-bold">Total</span></td>
                        <td class="text-right"><span class="total_mes">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                        <td class="text-right"><span class="">0</span>€</td>
                    </tr>

                </tbody>
            </table>                                                

        </div>

    </div>

</div>

</div>