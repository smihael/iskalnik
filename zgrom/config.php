<?php

// Podatki o bazi
$info = array(
	"title" => "Slovenski zgodovinski roman",
	"description" => "Iskalnik po podatkovni zbirki",
	"intro" => "",
	"about" => "",
	"searchhint" => "Filtriraj po naslovu, avtorju, letnici , ...",
 	"last_update" => "",
 	"n_entries" => "",
	"base_url" => "http://www.slov.si/iskalniki/zgrom/"
);


// Povezava do baze
$config = array(
            "db_name" => "mihael_jezik",
            "db_user" => "mihael",
            "db_password" => "REDACTED",
            "db_host" => "localhost",
            "db_table" => "zgrom"
        );                

// Vsa polja
$fields = array(
            // kljuc v podatkovni zbirki =>  (ime polja, tip polja, ikona polja)
            	'id' => array('Zaporedna številka', 'integer'),
		'av' => array('Avtor', 'clickable', 'glyphicon-user'),
		'ti' => array('Naslov', 'string', 'glyphicon-book'),
		'ye' => array('Obdobje izdaje', 'clickable', 'glyphicon-calendar'),
                'pn' => array('Podnaslov', 'string'),
		'kr' => array('Kraj izdaje','clickable'),
		'za' => array('Založba','clickable'),
		're' => array('Ponatisi, prevodi', 'string'),
		'ps' => array('Psevdonim avtorja', 'string'),
		'si' => array('Signatura v knjižnici', 'string'),
		'sb' => array('Dolžina v besedah', 'integer'),
		'oc' => array('Ocene, kritike', 'string'),
		'oz' => array('Oznaka', 'string'),
		'zt' => array('Žanrski tip', 'list'),
		'c1' => array('Čas dogajanja', 'string'),
		'c2' => array('Obdobje', 'string'),
		'c3' => array('Natančnejši popis časa', 'string'),
		'zd' => array('Zgodovinsko dogajanje', 'string'),
		'zo' => array('Zgodovinske osebe', 'list'),
		'sp' => array('Prostor', 'list'),
		'mp' => array('Dogajališče', 'string'),
		'pp' => array('Pripovedovalec', 'string'),
		'mo' => array('Motivi', 'list'),
		'km' => array('Kompozicija', 'string'),
		'ko' => array('Število in konstelacija oseb', 'string'),
		'kc' => array('Konec', 'string'),
		'mt' => array('Motivacija', 'string'),
		'te' => array('Tema', 'string'),
		'ide' => array('Ideja', 'string'),
		'zg' => array('Zgodba', 'string'),
		'ci' => array('Citati iz dela', 'string'),
		'op' => array('Opombe ob delu', 'string'),
		'link' => array('Povezava', 'url')
        );
        
// Polja, ki so prikazana v tabeli
$summary = array('av', 'ti', 'ye');

// Privzeti ključ za razvrščanje
$order_key = "id";


// Tehnikalije
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

?>
