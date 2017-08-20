<?php

require_once("misc_functions.php");
//require_once("config_selector.php");

?>
<!DOCTYPE html>
<html lang="sl">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="../engine/ico/favicon.ico">

<title><?php echo $info['title']; ?></title>

<!-- Bootstrap core CSS -->
<link href="../engine/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap theme -->
<link href="../engine/css/bootstrap-theme.min.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="../engine/css/style.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

</head>

<body role="document">

<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
<div class="container">
	<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	<span class="sr-only">Toggle navigation</span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	</button>
	<a class="navbar-brand" href="<?php echo $info['base_url']; ?>"><?php echo $info['description']; ?></a>
	</div>
	<div class="navbar-collapse collapse">
	<ul class="nav navbar-nav pull-right">
	<li><a href="#about">O projektu</a></li>
        <!-- <li><a href="#contact">Kontakt</a></li> -->
        <li class="dropdown pull-right">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Povezave <b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="http://www.slov.si/">Oddelek za slovenistiko FF Univerze v Ljubljani</a></li>
			<li class="divider"></li>
			<li><a href="http://www.slov.si/iskalniki/slovlit">Slovensko leposlovje na spletu</a></li>
			<li><a href="http://www.slov.si/iskalniki/kmpov">Slovenska kmečka povest</a></li>
			<li><a href="http://www.slov.si/iskalniki/zgrom">Slovenski zgodovinski roman</a></li>
			<li><a href="http://www.slov.si/iskalniki/diplomske">Zbirka diplomskih in seminarskih nalog</a></li>
			<li><a href="http://www.slov.si/iskalniki/upor">Pesmi slovenskega narodnoosvobodilnega boja</a></li>
			<li class="divider"></li>
			<li><a href="admin">Urejevalnik baze</a></li>
		</ul>
	</li>
	</ul>
	</div><!--/.nav-collapse -->
</div>
</div>

<br clear="all" />
<br clear="all" />

<div class="container theme-showcase" role="main">    
