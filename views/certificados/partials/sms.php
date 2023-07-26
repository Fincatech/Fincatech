<div class="row">

    <div class="col-12 flex">

        <form class="form form-smsSimple">
            <!-- Tlf del destinatario -->
            <div class="form-group mb-3">
                    <label for="telefonoDestinatario" class="form-label">Nº teléfono del destinatario</label>
                    <input type="phone" maxlength="15" class="form-control shadow-inset" id="telefonoDestinatario" placeholder="Escriba el número de teléfono">
                    <label for="telefonoDestinatario" class="form-label">Escriba el prefijo del país seguido del número de móvil sin espacios ni guiones. Ej: 34600123456</label>
            </div>
            <!-- Mensaje -->
            <div class="form-group mb-3 smsSimple">
                    <label for="mensajeSMS" class="form-label">Mensaje</label>
                    <textarea id="mensajeSMS" data-origin="smsSimple" class="form-control shadow-inset mensajeSMS" rows="3" maxlength="160"></textarea>
                    <label class="form-label"><span class="smsCaracteresRestantes">160</span> caracteres restantes</label>
            </div>

        </form>

    </div>

</div>

<div class="row">
    <div class="col-12 text-center">
        <a href="javascript:void(0);" class="btn btn-success btnSendSimpleSMS">Enviar SMS Certificado</a>
    </div>
</div>