<nav class="navbar navbar-expand navbar-light navbar-bg">

    <?php if($App->isAdminFincas()): ?>
    
    <!-- <a class="sidebar-toggle d-flex">
        <i class="hamburger align-self-center"></i>
    </a>
    
    <form class="d-none d-sm-inline-block">
        <div class="input-group input-group-navbar">
            <input type="text" class="form-control search-input" placeholder="Buscar" data-target="" aria-label="Buscar">
            <button class="btn btnBuscarComunidad" type="button">
                <i class="align-middle" data-feather="search"></i>
            </button>
        </div>
    </form> -->

    <?php endif; ?>

    

    <div class="navbar-collapse collapse">

        <img src="<?php echo HOME_URL ;?>public/assets/img/logo-fincatech.png" style="max-width: 130px;">    

        <h3 class="card-title mb-0 ml-4"><i class="bi bi-building" style="color: #17a2b8;"></i> <span class="titulo titulo-modulo pl-0"></span></h3>

        <ul class="navbar-nav navbar-align">
            <li class="nav-item">
                <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Dashboard" href="<?php echo HOME_URL . "dashboard"; ?>" id="home">
                    <div class="position-relative text-center text-info">
                        <i class="bi bi-house-door mr-2"></i><span style="font-size: 12px;display: block;">Ir al inicio</span>
                    </div>
                </a>
            </li>
            <!-- Notas informativas, Documentación Básica e Informes de valoración y seguimiento solo si es administrador de fincas -->
            <?php if($App->isAdminFincas()): ?>
                <!-- <li class="nav-item">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Documentación básica" href="<?php echo APPFOLDER . "rgpd/documentacionbasica"; ?>" id="documentacionbasica_nav">
                        <div class="position-relative">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                    </a>                    
                </li>                            
                <li class="nav-item">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Informes de evaluación y seguimiento" href="<?php echo APPFOLDER . "rgpd/informevaloracionseguimiento"; ?>" id="informevaloracionseguimiento_nav">
                        <div class="position-relative">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                    </a>                    
                </li>  
                <li class="nav-item">
                    <a class="nav-icon" data-toggle="tooltip" data-placement="bottom" title="Notas informativas" title="Notas informativas" href="<?php echo APPFOLDER . "rgpd/notasinformativas"; ?>" id="notasinformativas_nav">
                        <div class="position-relative">
                            <i class="bi bi-journals"></i>
                        </div>
                    </a>                    
                </li> -->
            <?php endif; ?>
            <!-- Notificaciones -->
            <?php $App->renderView('componentes/notificaciones/notificacionesmenu.php'); ?>
            <!-- Mensajes: Consultas al DPD -->
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
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
                    <!-- <img src="<?php //echo ASSETS_IMG; ?>avatars/avatar.jpg" class="avatar img-fluid rounded mr-1" alt="Oscar Rodríguez" />--><i class="bi bi-person-circle" style="font-size:20px;"></i> <span class="text-dark usuarioFincatech">Óscar Rodríguez</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- Cambiar contraseña -->
                    
                    <a class="dropdown-item btnChangePassword" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="unlock"></i> Cambiar contraseña</a>
                    <?php if(!$App->isSudo()): ?>
                        <a class="dropdown-item disabled d-none" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="user"></i> Perfil</a>
                    <?php endif; ?>
                    <!-- Sólo para usuarios de tipo administrador de fincas -->
                    <?php if($App->isAdminFincas() ) : ?>
                    <!-- <a class="dropdown-item disabled" href="#"><i class="align-middle mr-1" data-feather="eye"></i> SPA Asignado</a> -->
                    <?php endif; ?>
                    <!-- <a class="dropdown-item disabled" href="#"><i class="align-middle mr-1" data-feather="users"></i> RGPD Empleados</a> -->
                    <div class="dropdown-divider"></div>
                    <?php if($App->isSudo()): ?>
                        <a class="dropdown-item btnCargarComunidadesExcel" href="javascript:void(0);"><i class="align-middle mr-1" data-feather="folder-plus"></i> Cargar comunidades desde plantilla</a>
                    <?php endif; ?>
                    <?php if(!$App->isSudo()): ?>
                        <a class="dropdown-item disabled" href="pages-settings.html"><i class="align-middle mr-1" data-feather="settings"></i> Preferencias</a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="#"><i class="align-middle mr-1" data-feather="help-circle"></i> Ayuda</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item btnLogout" href="javascript:void(0);">Cerrar sesión</a>
                </div>

            </li>

        </ul>

    </div>

</nav>