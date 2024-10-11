<div class="row">
    <div class="col-12">
        <p class="text-left">Seleccione un administrador desde el listado y pulse el botón "Exportar comunidades" para descargar un fichero excel con la información de las comunidades contratadas por el administrador y los servicios asociados.</p>
    </div>
</div>

<div class="row">
    <div class="col-12 text-left">
        <p class="mt-3 text-justify" style="font-size: 14px;">1. Seleccione el fichero excel desde el que desea realizar la actualización masiva de servicios de comunidades</p>    
        <p class="mt-3 text-left text-justify" style="font-size: 14px;">2. Una vez seleccionado, clique sobre el botón <span class="font-weight-bold">ACTUALIZAR SERVICIOS</span></p>
    </div>
</div>

<div class="form-group row mb-2 justify-content-center wrapperSelectorFichero">

    <div class="col-12">  

        <div class="wrapperFichero row border rounded-lg ml-0 mr-0 mb-0 shadow-inset border-1 pt-3 pb-2">
            <div class="col-2 align-self-center h-100 text-center">
                <i class="bi bi-cloud-arrow-up text-info" style="font-size: 30px;"></i>
            </div>
            <div class="col-10 pl-0 align-self-center">
                <input accept=".xls, .xlsx" class="form-control form-control-sm ficheroAdjuntarExcel border-0" hs-fichero-entity="Administrador" id="ficheroAdjuntarExcel" type="file">
            </div>       
        </div>

        <span class="pb-3 d-block text-center pt-2 font-weight-bold" style="font-size: 13px;"><i class="bi bi-info-circle"></i> Sólo se permiten ficheros con extensión xls o xlsx</span>    
        
        <!-- Mensaje de error --> 
        <div class="wrapperMensajeErrorCarga row text-light p-3" style="display: none; font-size: 14px;">
            <div class="col-12 bg-danger p-3 rounded shadow-neumorphic">
                <p class="mensaje"></p>
            </div>
        </div>          

        <!-- Botón de iniciar proceso -->
        <div class="row mt-3">
            <div class="col-12">
                <a href="javascript:void(0);" class="btn d-block btn-success bntActualizarProcesos pt-3 pb-3">ACTUALIZAR SERVICIOS</a>
            </div>
        </div>
    </div>
</div>        

<!-- Progreso de actualización de servicios -->
<div class="wrapperProgresoCarga row" style="display: none;">
    <div class="col-12">
        <label class="text-center mb-2 mt-3">Procesando fichero</label>
        <div class="progress mb-3" style="height: 30px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">Animated</div>
        </div>
        <label class="progresoCarga">(n de y procesados)</label>
        <div class="row mt-3 btnCerrarProceso" style="display: none;">
            <div class="col-12">
            <a href="javascript:swal.close();" class="btn btn-success">OK</a>
            </div>
        </div>
    </div>
</div>