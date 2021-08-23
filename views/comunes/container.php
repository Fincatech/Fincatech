<div class="main">

    <?php 
        // Incluímos la barra superior 
        include_once('menusuperior.php'); ?>

    <main class="content">

        <div class="container-fluid p-0">

            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h3><strong>Fincatech</strong> Dashboard</h3>
                </div>
            </div>

            <?php $App->getContainerView(); ?>

        </div>
        
    </main>

    <?php include_once('footer.php'); ?>

</div>