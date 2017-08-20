<?php

// Podatki o bazi
$info = array(
	"title" => "Zbirka študijskih nalog",
	"description" => "Iskalnik po podatkovni zbirki",
	"about" => "",
	"intro" => "Na tej strani so zbrane študijske naloge iz področja slavistike od leta 1948 dalje. Vključene so (nekatere starejše) seminarske naloge iz slovenske književnosti, diplomske naloge iz slovenskega jezikoslovja ter iz didaktike slovenskega jezika in književnosti in bolonjske diplomske naloge iz slovenske književnosti ter iz slovenskega jezikoslovja.",
	"searchhint" => "Filtriraj po naslovu, avtorju, letnici , ...",
	"last_update" => "",
	"n_entries" => "",
	"base_url" => "http://www.slov.si/iskalniki/diplomske/"
);


// Povezava do baze
$config = array(
            "db_name" => "mihael_jezik",
            "db_user" => "mihael",
            "db_password" => "REDACTED",
            "db_host" => "localhost",
            "db_table" => "diplome"
        );                

// Vsa polja
$fields = array(
            // kljuc v podatkovni zbirki =>  (ime polja, tip polja, ikona polja)
            "id" => array('ID v skupni zbirki', 'string'),
            'signatura' => array('Signatura', 'string'),
            'avtor' => array('Avtor', 'clickable', 'glyphicon-user'),
            'naslov' => array('Naslov', 'string', 'glyphicon-book'),
            'kraj' => array('Kraj', 'string'),
            'leto' => array('Letnica izdaje', 'clickable', 'glyphicon-calendar'),
            'strani' => array('Strani', 'string'),
            'vrsta' => array('Vrsta','string'),
            'podrocje' => array('Področje', 'string'),
            'mentor' => array('Mentor', 'clickable'),
            'osebe' => array('Obravnavani avtorji', 'list'),
            'dela' => array('Obravnavana dela', 'list'),
            'kljucne' => array('Ključne besede', 'list'),
            'priloge' => array('Priloge', 'string'),		
            'cobiss' => array('COBISS', 'cobiss'),
            'zbirka' => array('Zbirka iz katere je vnos prenešen', 'string'),
            'celotno_besedilo' => array('Celotno besedilo', 'string')
        );
        
// Polja, ki so prikazana v tabeli
$summary = array('avtor', 'naslov', 'leto');

// Privzeti ključ za razvrščanje
$order_key = "id";


// Tehnikalije
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

?>
