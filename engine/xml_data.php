<?php

// Returns XML with search results

require_once("includes/EditableGrid.php");
include("config_selector.php");

$grid = new EditableGrid();

$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 
$mysqli->query("SET NAMES 'utf8' COLLATE 'utf8_slovenian_ci'");


//$subtable = "";

$grid->addColumn("action", "", "html", NULL, false);

foreach($summary as $column)
    $grid->addColumn($column, $fields[$column][0], 'string', NULL, false); 
   // $subtable .= "$column,";
   
//$subtable_string = substr($subtable,0,-1);

//$subtable_string

// $grid->addColumn('id', 'ZŠ', 'integer', NULL, false); 
// $grid->addColumn('signatura', 'Signatura', 'string', NULL, false);  
// $grid->addColumn('avtor', 'Avtor', 'string', NULL, false);  
// $grid->addColumn('naslov', 'Naslov', 'string', NULL, false); 

$table = $config['db_table'];
//$data = $mysqli->query("SELECT id,$subtable_string FROM $table");   // iz cudnega razloga potem strajka xml pregled
$data = $mysqli->query("SELECT * FROM $table");
$mysqli->close();


// render XML or JSON
//if (isset($_GET['json'])) $grid->renderJSON($data);
//else 
$grid->renderXML($data);

?>
