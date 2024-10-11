<div class="row">

    <div class="col-12 flex">

        <form class="form form-smsContrato">
            <!-- Tlf del destinatario -->
            <div class="form-group mb-3">
                    <label for="telefonoDestinatarioContrato" class="form-label">Nº teléfono del destinatario</label>
                    <input type="phone" maxlength="15" class="form-control shadow-inset" id="telefonoDestinatarioContrato" placeholder="Escriba el número de teléfono">
                    <label for="telefonoDestinatario" class="form-label">Escriba el prefijo del país seguido del número de móvil sin espacios ni guiones. Ej: 34600123456</label>
            </div>

            <!-- Mensaje -->
            <div class="form-group mb-3 smsContrato">
                    <label for="mensajeSMSContrato" class="form-label">Mensaje</label>
                    <textarea id="mensajeSMSContrato" data-origin="smsContrato" class="form-control shadow-inset mensajeSMS" rows="3" maxlength="160"></textarea>
                    <label class="form-label"><span class="smsCaracteresRestantes">160</span> caracteres restantes</label>
            </div>

            <!-- Contrato -->
            <div class="form-group mb-3">
                <p class="mb-0"><i class="bi bi-file-pdf-fill"></i>  Cargue el contrato para firmar.</p>
                <p class="font-size-small text-primary">
                    <small><i class="bi bi-info-circle-fill"></i> Solo se admite un fichero en formato PDF por envío</small>
                </p>
                <div class="custom-file w-100">
                    <!-- <label class="form-label" for="ficheroadjuntarcontrato"><i class="bi bi-file-pdf-fill"></i> Seleccionar archivo</label> -->
                    <input type="file" accept=".pdf" class="form-control form-control-sm border-0 shadow-inset p-2" name="ficheroadjuntarcontrato" id="ficheroadjuntarcontrato" lang="es">
                </div>                
                    
            </div>

        </form>

    </div>

</div>

<div class="row">
    <div class="col-12 text-center">
        <a href="javascript:void(0);" class="btn btn-success btnEnviarSMSCertificadoContrato">Enviar contrato para su firma digital</a>
    </div>
</div>