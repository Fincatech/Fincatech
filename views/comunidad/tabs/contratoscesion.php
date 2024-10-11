<div class="tab-pane fade flex-grow-1 space-between col-12" id="rgpdcontratoscesion" role="tabpanel">

    <!-- Mensaje informativo -->
    <div class="row mt-3">

        <div class="col-12">

            <div class="alert alert-light rounded shadow-neumorphic" role="alert">

                <div class="alert-icon align-items-center d-flex">
                    <i class="bi bi-info-circle" style="font-size: 26px;"></i>
                </div>

                <div class="alert-message text-dark">
                    <p class="mb-0">En esta sección podrá archivar los contratos de cesión de datos con terceros, tan solo es necesario firmar este contrato en el supuesto que la comunidad disponga de asesoría laboral o fiscal.</p>
                    <p class="mb-0">En el supuesto que empresas externas le soliciten firmar un documento de confidencialidad, que es preparados por ellos, podrá archivarlo en esta sección</p>
                </div>

            </div>            

        </div>

    </div>

    <!-- Tabla documentos a adjuntar -->
    <!-- <div class="row mt-1">
        <div class="col-12">
            <div class="alert alert-secondary m-0 justify-content-center rounded" role="alert">
                <p class="m-0 p-3 text-uppercase">Documentos modelo disponibles para descargar</p>
            </div>            
        </div>
    </div> -->

    <div class="row mt-1">
        <div class="col-12">
            <table class="table my-0 hs-tabla w-100 no-clicable border-0" name="listadoDocumentacionContratosCesion" id="listadoDocumentacionContratosCesion" data-model="Requerimiento">
                <thead class="thead"></thead>
                <tbody class="tbody"></tbody>
            </table>
        </div>
    </div>

    <!-- Tabla contratos adjuntados -->
    <div class="row mt-1">
        <div class="col-12">
                <div class="row w-100">
                    <div class="col-12 text-center align-self-center">
                        <a href="javascript:void(0);" class="btnAdjuntarDocumentoRGPD btn btn-outline-primary text-uppercase rounded-pill shadow pl-2 pr-4" data-tipo="contratoscesion"><i class="bi bi-plus-circle pr-3"></i> Añadir nuevo contrato de cesión de datos a terceros</a>
                    </div>
                </div>
        </div>
    </div>
    <div class="row flex-grow-1">
        <div class="col-12">    
            <table class="table table-bordered table-hover my-0 hs-tabla w-100 no-clicable" name="listadoContratosCesion" id="listadoContratosCesion" data-model="Comunidad">
                <thead class="thead"></thead>
                <tbody class="tbody"></tbody>
            </table>
        </div>
    </div>

</div>