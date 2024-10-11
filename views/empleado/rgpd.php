<div id="appContainer" class="row flex-grow-1">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic">

            <div class="card-header">
                    <h5 class="card-title mb-0">RGPD Empleados Administración</h5>
            </div>
            
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-12 col-md-6 text-left">
                            <a href="<?php echo HOME_URL;?>public/storage/templates/compromiso_de_confidencialidad_empleado_administracion.doc" target="_blank">
                                <p class="m-0 d-inline-flex">
                                    <i class="bi bi-cloud-arrow-up text-primary pr-3" style="font-size: 26px;"></i> 
                                    <span class="align-self-center">Descargar modelo plantilla</span>
                                </p>
                            </a>
                    </div>                    
                    <div class="col-12 col-md-6 text-right">
                        <a href="javascript:void(0);" class="btnAdjuntarDocumentoEmpleadoRGPD btn btn-outline-primary text-uppercase rounded-pill shadow pl-2 pr-4" data-tipo="rgpdempleado"><i class="bi bi-plus-circle pr-3"></i> Añadir nuevo contrato de confidencialidad de empleado</a>
                    </div>
                </div>
                <div class="row h-100">
                    <div class="col-12">
                        <table class="table table-bordered table-hover my-0 hs-tabla h-100" name="listadoRGPDEmpleadosAdministracion" id="listadoRGPDEmpleadosAdministracion" data-model="rgpdempleadosadministracion">
                            <thead class="thead"></thead>
                            <tbody class="tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>