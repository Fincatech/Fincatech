<div class="row flex-grow-1">

    <div class="col-12 d-flex position-relative">

        <div class="card flex-fill pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header">

                <div class="row">
                    <div class="col-12 col-lg-3">
                        <h5 class="card-title mb-0"><span class="titulo titulo-modulo pl-0"><i class="bi bi-receipt pr-2"></i> Remesa</span></h5>
                    </div>
                    <div class="col-12 col-lg-9 text-right">
                        <a href="javascript:history.back(-1);" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-arrow-left"></i> Volver</a>
                    </div>
                </div>

            </div>
    
            <div class="card-body rounded-lg border mb-1 border-white pt-0">

                <div class="row h-100">

                    <div class="col-12 col-lg-4">

                        <form class="form-data form-floating form-facturacion h-100" autocomplete="off">

                            <div class="row h-100">

                                <!-- Fecha de generación de remesa -->
                                <div class="col-12 shadow-neumorphic h-100 br-10 p-3 border">

                                    <p class="card-title">Información de la remesa</p>

                                    <div class="row mt-3">
                                        <!-- Fecha -->
                                         <div class="col-12 col-lg">
                                            <label class="d-block font-weight-bold">Referencia</label>
                                            <label class="d-block data form-data mb-3" hs-entity="Remesa" hs-field="referencia"></label>
                                         </div>

                                    </div>

                                    <div class="row">
                                        <!-- Fecha -->
                                         <div class="col-12 col-lg">
                                            <label class="d-block font-weight-bold">Fecha</label>
                                            <label class="d-block data form-data mb-3" hs-entity="Remesa" hs-field="created"></label>
                                         </div>
                                        <!-- Nº de recibos presentados -->
                                        <div class="col-12 col-lg">
                                            <label class="d-block font-weight-bold text-lg-center">Nº de recibos</label>
                                            <label class="d-block data form-data mb-3 text-lg-center" hs-entity="Remesa" hs-field="numerorecibos">0</label>
                                        </div>                                        
                                        <!-- Importe total remesa -->
                                        <div class="col-12 col-lg">
                                            <label class="d-block font-weight-bold text-lg-center">Importe remesa</label>
                                            <label class="d-block mb-3 data form-data text-lg-center" hs-entity="Remesa" hs-field="totalamount">0€</label>                                            
                                        </div>

                                    </div>

                                    <!-- Administrador -->
                                    <label class="d-block font-weight-bold">Administrador</label>
                                    <label class="data mb-3 form-data d-block text-truncate" hs-entity="Remesa" hs-field="customername"></label>

                                    <!-- Banco domicialización -->
                                    <label class="d-block font-weight-bold">Cuenta domiciliación cobro</label>
                                    <label class="d-block mb-3 text-truncate data form-data" hs-entity="Remesa" hs-field="creditoraccountiban"></label>

                                    <label class="d-block font-weight-bold">Fichero generado</label>
                                    <label class="d-block">
                                        <a href="" target="_blank" class="form-data data btn btn-outline-secondary text-lowercase rounded-pill shadow pl-2 pr-3 py-0" hs-entity="Remesa" hs-field="remesafile"><i class="bi bi-cloud-arrow-down pr-2"></i> ver fichero remesa</a>  
                                        <label class="d-block form-data data text-lowercase mt-2" hs-entity="Remesa" hs-field="remesafile"></label>
                                    </label>

                                </div>

                            </div>

                        </form>

                    </div>

                    <div class="col-12 col-lg-8 pl-lg-4">
                        <div class="row h-100">
                            <div class="col-12 shadow-neumorphic h-100 br-10 p-3 border">
                                <p class="card-title">Recibos presentados</p>
                                <table class="table table-bordered table-hover my-0 hs-tabla" name="listadoRecibos" id="listadoRecibos" data-model="">
                                    <thead class="thead"></thead>
                                    <tbody class="tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>