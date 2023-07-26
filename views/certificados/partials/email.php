<div class="row">

    <div class="col-12 flex">

        <form class="form">
            <!-- Nombre de la comunidad -->
            <div class="form-group mb-3">
                    <label for="nombreDestinatario" class="form-label">Nombre de la comunidad*</label>
                    <input type="text" class="form-control" id="nombreDestinatario" placeholder="Nombre de la comunidad">
            </div>            
            <!-- E-mail del destinatario -->
            <div class="form-group mb-3">
                    <label for="emailDestinatario" class="form-label">E-mail del destinatario*</label>
                    <input type="email" class="form-control" id="emailDestinatario" placeholder="name@example.com">
            </div>
            <!-- Asunto del e-mail -->
            <div class="form-group mb-3">
                    <label for="emailAsunto" class="form-label">Asunto*</label>
                    <input type="text" class="form-control" id="emailAsunto" maxlength="50" placeholder="Escriba el asunto del correo">
            </div>                            
            <!-- Cuerpo del mensaje -->
            <div class="form-group mb-3">
                    <label for="emailBody" class="form-label">Mensaje*</label>
                    <textarea id="emailBody" class="form-control shadow-inset" rows="7"></textarea>
            </div>

        </form>

    </div>

</div>

<div class="row">
    <div class="col-12 text-center">
        <a href="javascript:void(0);" class="btn btn-success btnEnviarEmailCertificado">Enviar e-mail certificado</a>
    </div>
</div>