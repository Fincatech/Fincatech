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
                <label for="ficheroadjuntar" class="form-label">Cargue el contrato que desea ser firmado.<br><span class="text-danger">Solo se admiten ficheros PDF.</span></label>
                <div class="custom-file">
                    <input type="file" accept=".pdf" class="custom-file-input" name="ficheroadjuntar" id="ficheroadjuntar" lang="es">
                    <label class="custom-file-label" for="documentoContrato">Seleccionar Archivo</label>
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