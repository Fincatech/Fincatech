<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill card-principal">

            <div class="card-header pl-0 headerListado">

                <div class="row">

                    <div class="col-12 col-md-6">
                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1">Remesas</h5>
                    </div>
                    <div class="col-12 col-md-6 text-right">
                        <a href="create" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4 mr-2"><i class="bi bi-plus-circle pr-3"></i> Crear Remesa manual</a>
                        <a href="devolucion" class="btn btn-outline-primary text-uppercase rounded-pill shadow pl-2 pr-4 mr-2"><i class="bi bi-check2-square pr-3"></i> Devolución manual</a>
                        <a href="javascript:void(0);" class="btn btnProcesarRemesaDevolucion btn-outline-danger text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-arrow-repeat pr-3"></i> Procesar devolución remesa</a>
                    </div>
        
                </div>

            </div>
            
            <div class="listado pl-3 pr-3 pb-3">

                <table class="table table-bordered table-hover my-0 hs-tabla" name="listadoRemesas" id="listadoRemesas" data-model="Remesas">
                    <thead class="thead"></thead>
                    <tbody class="tbody"></tbody>
                </table>

            </div>

        </div>

    </div>

</div>