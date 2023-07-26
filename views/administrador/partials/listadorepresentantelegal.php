<div class="tab-pane fade h-100" id="representantelegal" role="tabpanel">

    <div class="row">
        <div class="col-12 text-right mt-3">
            <a href="<?php echo HOME_URL .  "representantelegal/add?idadmin=" . $App->getId(); ?>" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> AÑADIR REPRESENTANTE LEGAL</a>
        </div>
    </div>

    <div class="form-group row mb-2">

        <div class="col-12">

            <div class="card border rounded-0 mt-3">

                <div class="card-header p-0">
                    <div class="alert alert-success m-0 justify-content-center rounded" role="alert">
                        <p class="m-0 p-3 text-uppercase">Representantes legales</p>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover my-0 hs-tabla w-100" name="listadoAdministradorRepresentantesLegales" id="listadoAdministradorRepresentantesLegales" data-model="representantelegal">
                        <thead class="thead"></thead>
                        <tbody class="tbody"></tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>
</div>