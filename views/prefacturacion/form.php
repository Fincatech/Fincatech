<div id="appContainer" class="row flex-grow-1 form-data">

    <div class="col-12 d-flex">

        <div class="card flex-fill shadow-neumorphic card-principal">

            <div class="card-header pl-0 headerListado">
                <div class="row">
                    <div class="col-12">
                        <h5 class="card-title mb-0 pl-4"><i class="bi bi-calculator pr-2"></i> Pre-Facturación</h5>
                    </div>
                </div>
            </div>
            
            <div class="card-body pt-0 space-between">

                <div class="row g-3">
                    <div class="col-12 col-md-8 text-left">
                        <label for="usuarioId"><i class="bi bi-person-fill pr-2"></i>Seleccione el administrador para el que desea realizar el cálculo de la prefacturación</label>
                        <select id="usuarioId" name="usuarioId" class="select-data custom-select data form-control selectpicker" data-live-search="true" hs-list-entity="Administrador" hs-list-field="Administrador.nombre" hs-list-value="Administrador.id"></select>
                    </div>                   
                </div>
                
                <div class="row mt-3">
                    <div class="col-12 col-md-2">
                        <label><i class="bi bi-calendar3-week"></i> Fecha desde</label>
                        <input type="date" class="form-control fechaDesde" placeholder="dd/mm/aaaa" aria-label="Fecha desde">
                    </div>
                    <div class="col-12 col-md-2">
                        <label><i class="bi bi-calendar3"></i> Fecha hasta</label>
                        <input type="date" class="form-control fechaHasta" placeholder="dd/mm/aaaa" aria-label="Fecha hasta">
                    </div> 
                    <div class="col-12 mt-3">
                        <label class="text-danger" style="text-transform: initial;">NOTA: Deje las fechas vacías si quiere que se recupere toda la información independientemente de la fecha de alta de las comunidades que tenga asignadas el administrador seleccionado</label>
                    </div>
                </div>
                
                <div class="row mt-5 flex-grow-1 align-items-center">
                    <div class="col-12 text-center">
                        <a href="javascript:void(0);" class="btn btn-ouline-dark border shadow-neumorphic rounded-lg pt-3 pb-3 btnGenerarInformePrefacturacion" tabindex="-1" role="button" aria-disabled="true"><i class="bi bi-file-earmark-spreadsheet" style="font-size: 36px;"></i><br/>Generar y descargar Informe Excel</a>
                    </div>
                </div>                

            </div>

        </div>

    </div>

</div>