<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'on');

require_once('config.php');  

require_once('../engine/misc_functions.php');  
require_once('../engine/generate_details_table.php');  

$searchcriteria = "";
$readablecriteria = "Iskalni kriteriji:<ul>";
$id = "";


$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']);
$mysqli->query("SET NAMES 'utf8' COLLATE 'utf8_slovenian_ci'");


foreach ($_REQUEST as $key => $value)
    if ($value[0] != NULL) {
      $searchcriteria .= "".$mysqli->real_escape_string($key)." ".$mysqli->real_escape_string($value[1])." '%".$mysqli->real_escape_string($value[0])."%' AND ";
      $readablecriteria .= "<li> Polje \"".$key."\" ".humanReadableOperator($value[1])." \"".$value[0]."\"</li>";
    }
    
$searchcriteria = substr($searchcriteria,0,-4);
$readablecriteria .= "</ul>";

if ($searchcriteria == null) {
    $id = $_GET['id'];
    if($id != null) {
        $searchcriteria = "id = $id";
        $readablecriteria = "Vnos z identifikacijsko številko $id";
    } else {
        echo "Napaka: Niste podali nobenih iskalnih zahtev";
        return;
    }
}


$tn = $config['db_table']; 
$query = "SELECT * FROM $tn WHERE ($searchcriteria) ORDER BY $order_key";


$rs=$mysqli->query($query);

if($rs === false) {
    trigger_error('Napačen SQL: ' . $sql . ' Napaka: ' . $mysqli->error, E_USER_ERROR);
} else {
    $rows_returned = $rs->num_rows;
}


include('../engine/get_header.php');

?>

    <div class="container-fluid">
            <div class="row">
                    <?php if ($id == null) { ?>
                        <div class="col-md-12">
                                <div class="page-header">
                                        <h1>Podrobno iskanje po zbirki</h1>
                                </div>
                                <button id="collapse-init" class="btn btn-primary  pull-right">Prikaži vse zadetke</button>
                                <?php 
                                echo $readablecriteria;
                                echo "Število zadetkov: $rows_returned"; ?>
                                <br />
                        </div>
                    <?php } else { echo $readablecriteria; } ?>
            </div>
            <hr /><br />
            <div class="row">
                    <div class="col-md-12 panel-group" id="accordion">
                        <?php $rs->data_seek(0);
                        while($row = $rs->fetch_row()){
                                $table = new DetailsTable($config, $fields, $row[0], $summary);
                                $table = $table->renderNiceHTML();
                                echo $table;
                        } ?>
                    </div>
            </div>
    </div>
    
<?php

include('../engine/get_footer.php');

 
?>

<script src="../engine/js/extras.js"></script>
