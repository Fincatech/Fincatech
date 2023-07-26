<div class="card flex-fill shadow-neumorphic">
    <div class="card-header">
        <h5 class="card-title mb-0">Videotutoriales de Fincatech</h5>
    </div>
    <div class="card-body pt-0">
        <?php if($App->isContratista()): ?>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="embed-responsive embed-responsive-16by9 shadow-neumorphic p-2 rounded rounded-lg bg-secondary">
                        <h6 class="text-white"><i class="bi bi-camera-video mr-2"></i>Subir documentación a la plataforma Fincatech</h5>
                        <iframe class="embed-responsive-item w-100" src="/public/assets/video/video_tutorial_empresa_externa.mp4"></iframe>
                    </div>
                </div>
            </div>
        <?php endif; ?>

            <?php if($App->isAdminFincas()): ?>
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="embed-responsive embed-responsive-16by9 shadow-neumorphic p-2 rounded rounded-lg bg-secondary">
                            <h6 class="text-white"><i class="bi bi-camera-video mr-2"></i>Tutorial RGPD para AAFFS</h5>
                            <iframe class="embed-responsive-item w-100" src="/public/assets/video/VIDEO_TUTORIAL_RGPD_PARA_AAFF.mp4"></iframe>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="embed-responsive embed-responsive-16by9 shadow-neumorphic p-2 rounded rounded-lg bg-secondary">
                            <h6 class="text-white"><i class="bi bi-camera-video mr-2"></i>Tutorial CAE para AAFF</h5>
                            <iframe class="embed-responsive-item w-100" src="/public/assets/video/VIDEO_TUTORIAL_CAE_PARA_AAFF.mp4"></iframe>
                        </div>
                    </div>                    
                </div>
            <?php endif; ?>
        </div>
</div>