<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill card-principal">

            <div class="card-header pl-0 headerListado">

                <div class="row">

                    <div class="col-12 col-md-6">
                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><i class="bi bi-list text-secondary pr-2"></i> Administradores</h5>
                    </div>
<?php if(!$App->isDPD()) : ?>
                    <div class="col-12 col-md-6 text-right">
                        <a href="/administrador/add" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> Añadir Administrador</a>
                    </div>
<?php endif; ?>        
                </div>

            </div>
            
            <div class="listado pl-3 pr-3 pb-3">

                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover my-0 hs-tabla no-footer no-clicable" data-order='[[ 1, "asc"]]' name="listadoAdministrador" id="listadoAdministrador" data-model="Administrador">
                                <thead class="thead"></thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>

            </div>

        </div>

    </div>

</div>