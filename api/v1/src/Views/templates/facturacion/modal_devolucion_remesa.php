<div class="row">
    <div class="col-12 text-center text-uppercase align-self-center">
        <p class="m-0" style="display: block; font-size: 18px;">Devolución de Recibos</p>
    </div>
</div>

<div class="row mb-2 wrapperInformacion">

    <div class="col-12">
        <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el fichero que desea adjuntar</p>
        <p class="mt-3 text-justify" style="font-size: 14px;">2. Presione el botón <strong>Procesar Devolución</strong></p>
    </div>

</div>

<div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">

    <div class="col-12">  

        <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
            <div class="col-2 align-self-center h-100 text-center">
                <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
            </div>
            <div class="col-10 pl-0 align-self-center">
                <input accept=".xml" class="form-control ficheroAdjuntar form-control-sm border-0" id="ficheroadjuntar" name="ficheroadjuntar" type="file">
            </div>       
        </div>

        <span class="pb-3 d-block text-center pt-2" style="font-size: 13px;">Sólo se permiten ficheros con extensión xml</span>    
            
        <!-- Mensaje de error --> 
        <div class="wrapperMensajeErrorCarga row text-light" style="font-size: 14px;">
            <div class="col-12 p-3">
                <p class="mensaje mb-0 text-danger text-center"></p>
            </div>
        </div>       

        <!-- Botón de adjuntar documento -->
        <div class="row mt-3">
            <div class="col-12">
            <a href="javascript:void(0);" class="btn d-block btn-success btnUploadXML pt-3 pb-3">Procesar Devolución</a>
            </div>
        </div>

    </div>

</div>
