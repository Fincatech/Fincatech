<?php $App->renderView('facturacion/partials/emision/progreso'); ?>

<div class="row flex-grow-1">

    <div class="col-12 d-flex position-relative">

        <div class="card flex-fill pl-3 pb-3 pt-2 pr-3 card-principal">

            <div class="card-header pl-0 headerListado mb-0 pb-0">

                <div class="row">

                    <div class="col-12 col-md-6">
                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><span class="titulo titulo-modulo pl-0"><i class="bi bi-file-earmark-ruled pr-2"></i> Remesa manual</span></h5>
                    </div>
                    <div class="col-12 col-md-6 text-right">
                        <a href="javascript:history.back(-1);" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-arrow-left"></i> Volver</a>
                    </div>        
                </div>

            </div>
            <!-- Listado e información -->
            <div class="card-body rounded-lg border-0">

                <form class="form-data form-floating form-remesa d-flex flex-grow-1" autocomplete="off">

                    <div class="listado row flex-grow-1">
                        <!-- Listado -->
                        <div class="col-12 col-lg-8">

                            <div class="shadow-neumorphic br-8 p-3">

                                <div class="row mb-3">
                                    <div class="col-12 col-lg-6">
                                        <p class="card-title">Recibos devueltos</p>
                                    </div>
                                    <div class="col-12 col-lg-6 text-right">
                                        <a href="javascript:void(0);" class="btn text-success text-uppercase pl-2 pr-4 chkSeleccionarTodo mr-2" data-table="listadoRecibosDevueltos" data-target="chkReciboDevuelto"><i class="bi bi-check-all" title="Seleccionar todos los recibos devueltos"></i> Seleccionar Todos</a>
                                        <a href="javascript:void(0);" title="Anular selección" class="btn text-danger text-uppercase pl-2 pr-4 chkDeseleccionar" data-table="listadoRecibosDevueltos" data-target="chkReciboDevuelto"><i class="bi bi-x-circle"></i> Deseleccionar todos</a>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered table-hover my-0 hs-tabla" data-order='[[ 1, "asc"]]'  name="listadoRecibosDevueltos" id="listadoRecibosDevueltos" data-model="Remesas">
                                            <thead class="thead"></thead>
                                            <tbody class="tbody"></tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Información -->
                        <div id="informacionRemesaDevuelta" class="col-12 col-lg-4">

                            <div class="row h-100">

                                <div class="col-12 br-10 p-3 d-flex flex-column" style="background:#f7f7fc;">
                                <!-- Información recibos devueltos -->
                                <div class="form-group row mb-2">

                                    <div class="row">
                                        <div class="col-12">
                                            <label class="d-block font-weight-bold card-title text-uppercase"><i class="bi bi-info-circle pr-2"></i>Información</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-lg-6">
                                            <p class="font-weight-bold mb-0">Nº de recibos devueltos</p>
                                            <p class="infoRecibosDevueltos font-weight-normal">12</p>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <p class="font-weight-bold mb-0">Importe total devuelto</p>
                                            <p class="infoRecibosDevueltosImporte font-weight-normal">12€</p>
                                        </div>
                                    </div>

                                </div>

                                <div id="informacionRecibosNuevaRemesa" class="form-group row mb-2 flex-grow-1">

                                    <div class="row">

                                        <div class="col-12 br-10 px-3 pt-3 d-flex flex-column">

                                            <label class="d-block font-weight-bold card-title text-uppercase"><i class="bi bi-receipt pr-2"></i>Recibos seleccionados</label>

                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="font-weight-bold mb-0 text-primary">Nº de recibos seleccionados: <span class="total-recibos-seleccionados text-dark font-weight-normal">0</span></p>
                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="font-weight-bold mb-0 text-primary">Total importe nueva remesa: <span class="total-importe-recibos-seleccionados text-dark font-weight-normal">0&euro;</span></p>
                                                    
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <?php $App->renderView('facturacion/partials/emision/banco'); ?>
                                                </div>
                                            </div>

                                            <!-- Botones de acción -->
                                            <div class="row flex-grow-1 align-items-start mt-3">
                                                <div class="col-12 text-center">
                                                    <a href="javascript:void(0);" class="btnGenerarRemesaManual btn btn-success br-8 d-block" title="Generar nueva remesa">Generar nueva remesa</a>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>