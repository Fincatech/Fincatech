<div class="row h-100">

    <div class="col-12">
        <div class="row h-100">
            <form class="d-flex flex-column">
                <!-- TÍTULO DE PANTALLA -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="form-label font-weight-bold d-block">SOLICITUD DE CERTIFICADO DIGITAL</h4>
                    </div>
                </div>                                                        
                
                <!-- Comunidades para las que solicita el certificado digital -->
                <div class="row mt-3">
                    <div class="col-12 text-left">
                        <p class="font-weight-bold">Comunidades para las que solicita el certificado digital</p>
                        <label class="form-label comunidadesCertificadoSolicitado pb-3 d-block text-justify font-weight-light">&nbsp;</label>
                    </div>
                </div>
                
                <!-- Documento identificativo -->
                <div class="row wrapperDocumentoIdentificativo">
                    <div class="col-12">
                        <label class="form-label font-weight-bold text-uppercase">Documento identificativo</label>
                        <p class="mb-0">Para poder emitir el certificado de cada una de las comunidades seleccionadas necesitamos que nos adjunte la cara frontal y trasera de su documento identificativo.</p>
                        <p class="mb-1 mt-0">Sólo se admiten ficheros de imagen JPG o PNG.</p>
                        <p><strong>NOTA</strong>: Estas imágenes no son almacenadas en los servidores de Fincatech sino que se envían directamente a la autoridad de registro correspondiente.</p>
                        <form>
                        <div class="row pl-2 pr-2">
                            <!-- Parte frontal -->
                            <div class="col-6">
                                <div class="wrapperDocumento border border-1 rounded-lg shadow-neumorphic pb-3 pt-3">
                                    <p class="text-center">Adjunte la imagen frontal de su documento de identidad</p>
                                    <img src="" class="imgDocument imgfileFrontDocument">
                                    <div class="custom-file" style="width: 70%;margin: 0 auto;">
                                        <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input" id="fileFrontDocument" name="fileFrontDocument" lang="es">
                                        <label class="custom-file-label" for="fileFrontDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Seleccionar Imagen Frontal Documento</label>
                                    </div>                                    
                                </div>
                            </div>
                            <!-- Parte trasera -->
                            <div class="col-6">
                                <div class="wrapperDocumento border border-1 rounded-lg shadow-neumorphic pb-3 pt-3">
                                    <p class="text-center">Adjunte la imagen trasera de su documento de identidad</p>
                                    <img src="" class="imgDocument imgfileRearDocument">
                                    <div class="custom-file" style="width: 70%;margin: 0 auto;">
                                        <input type="file" accept="image/png, image/jpeg, .jpg" class="custom-file-input" id="fileRearDocument" name="fileRearDocument" lang="es">
                                        <label class="custom-file-label" for="fileRearDocument" style="border-radius: 20px;cursor: pointer;"><i class="bi bi-image"></i> Seleccionar Imagen Trasera Documento</label>
                                    </div>                                         
                                </div>    
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

                <!-- Información -->
                <div class="row">
                    <div class="col-12 justify-content-center d-flex align-items-center">
                        <p class="text-danger mensajeError m-0 mt-3"></p>
                    </div>
                </div>
                <div class="row mb-3 flex-grow-1 align-items-end mt-3">
                    <div class="col-6 text-right">
                        <a href="javascript:Swal.close();" class="btn btn-danger d-inline">Cancelar solicitud</a>
                    </div>
                    <div class="col-6 text-left">
                        <a href="javascript:void(0);" class="btn btn-success btnProcesarSolicitudCertificados d-inline">Solicitar certificados</a>
                    </div>
                </div>                            
            </form>
        </div>
    </div>

</div>