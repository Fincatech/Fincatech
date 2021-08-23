<table class="table table-hover my-0 hs-tabla" name="<?php echo $tableId;?>" id="<?php echo $tableId;?>" data-model="<?php echo $model;?>">
    <thead class="thead">
        <?php
            for($x=0;$x<count($params); $x++)
            {
                echo '<th class="table-cell">' . $params[$x] . "</th>";
            }
        ?>
    </thead>
    <tbody class="tbody"></tbody>
</table>