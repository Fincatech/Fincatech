<div class="row">
    <div class="col-12">
        <p class="titulo">Generación de informe en fichero Excel</p>
    </div>
</div>
<form class="form-data form-inline text-left">
    <!-- Cliente -->
     <div class="row g-3">
        <div class="col">
            <!-- Administrador -->
            <label for="nombre" class="font-weight-bold mb-2"><i class="bi bi-people pr-2"></i>Administrador</label>                
            <select id="usuarioId" name="usuarioId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-seleccionar="true" hs-entity="Administrador" hs-field="id" hs-list-entity="Administrador" hs-list-field="nombre" hs-list-value="id"></select>
            <small><i class="bi bi-info-circle"></i> Si no selecciona ningún administrador se generará un informe con todos los administradores</small>
        </div>
     </div>
    <!-- Periodo de generación -->
    <div class="row mt-3">
        <div class="col">
            <p class="font-weight-bold mb-1"><i class="bi bi-calendar-range pr-2"></i>Período para el que desea generar el informe</p>
            <div class="row my-3">
                <div class="form-group col-12 col-lg-6">
                    <label for="exportDateFrom">Desde</label>
                    <input type="date" id="exportDateFrom" name="exportDateFrom" class="form-control">
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="exportDateTo">Hasta</label>
                    <input type="date" id="exportDateTo" name="exportDateTo" class="form-control">
                </div>  
            </div>
        </div>
    </div>     
    <!-- Estado de la factura -->
    <div class="row">
        <div class="col">
            <p class="font-weight-bold"><i class="bi bi-activity pr-2"></i>Estado de la factura</p>
            <div class="d-flex align-items-center">
                <div class="form-check form-check-inline">
                    <input type="radio" id="rbEstadoCobrada" name="rbEstado" value="C" class="form-check-input" checked>
                    <label class="form-check-label text-lowercase" for="rbEstadoCobrada">Cobrada</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="rbEstadoPendiente" name="rbEstado" value="P" class="form-check-input">
                    <label class="form-check-label text-lowercase" for="rbEstadoPendiente">Pendiente de cobro</label>
                </div>                  
                <div class="form-check form-check-inline">
                    <input type="radio" id="rbEstadoDevuelta" name="rbEstado" value="D" class="form-check-input">
                    <label class="form-check-label text-lowercase" for="rbEstadoDevuelta">Devuelta</label>
                </div>
                  </div>
        </div>     
    </div>
    <!-- Botones de acción -->
    <div class="row mt-3 text-center">
        <div class="col">
            <a href="javascript:swal.close();" class="btn btn-outline-danger mr-2"><i class="bi bi-x-circle mr-2"></i>Cancelar</a>
            <a href="javascript:void(0);" class="btn btn-outline-success mr-2 btnGenerarInformeExcel"><i class="bi bi-file-earmark-spreadsheet mr-2"></i>Generar y descargar informe</a>
        </div>
    </div>
</form>