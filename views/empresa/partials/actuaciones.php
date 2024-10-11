<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-title mb-0 mt-3">
                <div class="m-0 justify-content-center rounded shadow-neumorphic bg-light" role="alert">
                    <p class="m-0 p-3 text-center text-uppercase">Seguimiento</p>
                    <input type="hidden" id="seguimientoId" name="seguimientoId">
                </div>
            </div>

            <div class="card-body py-0 px-0">

                <div class="row h-100 pb-4">

                    <div class="col-12 col-md-6 py-3">
                        <div class="shadow-neumorphic p-3 h-100">
                            <!-- Titulo -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4 class="card card-title mb-2 text-center">Datos de la actuación</h4>
                                </div>
                            </div>

                            <!-- Formulario -->
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="seguimientoFecha">FECHA DE ACTUACIÓN</label>
                                    <input type="date" class="form-control" id="seguimientoFecha" placeholder="name@example.com">
                                </div>
                                <div class="col">
                                    <label for="seguimientoTipo">TIPO ACTUACIÓN</label>
                                    <input type="texto" class="form-control" maxlength="40" id="seguimientoTipo" placeholder="E-mail / Llamada / Otros">
                                </div>
                            </div>
                            <!-- Observaciones -->
                            <div class="row">
                                <div class="col">
                                    <label for="seguimientoObservaciones">Observaciones</label>
                                    <textarea class="form-control" maxlength="500" rows="10" id="seguimientoObservaciones"></textarea>
                                </div>                                                        
                            </div>

                            <!-- Botones de acción -->
                            <div class="form-group row mt-4 justify-content-center">
                                <div class="col-6 text-center p-3 shadow-inset rounded-pill border-light border-2">
                                    <div class="row">
                                        <div class="col-6 pr-1">
                                            <a href="javascript:void(0);" class="btn btn-dark btnResetActuacion shadow d-block pb-2 pt-2" style="border-radius: 50rem 0rem 0rem 50rem"><i class="bi bi-x-circle mr-2"></i>Nueva actuación</a>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <a href="javascript:void(0);" class="btn btnSaveActuacion btn-success shadow d-block pb-2 pt-2" style="border-radius: 0rem 50rem 50rem 0rem"><i class="bi bi-save mr-2"></i>Guardar</a>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 py-3">
                        <div class="shadow-neumorphic p-3 h-100 d-flex flex-column justify-content-between">
                            <!-- Titulo -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4 class="card-title mb-2 d-flex justify-content-between text-center">Actuaciones realizadas <span id="estadoActuacion" class="badge badge-pill p-2" style="font-size: 12px;"></span></h4>
                                </div>
                            </div>
                            <!-- Listado -->
                            <div class="row flex-grow-1">
                                <div class="col-12">
                                    <table class="table table-hover my-0 hs-tabla h-100" name="listadoSeguimientoEmpresas" id="listadoSeguimientoEmpresas" data-model="seguimiento">
                                        <thead class="thead"></thead>
                                        <tbody class="tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6 text-center p-3 shadow-inset rounded-pill border-light border-2">
                                    <div class="row">
                                        <div class="col-12 pr-1">
                                            <a href="javascript:void(0);" class="btn btn-primary btnFinishSeguimiento shadow d-block pb-2 pt-2" style="border-radius: 50rem 50rem 50rem 50rem"><i class="bi bi-card-checklist mr-2"></i>Finalizar Protocolo</a>
                                        </div>
                                    </div>
                                </div>                             
                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>

        </div>        

    </div>

</div>
<!-- Formulario de actuaciones -->


<!-- Listado de actuaciones -->