<div class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">

                <div class="row">

                    <div class="col-12 col-md-9">
                        <h5 class="card-title mb-0"> <i class="bi bi-file-earmark-text mr-2"></i> Mis empleados</h5>
                    </div>

                    <div class="col-12 col-md-3 text-right">
                        <a href="<?php echo HOME_URL;?>contratista/empleado" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> NUEVO EMPLEADO</a>
                    </div>
                </div>

            </div>
            
            <div class="card-body pl-3 pr-3 pb-3 space-between">

                <div class="row flex-grow-1">

                    <div class="col-12">

                        <table class="table table-hover my-0 hs-tabla" name="listadoEmpleadosContratista" id="listadoEmpleadosContratista" data-model="empleados">
                            <thead class="thead"></thead>
                            <tbody class="tbody"></tbody>
                        </table>

                    </div>

                </div>

                <div class="row flex-grow-1">

                    <div class="col-12 text-center p-2 align-self-start wrapperDocumentacionEmpleado">
                        
                        <p class="m-0 mensajeInformacion"><i class="bi bi-info-circle"></i> Seleccione un empleado del listado para ver los requerimientos asociados</p>
                        <p class="m-0 empleadoRequerimientosInfo font-weight-bold text-uppercase shadow-neumorphic pt-2 pb-2 rounded-pill bg-secondary text-white" style="display:none;"></p>
                        <table class="table table-hover my-0 hs-tabla d-none" name="listadoDocumentacionEmpleado" id="listadoDocumentacionEmpleado" data-model="empleado">
                            <thead class="thead"></thead>
                            <tbody class="tbody"></tbody>
                        </table>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
