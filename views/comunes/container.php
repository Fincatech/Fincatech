<div class="main">

    <?php 
// if($App->getController() != 'login' && $App->isLogged()): 
if( $App->isLogged() ): 

        //  Incluímos la barra superior 
            include_once('menusuperior.php'); 
        ?>

    <main class="content">

        <div class="container-fluid p-0 h-100 space-between">

            <?php $App->getContainerView(); ?>

        </div>
        
    </main>

<?php   
else: 
    $App->getContainerView();
endif; 

include_once('footer.php'); ?>

</div>