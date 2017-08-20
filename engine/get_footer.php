
<!-- O projektu -->
<div class="page-header" id="about">
	<h1>O projektu</h1>
	<p><?php echo $info['about']; ?></p>
</div>

<div class="well">
	<?php
	$today=date('j. n. Y');
	$today2=date('G:i');
	$url=selfURL();
	$footer="\n<small>";
	$footer .="\nPrikaz je bil ustvarjen $today ob $today2 prek <a href=\"$url\">$url</a>.<br/>";
	if (array_key_exists('credits', $info)) {
        $footer .= $info['credits']; }
    else {
        $footer .="\nZbirko je pripravil <a href=\"http://lit.ijs.si/hladnik.html\">Miran Hladnik</a>, spletno postavitev ";
        $footer .="pa <a href=\"http://www.simonic.si\">Mihael Simonič</a>.<br />";
	}
	//$st = $info['n_entries'];
	//$lu = $info['last_update'];
	//$footer .="\nZbirka vsebuje $st. Zadnjič posodobljeno: $lu<br /><br />";
	//$footer .="\n<a href=\"index.php\">nova poizvedba</a> | <a href=\"podrobno\">podrobno iskanje</a> | <a href=\"../../baze\">uredi bazo</a>";
	$footer .="\n</small>";

	echo $footer;
	?>

</div>



</div> <!-- /container -->

<!-- Bootstrap core JavaScript
================================================== -->

<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="../engine/js/jquery-1.10.2.min.js"></script>
<script src="../engine/js/jquery-ui.min.js"></script>

<script src="../engine/js/bootstrap.min.js"></script>
<script src="../engine/js/editablegrid-2.0.1.js"></script>
<link rel="stylesheet" href="../engine/css/editablegrid-2.0.1.css" type="text/css" media="screen">

</body>
</html>
