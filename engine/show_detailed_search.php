<?php

include('config_selector.php'); 
require_once('misc_functions.php');  
require_once('generate_details_table.php');  

$table = new DetailsTable($config, $fields, null);
$rows = $table->getRows();

?>

<div class="modal-header modal-info">
    <!--<button type="button" class="btn btn-default pull-right" onclick="$('.additional').toggle('slow');" ><span class="glyphicon glyphicon-menu-down"></span><span class="sr-only">Prikaži/skrij dodatna polja</span></button>-->
    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Zapri</button>
<h4 class="modal-title" id="iskanje-modal-label">Podrobno iskanje po zbirki diplom</h4>
</div>
<div class="modal-body">
    <!-- Form -->
    <form class="advancedsearch" action="results.php" method="POST">
        <?php foreach($rows as $row) searchCriterion2($row); ?>
        <br />
        <input type="submit" class="btn btn-primary btn-default btn-block" value="Išči">
    </form>
</div>

