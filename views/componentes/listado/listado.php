<div class="row">

    <div class="col-12 d-flex">

        <div class="card flex-fill card-principal">

            <div class="card-header pl-0 headerListado">

                <div class="row">

                    <div class="col-12 col-md-9">
                        <h5 class="card-title mb-0 text-uppercase font-weight-normal pl-3 pt-1"><?php echo $App->getController(); ?></h5>
                    </div>
<?php

    if($App->getController() != 'dpd'): ?>
                    <div class="col-12 col-md-3 text-right">
                        <a href="<?php echo HOME_URL . $App->getController() . "/add" ?>" class="btn btn-outline-secondary text-uppercase rounded-pill shadow pl-2 pr-4"><i class="bi bi-plus-circle pr-3"></i> AÑADIR <?php echo strtoupper($App->getController() ); ?></a>
                    </div>
    <?php endif; ?>
    
                </div>

            </div>
            
            <div class="listado pl-3 pr-3 pb-3">

                <table class="table table-bordered table-hover my-0 hs-tabla" name="<?php echo $tableId;?>" id="<?php echo $tableId;?>" data-model="<?php echo $model;?>">
                    <thead class="thead"></thead>
                    <tbody class="tbody"></tbody>
                </table>

            </div>

        </div>

    </div>

</div>