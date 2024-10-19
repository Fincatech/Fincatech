<!-- Servicios -->
<div class="row">
    <div class="col-12">
        <label for="serviciosFacturacion" class="d-block col-12 font-weight-bold"><i class="bi bi-cart-check pr-2"></i>Servicios contratados y disponibles para facturar</label>
        <div class="row mb-2 mt-2 emision--servicios-noinfo">
            <div class="col-12">
                <p class="mensaje">Seleccione un administrador para ver los servicios contratados susceptibles de ser facturados</p>
            </div>
        </div>

        <div class="row mb-2 mt-2 emision--servicios-info" style="display: none;">
            <div class="col-12 col-lg-3 d-flex align-items-center pb-2">
                <p class="mb-0 text-secondary">Nombre del Servicio</p>
            </div>
            <div class="col-12 col-lg-9 pb-2">
                <p class="mb-0 text-secondary">Concepto (Opcional)</p>
            </div>
        </div>

        <!-- CAE -->
        <div class="row mb-2 servicio-cae" style="display: none;">
            <div class="col-12 col-lg-3 d-flex align-items-center">
                    <label class="form-check-label" for="chkCAE">CAE</label>
            </div>
            <div class="col-12 col-lg-9">
                <input type="text" name="conceptoCAE" id="conceptoCAE" class="form-data form-control" placeholder="Concepto" maxlength="100" />
            </div>
        </div>

        <!-- DOC CAE -->
        <div class="row mb-2 servicio-doccae" style="display: none;">
            <div class="col-12 col-lg-3 d-flex align-items-center">
                    <label class="form-check-label" for="chkDOCCAE">DOC. CAE</label>
            </div>
            <div class="col-12 col-lg-9">
                <input type="text" name="conceptoDOCCAE" id="conceptoDOCCAE" class="form-data form-control" placeholder="Concepto" maxlength="100" />
            </div>
        </div>

        <!-- DPD -->
        <div class="row mb-2 servicio-dpd" style="display: none;">
            <div class="col-12 col-lg-3 d-flex align-items-center">         
                <label class="form-check-label" for="chkDPD">DPD</label>
            </div>
            <div class="col-12 col-lg-9">
                <input type="text" name="conceptoDPD" id="conceptoDPD" class="form-data form-control" placeholder="Concepto" maxlength="100" />
            </div>            
        </div>
        <!-- Certificados digitales -->
        <div class="row mb-2 servicio-certificadosdigitales" style="display: none;">
            <div class="col-12 col-lg-3 d-flex align-items-center">         
                <label class="form-check-label" for="chkCertificado">Certificados digitales</label>
            </div>
            <div class="col-12 col-lg-9">
                <input type="text" name="conceptoCertificadosDigitales" id="conceptoCertificadosDigitales" class="form-data form-control" placeholder="Concepto" maxlength="100" />
            </div>            
        </div>

    </div>     
</div>  