<?php

// shows the info table for modal view

require_once('misc_functions.php');  
require_once('generate_details_table.php');
include('config_selector.php');


$table = new DetailsTable($config, $fields, $_REQUEST['id']);
$table = $table->renderHTML();

?>
	 <div class="modal-header modal-info">
	    <span style="float: right;">
		<button type="button" class="btn btn-default" onclick="printSelection('printable');" ><span class="glyphicon glyphicon-print"></span><span class="sr-only">Natisni</span></button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Zapri</button>
	    </span>
	    <h4 class="modal-title" id="podrobnosti-modal-label">Podrobnosti o izpisu</h4>
	 </div>
         <div class="modal-body" id="printable">
              <?php echo $table;?>
         </div>
	  
<script type="text/javascript">   
    //print specific part of webpage
    //http://stackoverflow.com/a/12997207/822644
    printSelection = function(id) {
	var prtContent = document.getElementById(id);
	var WinPrint = window.open('', '', 'letf=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
	WinPrint.document.write(prtContent.innerHTML);
	WinPrint.document.close();
	WinPrint.focus();
	WinPrint.print();
	WinPrint.close();
    }
</script>
