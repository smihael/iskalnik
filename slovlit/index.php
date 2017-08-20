<?php 

error_reporting(-1);
ini_set('display_errors', 'On');

header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'sl_SI.UTF-8');
error_reporting(E_ALL & ~E_NOTICE);
// setlocale(LC_TIME, 'slovene');
date_default_timezone_set('Europe/Ljubljana');

//CONFIGURATION
$DB_host="localhost";
$DB_user="mihael";
$DB_pass="REDACTED";
$DB_table="mihael_jezik";


$con = mysqli_connect("$DB_host", "$DB_user", "$DB_pass", "$DB_table") or die("Could not connect. " . mysqli_error($con));

function request($q) {
  global $con;
  $q=$_REQUEST[$q];
  return  mysqli_real_escape_string($con,trim($q));
}


require_once("config.php");
include("../engine/get_header.php");

?>

<!-- Filtrirnik -->
<div class="jumbotron">
	<h1><?php echo $info['title']; ?></h1>
	<p><?php echo $info['intro']; ?></p>
	
	<form class="navbar-form" role="search" action="#results" method="get" id="search-form" name="search-form">

		<div class="input-group">   
			<input type="text" class="form-control" placeholder="<?php echo $info['searchhint']; ?>" id="filter" name="q">
			<span class="input-group-btn">
                            <!-- <button type="button" class="btn btn-default hidden-xs keyboard" data-input="Query"><i class="glyphicon glyphicon-keyboard" aria-hidden="true"></i></button> -->
                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
		</div>
		<br />
		<a class="pull-right" data-toggle="modal" data-target="#iskanje-modal" onclick="$('#iskanje-modal #modal-content').load('../engine/show_detailed_search.php?baza=literatura');" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Podrobno iskanje</a>

		Urejenost: 
                    <input type="radio" name="sort" value="priimek" checked /> Priimek avtorja 
                    <input type="radio" name="sort" value="naslov" /> Naslov
                    <input type="radio" name="sort" value="vir" /> Vir
                    <input type="radio" name="sort" value="id"> Zaporedne številke
                    
                <br />

	</form>
	
</div>

<?php
$iskanje=request('q');
if(!empty($iskanje)) {
	mysqli_query($con,"SET NAMES 'utf8' COLLATE 'utf8_slovenian_ci'");
	$order= mysqli_real_escape_string($con,utf8_decode(request('sort')));
	$message = "Prikazujem zadetke za iskanje: »".$iskanje."«, razvrščene po ključu $order";
	//$query = "SELECT id,tip,naslov,vir,ime,priimek,LinkAvtor,LinkDelo,psevdonim FROM literatura WHERE (validity=1 AND (naslov LIKE '%$iskanje%' or priimek LIKE '%$iskanje%' or ime LIKE '%$iskanje%')) ORDER BY $order";
	$query = "SELECT id,tip,naslov,vir,ime,priimek,LinkAvtor,LinkDelo,psevdonim FROM literatura WHERE (naslov LIKE '%$iskanje%' or priimek LIKE '%$iskanje%' or ime LIKE '%$iskanje%') ORDER BY $order";
	$result = mysqli_query($con,$query) or die('Error : ' . mysqli_error($con));
	$stevilo =  mysqli_num_rows($result);

	$results_table .="<table align='center'>\n<thead><th>ID</th><th>Avtor</th><th>Naslov</th><th>Vir</th></thead><tbody>\n";
	while($row =  mysqli_fetch_array($result, MYSQLI_NUM)) {
		list($id,$tip,$naslov,$vir,$ime,$priimek,$LinkAvtor,$LinkDelo,$psevdonim) = $row;
		$imepriimek= str_replace("_","&nbsp;","$ime $priimek"); //psevdonimi!
		if ($LinkAvtor !== "") { $imepriimek = "<a href=\"$LinkAvtor\">$imepriimek</a>"; };
		if ($LinkDelo !== "") { $naslov = "<a href=\"$LinkDelo\">$naslov</a>"; };
		switch ($vir) {
			case 'luin':	$vir = '<a href="http://www.drustvo-para-kp.si/index.php?option=com_content&view=article&id=17&Itemid=23">Zbirka BESeDA na DPIK</a> (Franko Luin)';	break;
			case 'LZ':	$vir = '<a href="http://sl.wikipedia.org/wiki/Ljubljanski_zvon">Ljubljanski zvon</a>';	break;
			case 'dLib':	$vir = '<a href="http://www.dlib.si">Digitalna knjižnica Slovenije</a>';	break;
			case 'MH':	$vir = '<a href="http://lit.ijs.si/leposl.html">Zbirka leposlovnih besedil</a> (Miran Hladnik)';	break;
			case 'PJ':	$vir = '<a href="http://bos.zrc-sazu.si/s_nova_beseda.html#a">Leposlovna besedila v korpusu Nova beseda</a> (Primož Jakopin)';	break;
			case 'IMP':	$vir = '<a href="http://nl.ijs.si/imp/dl/index-date.html">Projekt IMP</a> (IJS)';	break;
			case 'wikivir':	$vir = '<a href="http://sl.wikisource.org/wiki/Glavna_stran">Wikivir</a>';	break;
		}
		$results_table .= "<tr><td>$id</td><td>$imepriimek</td><td>$naslov</td><td>$vir</td></tr>\r\n";
	}
	$results_table .='</tbody></table>';
} 


if(!empty($iskanje)) {
?>

<h1 id="results">Iskalni zadetki</h1>
<div class="content" >
	<!-- Feedback message zone -->
	<div class="alert alert-info" id="message"><?php echo $message ?></div>
	
	<!-- Grid contents -->
	<div class="panel panel-default">
                <?php echo $results_table; ?>
	</div>

</div>

<?php } ?>

<!-- Podrobno iskanje -->
<div class="modal fade" id="iskanje-modal" tabindex="-1" role="dialog" aria-labelledby="iskanje-modal-label" aria-hidden="true">
    <div class="modal-dialog">    
    <div class="modal-content">
        <div id="modal-content">
        <div class="modal-header modal-info">
            <h4 class="modal-title" id="iskanje-modal-title">Podrobnosti o iskanju</h4>
        </div>
        <div class="modal-body" id="printable">
            <div id="loading">Iskalna maska se nalaga ... To lahko traja nekaj trenutkov.</div>
        </div>
        </div>
    </div>
    </div>
</div>


<?php

include '../engine/get_footer.php';

?>
