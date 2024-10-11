<nav class="navbar navbar-expand navbar-light navbar-bg">
<?php if ($App->isAdminFincas() || $App->isContratista()): ?>
    <a class="sidebar-toggle d-flex">
        <i class="hamburger align-self-center"></i>
    </a>
<?php endif; ?>
   
    <div class="navbar-collapse collapse">

        <img src="<?php echo HOME_URL ;?>public/assets/img/logo-<?php echo $App->GetLogo(); ?>.png" style="max-width: 130px;">    

        <ul class="navbar-nav navbar-align">

            <?php if( !$App->isContratista() ): ?>

                <li class="nav-item align-self-center">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Ir al Dashboard" href="<?php echo HOME_URL . "dashboard"; ?>" id="home">
                        <div class="position-relative text-center text-info">
                            <i class="bi bi-house-door mr-2"></i><span class="text-uppercase" style="font-size: 12px;">Dashboard</span>
                        </div>
                    </a>
                </li>

            <?php endif; ?>

            <?php if( $App->isContratista() ): ?>
                <!-- CONTRATISTA -->
                <li class="nav-item align-self-center">
                    <a href="<?php echo HOME_URL;?>videotutoriales" class="nav-icon" role="button">
                        <div class="position-relative text-center text-info">
                            <i class="bi bi-camera-video mr-2"></i><span class="text-uppercase" style="font-size: 12px;">Videotutoriales</span>
                        </div>
                    </a>
                </li>                
                <!-- CONTRATISTA -->
                <li class="nav-item align-self-center">
                    <a href="<?php echo HOME_URL;?>contratista/documentos" class="nav-icon" role="button">
                        <div class="position-relative text-center text-info">
                            <i class="bi bi-file-earmark-text mr-2"></i><span class="text-uppercase" style="font-size: 12px;">Mis documentos</span>
                        </div>
                    </a>
                </li>
                <li class="nav-item align-self-center">
                    <a href="<?php echo HOME_URL;?>contratista/empleados" class="nav-icon" role="button">
                        <div class="position-relative text-center text-info">
                            <i class="bi bi-people-fill mr-2"></i><span class="text-uppercase" style="font-size: 12px;">Mis empleados</span>
                        </div>
                    </a>
                </li>

            <?php endif; ?>

            <!-- Notas informativas, Documentación Básica e Informes de valoración y seguimiento solo si es administrador de fincas -->
            <?php if($App->isAdminFincas()): ?>
                <!-- Home -->
                <li class="nav-item align-self-center">
                    <a href="<?php echo HOME_URL;?>videotutoriales" data-toggle="tooltip" data-placement="bottom" title="Videotutoriales de uso de la plataforma"  class="nav-icon" role="button">
                        <div class="position-relative text-center text-info">
                            <i class="bi bi-camera-video mr-2"></i><span class="text-uppercase" style="font-size: 12px;">Videotutoriales</span>
                        </div>
                    </a>
                </li>     
                <!-- Requerimientos pendientes -->              
                <li class="nav-item align-self-center">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Listado requerimientos pendientes" href="<?php echo HOME_URL . "requerimiento/pendientes"; ?>">
                        <div class="pposition-relative text-center text-info">
                            <i class="bi bi-shield-exclamation mr-2" style="font-size: 18px;"></i><span class="text-uppercase" style="font-size: 12px;">Requerimientos pendientes</span>
                        </div>
                    </a>                    
                </li>        
                <!-- Nueva comunidad -->          
                <li class="nav-item d-none">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Añadir nueva comunidad" href="<?php echo HOME_URL . "comunidad/add"; ?>">
                        <div class="position-relative pt-1">
                            <i class="bi bi-building mr-2" style="font-size:14px;"></i><span class="text-uppercase" style="font-size: 12px;">Añadir comunidad</span>
                        </div>
                    </a>                    
                </li>                            

            <?php endif; ?>
            <!-- Notificaciones -->
            <?php $App->renderView('componentes/notificaciones/notificacionesmenu.php'); ?>

            <!-- Solo para DPD y ADMINFINCAS -->
            <?php if($App->getUserRol() == 'ROLE_DPD' || $App->getUserRol() == 'ROLE_ADMINFINCAS'): ?>
                <li class="nav-item dropdown d-none">
                    <a class="nav-icon dropdown-toggle" data-toggle="tooltip" data-placement="bottom" title="Consultas al DPD" href="#" id="messagesDropdown" data-toggle="dropdown">
                        <div class="position-relative">
                            <i class="align-middle" data-feather="message-square"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right py-0" aria-labelledby="messagesDropdown">
                        <div class="dropdown-menu-header">
                            <div class="position-relative">
                                4 New Messages
                            </div>
                        </div>
                        <div class="list-group">
                            <a href="#" class="list-group-item">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2">
                                        <img src="<?php echo ASSETS_IMG; ?>avatars/avatar-5.jpg" class="avatar img-fluid rounded-circle" alt="Vanessa Tucker">
                                    </div>
                                    <div class="col-10 pl-2">
                                        <div class="text-dark">Vanessa Tucker</div>
                                        <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                                        <div class="text-muted small mt-1">15m ago</div>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2">
                                        <img src="<?php echo ASSETS_IMG; ?>avatars/avatar-2.jpg" class="avatar img-fluid rounded-circle" alt="William Harris">
                                    </div>
                                    <div class="col-10 pl-2">
                                        <div class="text-dark">William Harris</div>
                                        <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                                        <div class="text-muted small mt-1">2h ago</div>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2">
                                        <img src="<?php echo ASSETS_IMG; ?>avatars/avatar-4.jpg" class="avatar img-fluid rounded-circle" alt="Christina Mason">
                                    </div>
                                    <div class="col-10 pl-2">
                                        <div class="text-dark">Christina Mason</div>
                                        <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                                        <div class="text-muted small mt-1">4h ago</div>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2">
                                        <img src="<?php echo ASSETS_IMG; ?>avatars/avatar-3.jpg" class="avatar img-fluid rounded-circle" alt="Sharon Lessman">
                                    </div>
                                    <div class="col-10 pl-2">
                                        <div class="text-dark">Sharon Lessman</div>
                                        <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac, mattis non.</div>
                                        <div class="text-muted small mt-1">5h ago</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="dropdown-menu-footer">
                            <a href="#" class="text-muted">Show all messages</a>
                        </div>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Perfil y acciones principales -->
            <li class="nav-item dropdown">
                    <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-toggle="dropdown">
                        <i class="align-middle" data-feather="settings"></i>
                    </a>
                <?php //endif; ?>
                <a class="nav-link dropdown-toggle d-none d-sm-flex align-items-center" href="#" data-toggle="dropdown">
                    <!-- <img src="<?php //echo ASSETS_IMG; ?>avatars/avatar.jpg" class="avatar img-fluid rounded mr-1" alt="Oscar Rodríguez" />--><i class="bi bi-person-circle pr-2" style="font-size:20px;"></i> <span class="text-dark usuarioFincatech">&nbsp;</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- Cambiar contraseña -->
                    <a class="dropdown-item disabled" href="javascript:void(0);">Mi cuenta</a>
                    <a class="dropdown-item btnChangePassword" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="unlock"></i> Cambiar contraseña</a>
                    <?php if(!$App->isSudo()): ?>
                        <a class="dropdown-item disabled d-none" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="user"></i> Perfil</a>
                    <?php endif; ?>

                    <?php if($App->isSudo()): ?>
                        <!-- <div class="dropdown-divider"></div> -->
                        <a class="dropdown-item disabled" href="javascript:void(0);">Herramientas de comunidades</a>
                        <a class="dropdown-item btnCargarComunidadesExcel" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="folder-plus"></i> Cargar comunidades desde plantilla</a>
                        <a class="dropdown-item btnActualizacionServicios" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="folder-plus"></i> Actualización masiva servicios comunidades</a>
                        <a class="dropdown-item btnReasignacionProveedores" href="<?php echo HOME_URL . "proveedor/reasignacion"; ?>"><i class="bi bi-people-fill"></i> Reasignación de proveedor</a>
                    <?php endif; ?>
                    <?php if(!$App->isSudo()): ?>
                        <!-- <a class="dropdown-item disabled" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="settings"></i> Editar perfil</a> -->
                    <?php endif; ?>

                    <!-- Sólo para usuarios de tipo administrador de fincas -->
                    <?php if($App->isAdminFincas() ) : ?>
                        <?php if(!$App->isAuhorizedUser()): ?>

                            <a class="dropdown-item disabled" href="javascript:void(0);">Gestión empleados y usuarios</a>
                            <a class="dropdown-item" href="<?php echo HOME_URL ;?>autorizado/list"><i class="align-middle mr-1" data-feather="users"></i> Usuarios autorizados</a>
                            <a class="dropdown-item" href="<?php echo HOME_URL ;?>empleado/rgpd"><i class="align-middle mr-1" data-feather="eye"></i> RGPD empleados administración</a>

                            <!-- <div class="dropdown-divider"></div> -->
                            <a class="dropdown-item disabled" href="javascript:void(0);">Certificados digitales</a>
                            <a class="dropdown-item d-none" href="<?php echo HOME_URL ;?>certificadodigital/solicitudes"><i class="align-middle mr-1" data-feather="lock"></i> Certificados digitales</a>
                            <a class="dropdown-item" href="<?php echo HOME_URL ;?>certificados/dashboard"><i class="align-middle mr-1" data-feather="mail"></i> Envíos certificados</a>

                        <?php endif; ?>

                        <!-- <div class="dropdown-divider"></div> -->
                            <a class="dropdown-item disabled" href="javascript:void(0);">Listados</a>
                            <a class="dropdown-item" href="<?php echo HOME_URL ;?>comunidad/proveedores"><i class="align-middle mr-1" data-feather="list"></i> Proveedores asignados a comunidades</a>

                    <?php endif; ?>

                    <!-- <a class="dropdown-item" href="#"><i class="align-middle mr-1" data-feather="help-circle"></i> Ayuda</a> -->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item btnLogout" href="javascript:void(0);"><i class="bi bi-box-arrow-left" style="font-size:18px;"></i> Cerrar sesión</a>
                </div>

            </li>

        </ul>

    </div>

</nav>