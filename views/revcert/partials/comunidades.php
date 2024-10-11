<div class="tab-pane fade show active h-100" id="comunidades" role="tabpanel">
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                <div class="card-header pl-1 mb-2">
                    <div class="row">
                        <div class="col-12 col-md-9">
                            <h5 class="card-title mb-0"><i class="bi bi-folder-check mr-2"></i> <span class="titulo">Comunidades con solicitud de certificado pendiente de validación</span></h5>                            
                        </div>
                    </div>
                </div>
                <div class="card-body shadow-inset rounded-lg border mb-1 border-white space-between">
                    <!-- Listado de comunidades pendientes de validación -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-0">
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-hover my-0 hs-tabla w-100 no-clicable" name="listadoComunidadesPendientesCertificado" id="listadoComunidadesPendientesCertificado" data-model="Comunidad">
                                        <thead class="thead"></thead>
                                        <tbody class="tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>                 
                </div>
            </div>
        </div>
    </div>
    <!-- Documentos asociados a la comunidad seleccionada -->
    <div class="row flex-grow-1">
        <div class="col-12 d-flex">
            <div class="card flex-fill shadow-neumorphic pl-3 pb-3 pt-2 pr-3">
                <div class="card-header pl-1 mb-2">
                    <div class="row">
                        <div class="col-12 col-md-9">
                            <h5 class="card-title p-0 mb-0"><span class="titulo">Documentos aportados por la comunidad <span class="nombreComunidad"></span></span></h5>                            
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 m-0">
                    <label class="infoComunidad"><i class="bi bi-info-circle"></i> Seleccione una comunidad del listado superior para poder ver la documentación aportada por el administrador</label>
                    <table class="table table-bordered table-hover my-0 hs-tabla w-100 no-clicable" name="listadoComunidadesDocumentosCertificado" id="listadoComunidadesDocumentosCertificado" data-model="Comunidad">
                        <thead class="thead"></thead>
                        <tbody class="tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>      
</div> 