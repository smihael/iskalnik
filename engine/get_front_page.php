<!-- Filtrirnik -->
<div class="jumbotron">
	<h1><?php echo $info['title']; ?></h1>
	<p><?php echo $info['intro']; ?></p>
	
	<form class="navbar-form" role="search" action="" method="" id="search-form" name="search-form">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="<?php echo $info['searchhint']; ?>" id="filter" name="query">
			<span class="input-group-btn">
                            <!-- <button type="button" class="btn btn-default hidden-xs keyboard" data-input="Query"><i class="glyphicon glyphicon-keyboard" aria-hidden="true"></i></button> -->
                            <button type="button" class="btn btn-disabled"><span class="glyphicon glyphicon-filter"></span></button>
                            <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#iskanje-modal" 
                                    onclick="$('#iskanje-modal #modal-content').load('../engine/show_detailed_search.php?baza=<?php echo $config['db_table']; ?>');"                            
                            ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Podrobno iskanje</button>-->
                        </span>
		</div>
		<br />
                            <a class="pull-right" data-toggle="modal" data-target="#iskanje-modal" onclick="$('#iskanje-modal #modal-content').load('../engine/show_detailed_search.php?baza=<?php echo $config['db_table']; ?>');" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Podrobno iskanje</a>
	</form>
	
</div>

<div class="content">
	<!-- Feedback message zone -->
	<div class="alert alert-info" id="message">Podatkovna zbirka se nalaga ... To lahko traja nekaj trenutkov. Preverite ali imate vklopljen JavaScript.</div>
	
	<!-- Grid contents -->
	<div class="panel panel-default">
		<!-- Table -->
		<div id="tablecontent"></div>
	</div>

	<!-- Paginator control -->
	<ul class="pagination" id="paginator"></ul>
</div>
   
<!-- Detailed view -->
<div class="modal fade" id="podrobnosti-modal" tabindex="-1" role="dialog" aria-labelledby="podrobnosti-modal-label" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div id="modal-content">
        <div class="modal-header modal-info">
            <h4 class="modal-title" id="podrobnosti-modal-title">Podrobnosti o izpisu</h4>
        </div>
        <div class="modal-body" id="printable">
            <div id="loading">Podrobnosti se nalagajo ... To lahko traja nekaj trenutkov.</div>
        </div>
        </div>
    </div>
    </div>
</div>

<!-- Podrobno iskanje -->
<div class="modal fade" id="iskanje-modal" tabindex="-1" role="dialog" aria-labelledby="iskanje-modal-label" aria-hidden="true">
    <div class="modal-dialog">    
    <div class="modal-content">
        <div id="modal-content">
        <div class="modal-header modal-info">
            <h4 class="modal-title" id="iskanje-modal-title">Podrobno iskanje</h4>
        </div>
        <div class="modal-body" id="printable">
            <div id="loading">Iskalna maska se nalaga ... To lahko traja nekaj trenutkov.</div>
        </div>
        </div>
    </div>
    </div>
</div>


<style>

/* http://embed.plnkr.co/P1FYFl/preview */
.modal-info {
        background-image: -webkit-gradient(linear, 0 100%, 0 0, from(#d9edf7), to(#b9def0));
        background-image: -webkit-linear-gradient(#d9edf7 0%, #b9def0 100%);
        background-image: -moz-linear-gradient(#d9edf7 0%, #b9def0 100%);
        background-image: -o-linear-gradient(#d9edf7 0%, #b9def0 100%);
        background-image: linear-gradient(#d9edf7 0%, #b9def0 100%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffd9edf7', endColorstr='#ffb9def0', GradientType=0);
        border-color: #9acfea;
        border-radius: 6px 6px 0 0;
}
</style>
