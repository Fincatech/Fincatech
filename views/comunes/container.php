<div class="main">

    <?php 
if(!$App->getController() === 'ROLE_LOGIN'): 
        // Incluímos la barra superior 
        include_once('menusuperior.php'); ?>

    <main class="content">

        <div class="container-fluid p-0 h-100 space-between">

            <div class="row">
                <div class="col-auto d-none d-sm-block">
                    <h3>
                        <a href="<?php echo APPFOLDER . "dashboard"; ?>" class="text-dark btn" style="font-size: 18px;"><i data-feather="home" class="mb-1 mr-1"></i><strong>Fincatech</strong> Dashboard <?php echo $App->getTitleView(); ?></a>
                    </h3>
                </div>
            </div>

            <?php $App->getContainerView(); ?>

        </div>
        
    </main>
    
<?php   else: 
            $App->getContainerView();
        endif; 

        include_once('footer.php'); ?>

</div>