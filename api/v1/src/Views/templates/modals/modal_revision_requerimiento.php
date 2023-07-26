<div class="row h-100">

    <div class="col-xl-8 col-12">
        <div class="ratio ratio-16x9 h-100">
            <iframe class="visorDocumento" title="Visor Documento"></iframe>
        </div>
    </div>
    <div class="col-xl-4 col-12 datosValidacion text-left">
        <div class="row h-100">
            <form class="d-flex flex-column">
                <!-- Comunidad -->
                <div class="row wrapperComunidad">
                    <div class="col-12">
                        <label class="form-label font-weight-bold d-block">Comunidad</label>
                        <label class="form-label font-weight-light nombreComunidad shadow-inset pl-3 pr-3 pt-2 pb-2 d-block">&nbsp;</label>
                    </div>
                </div>                     
                <!-- Empresa -->
                <div class="row wrapperEmpresa">
                    <div class="col-12">
                        <label class="form-label font-weight-bold d-block">Empresa</label>
                        <label class="form-label font-weight-light nombreEmpresa shadow-inset pl-3 pr-3 pt-2 pb-2 d-block">&nbsp;</label>
                    </div>
                </div>      
                <!-- Empleado -->
                <div class="row wrapperEmpleado">
                    <div class="col-12">
                        <label class="form-label font-weight-bold d-block">Nombre del empleado</label>
                        <label class="form-label font-weight-light nombreEmpleado shadow-inset pl-3 pr-3 pt-2 pb-2 d-block">&nbsp;</label>
                    </div>
                </div>  
                <!-- CIF/NIF -->
                <div class="row">
                    <div class="col-12">
                        <label class="form-label font-weight-bold d-block">CIF/NIF</label>
                        <label class="form-label font-weight-light cif shadow-inset pl-3 pr-3 pt-2 pb-2 d-block">&nbsp;</label>
                    </div>
                </div>                                        
                <!-- Nombre del documento -->
                <div class="row">
                    <div class="col-12">
                        <label class="form-label font-weight-bold d-block">Nombre del documento</label>
                        <label class="form-label font-weight-light nombreDocumento shadow-inset pl-3 pr-3 pt-2 pb-2 d-block">&nbsp;</label>
                    </div>
                </div>
                <!-- Fecha de caducidad -->
                <div class="row wrapperFechaCaducidad">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="fechaCaducidad" class="form-label font-weight-bold">Fecha de Caducidad</label>
                            <input type="date" class="form-control fechaCaducidad" id="fechaCaducidad">
                        </div>
                    </div>
                </div>
                <!-- Estado del documento -->
                <div class="row">
                    <div class="col-12">
                        <label class="form-label font-weight-bold">Seleccione un estado</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rbEstado" checked id="rbEstadoVerificado" value="6">
                        <label class="form-check-label" for="rbEstadoVerificado">Verificado</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rbEstado" id="rbEstadoRechazado" value="7">
                        <label class="form-check-label" for="rbEstadoRechazado">Rechazado</label>
                    </div>
                </div>  
                <!-- Observaciones -->
                <div class="mb-3 flex-grow-1">
                    <label for="observacionesRechazo" class="form-label font-weight-bold">Observaciones</label>
                    <textarea class="form-control observacionesRechazo" id="observacionesRechazo" rows="8"></textarea>
                </div>  
                <div class="row">
                    <div class="col-12">
                        <p class="text-danger mensajeError"></p>
                    </div>
                </div>
                <div class="row mb-3 flex-grow-1 align-items-end">
                    <div class="col-6">
                        <a href="javascript:Swal.close();" class="btn btn-danger btnCancelarCambioEstadoDocumento d-block">Cancelar</a>
                    </div>
                    <div class="col-6">
                        <a href="javascript:void(0);" class="btn btn-success btnGuardarCambioEstadoDocumento d-block">Guardar cambios</a>
                    </div>
                </div>                            
            </form>
        </div>
    </div>

</div>