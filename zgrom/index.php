<?php

require_once("config.php");

include '../engine/get_header.php';
include '../engine/get_front_page.php';
include '../engine/get_footer.php';

?>

<script id="baza" data-name="zgrom" src="../engine/js/load_grid.js"></script>
<script type="text/javascript">
	window.onload = function() { 
		editableGrid.onloadXML("../engine/xml_data.php?baza=zgrom");
	};
</script>
