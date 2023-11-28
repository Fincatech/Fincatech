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
            <!-- Adjuntar fichero -->
            <div class="form-group mb-3">
                <label for="ficheroadjuntar" class="form-label">Adjuntar fichero</label>
                <input type="file" accept=".pdf" id="ficheroadjuntar" name="ficheroadjuntar" class="form-control border-0 pl-0" />
                <label class="form-label font-size-small text-primary"><i class="bi bi-info-circle-fill"></i> Solo se admite un fichero en formato PDF por envío</label>
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