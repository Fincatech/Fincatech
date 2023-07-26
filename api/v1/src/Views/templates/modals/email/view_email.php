<div class="row">
    <div class="col-12 text-left">
        <!-- Fecha de envío -->
        <div class="row">
            <div class="col-1">
                <i class="bi bi-calendar3 mr-2"></i>
            </div>
            <div class="col-11 p-0">
                <p class="mb-1"><strong>Fecha de envío</strong></p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-1">&nbsp;</div>
            <div class="col-11 p-0"><?php echo date('d-m-Y', strtotime($datos->created));?></div>
        </div>
        <!-- Destinatario -->
        <div class="row">
            <div class="col-1">
                <i class="bi bi-envelope-at mr-2"></i>
            </div>
            <div class="col-11 p-0">
                <p class="mb-1"><strong>Destinatario</strong></p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-1">&nbsp;</div>
            <div class="col-11 p-0"><?php echo $datos->email;?></div>
        </div>

        <!-- Asunto -->
        <div class="row">
            <div class="col-1">
                <i class="bi bi-envelope-paper mr-2"></i>
            </div>
            <div class="col-11 p-0">
                <p class="mb-1"><strong>Asunto</strong></p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-1">&nbsp;</div>
            <div class="col-11 p-0"><?php echo $datos->subject;?></div>
        </div>
        <!-- Mensaje -->
        <div class="row">
            <div class="col-1">
                <i class="bi bi-chat-right-text"></i>
            </div>
            <div class="col-11 p-0">
                <p class="mb-1"><strong>Mensaje</strong></p>
            </div>
        </div>
        <div class="row">
            <div class="col-1">&nbsp;</div>
            <div class="col-11 pl-0"><?php echo $datos->body;?></div>
        </div>        
    </div>
</div>